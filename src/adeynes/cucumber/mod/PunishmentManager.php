<?php
declare(strict_types=1);

namespace adeynes\cucumber\mod;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\cucumber\utils\ErrorCodes;
use adeynes\cucumber\utils\Queries;

// TODO: Punishment dates
final class PunishmentManager
{

    /** @var Cucumber */
    private $plugin;

    /** @var string[][] */
    private $messages;

    /** @var SimplePunishment[] */
    private $bans = [];

    /** @var SimplePunishment[] */
    private $ip_bans = [];

    /** @var SimplePunishment[] */
    private $ubans = [];

    /** @var SimplePunishment[] */
    private $mutes = [];

    /**
     * New punishments that have not yet been saved to permanent storage
     * @var SimplePunishment[][]
     */
    private $not_saved = [];

    /**
     * Newly-pardoned punishments that have not
     * yet been deleted from permanent storage
     * This is a list of names & IPs
     * @var string[][]
     */
    private $not_deleted = [];

    public function __construct(Cucumber $plugin)
    {
        $this->plugin = $plugin;
        $this->initMessages();
        $this->load();
    }
    
    public function close(): void
    {
        $this->save();
        $this->getPlugin()->getConnector()->waitAll(); // don't close until we're done saving
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
        $queries = [Queries::CUCUMBER_GET_PUNISHMENTS_BANS, Queries::CUCUMBER_GET_PUNISHMENTS_MUTES];
        $storage = [&$this->bans, &$this->mutes];

        foreach ($queries as $i => $query)
            $connector->executeSelect($query, [],
                function(array $rows) use ($i, $storage) {
                    foreach ($rows as $row)
                        $storage[$i][$row['name']] = SimplePunishment::from($row);
                }
            );

        $connector->executeSelect(Queries::CUCUMBER_GET_PUNISHMENTS_IP_BANS, [],
            function(array $rows) {
                foreach ($rows as $row)
                    $this->ip_bans[$row['ip']] = SimplePunishment::from($row);
            }
        );

        $connector->executeSelect(Queries::CUCUMBER_GET_PUNISHMENTS_UBANS, [],
            function(array $rows) {
                $expiration = strtotime('+10 year');
                foreach ($rows as $row) {
                    $row = $row + ['expiration' => $expiration];
                    $this->ubans[$row['ip']] = SimplePunishment::from($row);
                }
            }
        );
        
        $connector->waitAll(); // don't go on until everything is loaded

        $this->not_saved = ['ban' => [], 'ip-ban' => [], 'uban' => [], 'mute' => []];
        $this->not_deleted = ['ban' => [], 'ip-ban' => [], 'mute' => []];
    }

