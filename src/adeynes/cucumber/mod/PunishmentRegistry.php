<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\Queries;
use pocketmine\Player;
use pocketmine\utils\Config;
use poggit\libasynql\DataConnector;

final class PunishmentRegistry
{

    /** @var Config */
    private $message_config;

    /** @var Ban[] */
    private $bans = [];

    /** @var IpBan[] */
    private $ip_bans = [];

    /** @var UBan[] */
    private $ubans = [];

    /** @var UBanChecker */
    private $uban_checker;

    /** @var Mute[] */
    private $mutes = [];

    public function __construct(Config $message_config, DataConnector $connector)
    {
        $this->message_config = $message_config;
        $this->uban_checker = new UBanChecker($this, $connector);
        $this->load($connector, true);
    }

    public function getUBanChecker(): UBanChecker
    {
        return $this->uban_checker;
    }

    private function getRawErrorMessage(string $path): string
    {
        return $this->message_config->getNested("error.$path");
    }

    public function load(DataConnector $connector, bool $blocking = false): void
    {
        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_BANS_CURRENT,
            [],
            function (array $rows) {
                $this->bans = [];
                foreach ($rows as $row) {
                    $this->bans[$row['player_name']] = Ban::from($row);
                }
            }
        );

        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_IP_BANS_CURRENT,
            [],
            function (array $rows) {
                $this->ip_bans = [];
                foreach ($rows as $row) {
                    $this->ip_bans[$row['ip']] = IpBan::from($row);
                }
            }
        );

        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_UBANS,
            [],
            function(array $rows) {
                $this->ubans = [];
                foreach ($rows as $row) {
                    $this->ubans[$row['ip']] = UBan::from($row);
                }
            }
        );

        $connector->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_MUTES_CURRENT,
            [],
            function (array $rows) {
                $this->mutes = [];
                foreach ($rows as $row) {
                    $this->mutes[$row['player_name']] = Mute::from($row);
                }
            }
        );

        if ($blocking) {
            $connector->waitAll();
        }
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
     * @param Ban $ban
     * @param bool $override
     * @throws CucumberException If the player is already banned
     */
    public function addBan(Ban $ban, bool $override = false): void
    {
        $player = $ban->getPlayer();
        if ($this->getBan($player) && !$override) {
            throw new CucumberException($this->getRawErrorMessage('ban.already-banned'), ['player' => $player]);
        }

        $this->bans[$player] = $ban;
    }

    /**
     * @param string $player
     * @throws CucumberException If the player is not banned
     */
    public function removeBan(string $player): void
    {
        if (!$this->getBan($player)) {
            throw new CucumberException($this->getRawErrorMessage('ban.not-banned'), ['player' => $player]);
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
     * @param IpBan $ip_ban
     * @param bool $override
     * @throws CucumberException If the IP is already banned
     */
    public function addIpBan(IpBan $ip_ban, bool $override = false): void
    {
        $ip = $ip_ban->getIp();
        if ($this->getIpBan($ip) && !$override) {
            throw new CucumberException($this->getRawErrorMessage('ipban.already-banned'), ['ip' => $ip]);
        }

        $this->ip_bans[$ip] = $ip_ban;
    }

    /**
     * @param string $ip
     * @throws CucumberException If the IP is not banned
     */
    public function removeIpBan(string $ip): void
    {
        if (!$this->getIpBan($ip)) {
            throw new CucumberException($this->getRawErrorMessage('ipban.not-banned'), ['ip' => $ip]);
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
     * @param UBan $uban
     * @param bool $override
     * @throws CucumberException If the IP is already ubanned
     */
    public function addUBan(UBan $uban, bool $override = false): void
    {
        $ip = $uban->getIp();
        if ($this->getUBan($ip) && !$override) {
            throw new CucumberException($this->getRawErrorMessage('uban.already-banned'), ['ip' => $ip]);
        }

        $this->ubans[$ip] = $uban;
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
     * @param Mute $mute
     * @param bool $override
     * @throws CucumberException If the player is already muted
     */
    public function addMute(Mute $mute, bool $override = false): void
    {
        $player = $mute->getPlayer();
        if ($this->getMute($player) && !$override) {
            throw new CucumberException($this->getRawErrorMessage('mute.already-muted'), ['player' => $player]);
        }

        $this->mutes[$player] = $mute;
    }

    /**
     * @param string $player
     * @throws CucumberException If the player is not muted
     */
    public function removeMute(string $player): void
    {
        if ($this->getMute($player) === null) {
            throw new CucumberException($this->getRawErrorMessage('mute.not-muted'), ['player' => $player]);
        }

        unset($this->mutes[$player]);
    }

    public function isBanned(Player $player, ?Punishment &$punishment = null, bool $remove_if_expired = true): bool
    {
        $name = $player->getLowerCaseName();
        if ($ban = $this->getBan($name)) {
            $punishment = $ban;
            if ($ban->isExpired()) {
                if ($remove_if_expired) {
                    /** @ign */
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $this->removeBan($name);
                }
                return false;
            }
            return true;
        }

        $ip = $player->getAddress();
        if ($ip_ban = $this->getIpBan($ip)) {
            $punishment = $ip_ban;
            if ($ip_ban->isExpired()) {
                if ($remove_if_expired) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $this->removeIpBan($ip);
                }
                return false;
            }
            return true;
        }

        return false;
    }

    public function isMuted(Player $player, ?Punishment &$punishment = null, bool $remove_if_expired = true): bool
    {
        $name = $player->getLowerCaseName();
        if ($mute = $this->getMute($name)) {
            $punishment = $mute;
            if ($mute->isExpired()) {
                if ($remove_if_expired) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $this->removeMute($name);
                }
                return false;
            }
            return true;
        }

        return false;
    }

}