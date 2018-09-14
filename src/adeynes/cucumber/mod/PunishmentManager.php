<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\Queries;
use pocketmine\Player;

// TODO: Punishment dates
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

        /*
        $this->not_saved = ['ban' => [], 'ip-ban' => [], 'uban' => [], 'mute' => []];
        $this->not_deleted = ['ban' => [], 'ip-ban' => [], 'mute' => []];
        */
    }

    /*
    public function save(): void
    {
        // TODO: bulk DELETE

        $connector = $this->getPlugin()->getConnector();
        $queries = ['ban' => Queries::CUCUMBER_PUNISH_BAN, 'mute' => Queries::CUCUMBER_PUNISH_MUTE];

        foreach ($queries as $type => $query) {
            foreach ($this->not_saved[$type] as $name => $punishment) {
                $connector->executeInsert(
                    $query,
                    ['name' => $name] + $punishment->getData());
                unset($this->not_saved[$type][$name]);
            }
        }

        foreach ($this->not_saved['ip-ban'] as $ip => $ip_ban) {
            $connector->executeInsert(
                Queries::CUCUMBER_PUNISH_IP_BAN,
                ['ip' => $ip] + $ip_ban->getData()
            );
            unset($this->not_saved['ip-ban'][$ip]);
        }

        foreach ($this->not_saved['uban'] as $ip => $uban) {
            $data = $uban->getData();
            unset($data['expiration']);
            $connector->executeInsert(Queries::CUCUMBER_PUNISH_UBAN, ['ip' => $ip] + $data);
            unset($this->not_saved['uban'][$ip]);
        }

        $queries = ['ban' => Queries::CUCUMBER_PUNISH_UNBAN, 'mute' => Queries::CUCUMBER_PUNISH_UNMUTE];

        foreach ($queries as $type => $query) {
            foreach ($this->not_deleted[$type] as $i => $name) {
                $connector->executeChange($query, ['name' => $name]);
                unset($this->not_deleted[$type][$i]);
            }
        }

        foreach ($this->not_deleted['ip-ban'] as $i => $ip) {
            $connector->executeChange(Queries::CUCUMBER_PUNISH_IP_UNBAN, ['ip' => $ip]);
            unset($this->not_deleted['ip-ban'][$i]);
        }
    }
    */

    /*
    private function punish($id, Punishment $punishment, string $type, array &$storage, CucumberException $exception,
                            bool $override = false): Punishment
    {
        if (isset($storage[$id]) && !$override) throw $exception;

        $storage[$id] = $punishment;
        $this->not_saved[$type][$id] = $punishment;

        return $punishment;
    }
    */

    /*
    private function pardon($id, string $type, array &$storage, CucumberException $exception): void
    {
        if (!isset($storage[$id])) throw $exception;

        unset($storage[$id]);
        $this->not_deleted[$type][] = $id;
    }
    */

    /*
    private function playerPunish(string $name, string $reason, int $expiration, string $moderator, string $type,
                                  array &$storage, string $error_message, bool $override = false): SimplePunishment
    {
        return $this->punish(
            $name,
            new SimplePunishment($reason, $expiration, $moderator),
            $type,
            $storage,
            new CucumberException(
                $error_message,
                ['player' => $name],
                ErrorCodes::ATTEMPT_PUNISH_PUNISHED
            ),
            $override
        );
    }
    */

    /*
    private function playerPardon(string $name, string $type, array &$storage, string $error_message): void
    {
        $this->pardon(
            $name,
            $type,
            $storage,
            new CucumberException(
                $error_message,
                ['player' => $name],
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            )
        );
    }
    */


    /**
     * @return Ban[]
     */
    public function getBans(): array
    {
        return $this->bans;
    }

    public function getBan(string $name): ?Ban
    {
        return $this->getBans()[$name] ?? null;
    }

    /**
     * @param string $name
     * @param string|null $reason
     * @param int|null $expiration Defaults to +10 years
     * @param string $moderator
     * @param bool $override When set to true, replaces an old ban with a new one and doesn't throw
     * @return Ban
     * @throws CucumberException If the player is already banned
     */
    public function ban(string $name, ?string $reason, ?int $expiration, string $moderator, bool $override = false): Ban
    {
        if (is_null($reason)) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }
        if (is_null($expiration)) {
            $expiration = 0x7FFFFFFF;
        }

        if ($this->getBan($name) && !$override) {
            throw new CucumberException($this->messages['ban']['already-banned'], ['player' => $name]);
        }

        $ban = new Ban($name, $reason, $expiration, $moderator);
        $this->bans[$name] = $ban;
        $this->getPlugin()->getConnector()->executeInsert(Queries::CUCUMBER_PUNISH_BAN, $ban->getData());

        return $ban;
    }

    /**
     * @param string $name
     * @throws CucumberException If the player is not banned
     */
    public function unban(string $name): void
    {
        if (!$this->getBan($name)) {
            throw new CucumberException($this->messages['ban']['not-banned'], ['player' => $name]);
        }

        unset($this->bans[$name]);
        $this->getPlugin()->getConnector()->executeChange(Queries::CUCUMBER_PUNISH_UNBAN, ['name' => $name]);
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

        $ip_ban = new IpBan($ip, $reason, $expiration, $moderator);
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
        $this->getPlugin()->getConnector()->executeChange(Queries::CUCUMBER_PUNISH_IP_UNBAN, ['ip' => $ip]);
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

        $uban = new UBan($ip, $reason, 0x7FFFFFFF, $moderator);
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

    public function getMute(string $name): ?Mute
    {
        return $this->getMutes()[$name] ?? null;
    }

    /**
     * @param string $name
     * @param string|null $reason
     * @param int|null $expiration
     * @param string $moderator
     * @return Mute
     * @throws CucumberException If the player is already muted
     */
    public function mute(string $name, ?string $reason, ?int $expiration, string $moderator): Mute
    {
        if (is_null($reason)) {
            $reason = $this->getPlugin()->getMessage('moderation.mute.mute.default-reason');
        }
        if (is_null($expiration)) {
            $expiration = 0x7FFFFFFF;
        }

        if ($this->getMute($name)) {
            throw new CucumberException($this->messages['mute']['already-muted'], ['player' => $name]);
        }

        $mute = new Mute($name, $reason, $expiration, $moderator);
        $this->mutes[$name] = $mute;
        $this->getPlugin()->getConnector()->executeInsert(Queries::CUCUMBER_PUNISH_MUTE, $mute->getData());

        return $mute;
    }

    /**
     * @param string $name
     * @throws CucumberException If the player is not muted
     */
    public function unmute(string $name): void
    {
        if (!$this->getMute($name)) {
            throw new CucumberException($this->messages['mute']['not-muted'], ['player' => $name]);
        }

        unset($this->mutes[$name]);
        $this->getPlugin()->getConnector()->executeChange(Queries::CUCUMBER_PUNISH_UNMUTE, ['name' => $name]);
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