    public function save(): void
    {
        // TODO: bulk DELETE

        $connector = $this->getPlugin()->getConnector();
        $queries = ['ban' => Queries::CUCUMBER_PUNISH_BAN, 'mute' => Queries::CUCUMBER_PUNISH_MUTE];

        foreach ($queries as $type => $query) {
            foreach ($this->not_saved[$type] as $name => $punishment) {
                $connector->executeInsert($query, ['name' => $name] + $punishment->getData());
                unset($this->not_saved[$type][$name]);
            }
        }

        foreach ($this->not_saved['ip-ban'] as $ip => $ip_ban) {
            $connector->executeInsert(Queries::CUCUMBER_PUNISH_IP_BAN, ['ip' => $ip] + $ip_ban->getData());
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

    /**
     * @param $id
     * @param Punishment $punishment
     * @param string $type
     * @param array $storage
     * @param CucumberException $exception
     * @param bool $override When set to true, replaces an old punishment with a new one and doesn't throw
     * @return SimplePunishment
     * @throws CucumberException If the ID is already punished
     */
    private function punish($id, Punishment $punishment, string $type, array &$storage, CucumberException $exception,
                            bool $override = false): Punishment
    {
        if (isset($storage[$id]) && !$override)
            throw $exception;

        $storage[$id] = $punishment;
        $this->not_saved[$type][$id] = $punishment;

        return $punishment;
    }

    /**
     * @param $id
     * @param string $type
     * @param array $storage
     * @param CucumberException $exception
     * @throws CucumberException If the ID is not punished
     */
    private function pardon($id, string $type, array &$storage, CucumberException $exception): void
    {
        if (!isset($storage[$id]))
            throw $exception;

        unset($storage[$id]);
        $this->not_deleted[$type][] = $id;
    }

    /**
     * @param string $name
     * @param string $reason
     * @param int $expiration
     * @param string $moderator
     * @param string $type
     * @param array $storage
     * @param string $error_message
     * @param bool $override When set to true, replaces an old punishment with a new one and doesn't throw
     * @return SimplePunishment The new punishment
     * @throws CucumberException If the player is already punished
     */
    private function playerPunish(string $name, string $reason, int $expiration, string $moderator, string $type,
                                  array &$storage, string $error_message, bool $override = false): SimplePunishment
    {
        $punishment = new SimplePunishment($reason, $expiration, $moderator);
        return $this->punish($name, $punishment, $type, $storage,
            new CucumberException($error_message, ['player' => $name], ErrorCodes::ATTEMPT_PUNISH_PUNISHED), $override);
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $storage
     * @param string $error_message
     * @throws CucumberException If the player is not punished
     */
    private function playerPardon(string $name, string $type, array &$storage, string $error_message): void
    {
        $this->pardon($name, $type, $storage,
            new CucumberException(
                $error_message,
                ['player' => $name],
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            ));
    }


    /**
     * @return SimplePunishment[]
     */
    public function getBans(): array
    {
        return $this->bans;
    }

    public function getBan(string $name): ?SimplePunishment
    {
        return $this->getBans()[$name] ?? null;
    }

    /**
     * @param string $name
     * @param string|null $reason
     * @param int|null $expiration
     * @param string $moderator
     * @param bool $override When set to true, replaces an old ban with a new one and doesn't throw
     * @return SimplePunishment
     * @throws CucumberException If the player is already banned
     */
    public function ban(string $name, ?string $reason, ?int $expiration, string $moderator, bool $override = false): SimplePunishment
    {
        if (is_null($reason))
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        if (is_null($expiration))
            $expiration = strtotime('+10 year');

        return $this->playerPunish($name, $reason, $expiration, $moderator, 'ban', $this->bans,
            $this->getMessages()['ban']['already-banned'], $override);
    }

    /**
     * @param string $name
     * @throws CucumberException If the player is not banned
     */
    public function unban(string $name): void
    {
        $this->playerPardon($name, 'ban', $this->bans, $this->getMessages()['ban']['not-banned']);
    }

    /**
     * @return SimplePunishment[]
     */
    public function getIpBans(): array
    {
        return $this->ip_bans;
    }

    public function getIpBan(string $ip): ?SimplePunishment
    {
        return $this->getIpBans()[$ip] ?? null;
    }

    /**
     * @param string $ip
     * @param string|null $reason
     * @param int|null $expiration
     * @param string $moderator
     * @return SimplePunishment
     * @throws CucumberException If the IP is already banned
     */
    public function ipBan(string $ip, ?string $reason, ?int $expiration, string $moderator): SimplePunishment
    {
        if (is_null($reason))
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        if (is_null($expiration))
            $expiration = strtotime('+10 year');

        $punishment = new SimplePunishment($reason, $expiration, $moderator);

        return $this->punish($ip, $punishment, 'ip-ban', $this->ip_bans,
            new CucumberException(
                $this->getMessages()['ip-ban']['already-banned'],
                ['ip' => $ip],
                ErrorCodes::ATTEMPT_PUNISH_PUNISHED
            )
        );
    }

    /**
     * @param string $ip
     * @throws CucumberException If the IP is not banned
     */
    public function ipUnban(string $ip): void
    {
        $this->pardon($ip, 'ip-ban', $this->ip_bans,
            new CucumberException(
                $this->getMessages()['ip-ban']['not-banned'],
                ['ip' => $ip],
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            )
        );
    }

    /**
     * @return SimplePunishment[]
     */
    public function getUbans(): array
    {
        return $this->ubans;
    }

    public function getUban(string $ip): ?SimplePunishment
    {
        return $this->getUbans()[$ip] ?? null;
    }

    /**
     * @param string $ip
     * @param null|string $reason
     * @param string $moderator
     * @return SimplePunishment
     * @throws CucumberException If the IP is already ubanned
     */
    public function addUban(string $ip, ?string $reason, string $moderator): SimplePunishment
    {
        if (is_null($reason))
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');

        $punishment = new SimplePunishment($reason, strtotime('+10 year'), $moderator);

        return $this->punish($ip, $punishment, 'uban', $this->ubans,
            new CucumberException(
                $this->getMessages()['uban']['already-banned'],
                ['ip' => $ip],
                ErrorCodes::ATTEMPT_PUNISH_PUNISHED
            )
        );
    }

    /**
     * Checks if a player is affected by a uban. If so, bans them
     * @param CucumberPlayer $player
     * @return bool
     * @throws CucumberException
     */
    public function checkUban(CucumberPlayer $player): bool
    {
        $uban = $this->getUban($player->getIp());
        if ($uban)
            $this->ban($player->getName(), $uban->getReason(), $uban->getExpiration(), $uban->getModerator(), true);

        return (bool) $uban;
    }

    /**
     * @return SimplePunishment[]
     */
    public function getMutes(): array
    {
        return $this->mutes;
    }

    public function getMute(string $name): ?SimplePunishment
    {
        return $this->getMutes()[$name] ?? null;
    }

    /**
     * @param string $name
     * @param string|null $reason
     * @param int|null $expiration
     * @param string $moderator
     * @param bool $override When set to true, replaces an old mute with a new one and doesn't throw
     * @return SimplePunishment
     * @throws CucumberException If the player is already muted
     */
    public function mute(string $name, ?string $reason, ?int $expiration, string $moderator, bool $override = false): SimplePunishment
    {
        if (is_null($reason))
            $reason = $this->getPlugin()->getMessage('moderation.mute.mute.default-reason');
        if (is_null($expiration))
            $expiration = strtotime('+10 year');

        return $this->playerPunish($name, $reason, $expiration, $moderator, 'mute', $this->mutes,
            $this->getMessages()['mute']['already-muted'], $override);
    }

    /**
     * @param string $name
     * @throws CucumberException If the player is not muted
     */
    public function unmute(string $name): void
    {
        $this->playerPardon($name, 'mute', $this->mutes, $this->getMessages()['mute']['not-muted']);
    }

    public function isBanned(CucumberPlayer $player): ?SimplePunishment
    {
        $name = $player->getName();
        if ($ban = $this->getBan($name)) {
            if ($ban->isExpired())
                $this->unban($name);
            else return $ban;
        }

        $ip = $player->getIp();
        if ($ip_ban = $this->getIpBan($ip)) {
            if ($ip_ban->isExpired())
                $this->ipUnban($ip);
            else return $ip_ban;
        }

        return null;
    }

    public function isMuted(CucumberPlayer $player): ?SimplePunishment
    {
        $name = $player->getName();
        if ($mute = $this->getMute($name)) {
            if ($mute->isExpired())
                $this->unmute($name);
            else return $mute;
        }

        return null;
    }

}