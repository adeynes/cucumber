<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\Queries;
use pocketmine\Player;

final class PunishmentManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var string[][] */
    private $messages;

    /** @var Ban[] */
    private $bans = [];

    /** @var IpBan[] */
    private $ip_bans = [];

    /** @var UBan[] */
    private $ubans = [];

    /** @var Mute[] */
    private $mutes = [];

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->initMessages();
        $this->load();
    }

    public function close(): void
    {
        //$this->save();
        //$this->getPlugin()->getConnector()->waitAll(); // don't close until we're done saving
    }
    
    public function getPlugin(): Cucumber
    {
        return $this->plugin;
    }

    private function initMessages(): void
    {
        // TODO: config messages
        
        $this->messages = [
            'ban' => [
                'already-banned' => '%player% is already banned!',
                'not-banned' => '%player% is not banned!'
            ],
            'ip-ban' => [
                'already-banned' => 'IP %ip% is already banned!',
                'not-banned' => 'IP %ip% is not banned!',
            ],
            'uban' => [
                'already-banned' => 'IP %ip% is already banned!'
            ],
            'mute' => [
                'already-muted' => '%player% is already muted!',
                'not-muted' => '%player% is not muted!'
            ]
        ];
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    private function load(): void
    {
        // I could do all punishments with one foreach but it gets gross

        $connector = $this->getPlugin()->getConnector();

        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_BANS_CURRENT,
            [],
            function (array $rows) {
                foreach ($rows as $row) {
                    $this->bans[$row['player_name']] = Ban::from($row);
                }
            }
        );

        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_MUTES_CURRENT,
            [],
            function (array $rows) {
                foreach ($rows as $row) {
                    $this->mutes[$row['player_name']] = Mute::from($row);
                }
            }
        );

        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_IP_BANS_CURRENT,
            [],
            function (array $rows) {
                foreach ($rows as $row) {
                    $this->ip_bans[$row['ip']] = IpBan::from($row);
                }
            }
        );

        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_UBANS,
            [],
            function(array $rows) {
                $expiration = 0x7FFFFFFF;
                foreach ($rows as $row) {
                    $row = $row + ['expiration' => $expiration];
                    $this->ubans[$row['ip']] = UBan::from($row);
                }
            }
        );
        
        $connector->waitAll(); // don't go on until everything is loaded
    }

    /**
     * @return Ban[]
     */
    public function getBans(): array
    {
        return $this->bans;
    }

    public function getBan(string $player): ?Ban
    {
        return $this->getBans()[$player] ?? null;
    }

    /**
     * @param string $player
     * @param string|null $reason
     * @param int|null $expiration Defaults to +10 years
     * @param string $moderator
     * @param bool $override When set to true, replaces an old ban with a new one and doesn't throw
     * @return Ban
     * @throws CucumberException If the player is already banned
     */
    public function ban(string $player, ?string $reason, ?int $expiration, string $moderator, bool $override = false): Ban
    {
        if (is_null($reason)) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }
        if (is_null($expiration)) {
            $expiration = 0x7FFFFFFF;
        }

        if ($this->getBan($player) && !$override) {
            throw new CucumberException($this->messages['ban']['already-banned'], ['player' => $player]);
        }

        $ban = new Ban($player, $reason, $expiration, $moderator, time());
        $this->bans[$player] = $ban;
        $this->getPlugin()->getConnector()->executeInsert(Queries::CUCUMBER_PUNISH_BAN, $ban->getData());

        return $ban;
    }

    /**
     * @param string $player
     * @throws CucumberException If the player is not banned
     */
    public function unban(string $player): void
    {
        if (!$this->getBan($player)) {
            throw new CucumberException($this->messages['ban']['not-banned'], ['player' => $player]);
        }

        unset($this->bans[$player]);
    }

    /**
     * @return IpBan[]
     */
    public function getIpBans(): array
    {
        return $this->ip_bans;
    }

    public function getIpBan(string $ip): ?IpBan
    {
        return $this->getIpBans()[$ip] ?? null;
    }

    /**
     * @param string $ip
     * @param string|null $reason
     * @param int|null $expiration
     * @param string $moderator
     * @return IpBan
     * @throws CucumberException If the IP is already banned
     */
    public function ipBan(string $ip, ?string $reason, ?int $expiration, string $moderator): IpBan
    {
        if (is_null($reason)) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }
        if (is_null($expiration)) {
            $expiration = 0x7FFFFFFF;
        }

        if ($this->getIpBan($ip)) {
            throw new CucumberException($this->messages['ip-ban']['not-banned'], ['ip' => $ip]);
        }

        $ip_ban = new IpBan($ip, $reason, $expiration, $moderator, time());
        $this->ip_bans[$ip] = $ip_ban;
        $this->getPlugin()->getConnector()->executeInsert(Queries::CUCUMBER_PUNISH_IP_BAN, $ip_ban->getData());

        return $ip_ban;
    }

    /**
     * @param string $ip
     * @throws CucumberException If the IP is not banned
     */
    public function ipUnban(string $ip): void
    {
        if (!$this->getIpBan($ip)) {
            throw new CucumberException($this->messages['ip-ban']['not-banned']);
        }

        unset($this->ip_bans[$ip]);
    }

    /**
     * @return UBan[]
     */
    public function getUBans(): array
    {
        return $this->ubans;
    }

    public function getUBan(string $ip): ?UBan
    {
        return $this->getUBans()[$ip] ?? null;
    }

    /**
     * @param string $ip
     * @param null|string $reason
     * @param string $moderator
     * @return UBan
     * @throws CucumberException If the IP is already ubanned
     */
    public function addUBan(string $ip, ?string $reason, string $moderator): UBan
    {
        if (is_null($reason)) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }

        if ($this->getUBan($ip)) {
            throw new CucumberException($this->messages['uban']['already-banned'], ['ip' => $ip]);
        }

        $uban = new UBan($ip, $reason, 0x7FFFFFFF, $moderator, time());
        $data = $uban->getData();
        unset($data['expiration']);
        $this->ubans[$ip] = $uban;
        $this->getPlugin()->getConnector()->executeInsert(Queries::CUCUMBER_PUNISH_UBAN, $data);

        return $uban;
    }

    /**
     * Checks if a player is affected by a uban. If so, bans them
     * @param Player $player
     * @return bool
     * @throws CucumberException
     */
    public function checkUBan(Player $player): bool
    {
        $uban = $this->getUban($player->getAddress());
        if ($uban) {
            $this->ban($player->getLowerCaseName(), $uban->getReason(), $uban->getExpiration(), $uban->getModerator(), true);
        }

        return (bool) $uban;
    }

    /**
     * @return Mute[]
     */
    public function getMutes(): array
    {
        return $this->mutes;
    }

    public function getMute(string $player): ?Mute
    {
        return $this->getMutes()[$player] ?? null;
    }

    /**
     * @param string $player
     * @param string|null $reason
     * @param int|null $expiration
     * @param string $moderator
     * @return Mute
     * @throws CucumberException If the player is already muted
     */
    public function mute(string $player, ?string $reason, ?int $expiration, string $moderator): Mute
    {
        if (is_null($reason)) {
            $reason = $this->getPlugin()->getMessage('moderation.mute.mute.default-reason');
        }
        if (is_null($expiration)) {
            $expiration = 0x7FFFFFFF;
        }

        if ($this->getMute($player)) {
            throw new CucumberException($this->messages['mute']['already-muted'], ['player' => $player]);
        }

        $mute = new Mute($player, $reason, $expiration, $moderator, time());
        $this->mutes[$player] = $mute;
        $this->getPlugin()->getConnector()->executeInsert(Queries::CUCUMBER_PUNISH_MUTE, $mute->getData());

        return $mute;
    }

    /**
     * @param string $player
     * @throws CucumberException If the player is not muted
     */
    public function unmute(string $player): void
    {
        if (!$this->getMute($player)) {
            throw new CucumberException($this->messages['mute']['not-muted'], ['player' => $player]);
        }

        unset($this->mutes[$player]);
    }

    public function isBanned(Player $player): ?SimplePunishment
    {
        $name = $player->getLowerCaseName();
        if ($ban = $this->getBan($name)) {
            if ($ban->isExpired()) {
                $this->unban($name);
            }
            else return $ban;
        }

        $ip = $player->getAddress();
        if ($ip_ban = $this->getIpBan($ip)) {
            if ($ip_ban->isExpired()) {
                $this->ipUnban($ip);
            }
            else return $ip_ban;
        }

        return null;
    }

    public function isMuted(Player $player): ?SimplePunishment
    {
        $name = $player->getLowerCaseName();
        if ($mute = $this->getMute($name)) {
            if ($mute->isExpired()) {
                $this->unmute($name);
            }
            else return $mute;
        }

        return null;
    }

}