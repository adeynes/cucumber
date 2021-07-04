<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\IpBan;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\CommandParser;
use adeynes\parsecmd\command\ParsedCommand;
use InvalidArgumentException;
use pocketmine\command\CommandSender;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;

class IpbanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ipban',
            'cucumber.command.ipban',
            'Ban an IP',
            '/ipban <player>|<ip> <duration>|inf [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target, $duration, $reason] = $command->get(['target', 'duration', 'reason']);
        if ($reason === null) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }
        if (in_array($duration, self::PERMANENT_DURATION_STRINGS)) {
            $expiration = null;
        } else {
            try {
                $expiration = $duration ? CommandParser::parseDuration($duration) : null;
            } catch (InvalidArgumentException $exception) {
                $this->getPlugin()->formatAndSend($sender, 'error.invalid-duration', ['duration' => $duration]);
                return false;
            }
        }

        $ip_ban = function (string $ip) use ($sender, $reason, $expiration) {
            try {
                $ip_ban = new IpBan($ip, $reason, $expiration, $sender->getName(), time());
                $ip_ban_data = $ip_ban->getFormatData();
                $this->getPlugin()->getPunishmentRegistry()->addIpBan($ip_ban);
                $ip_ban->save($this->getPlugin()->getConnector());

                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    if ($player->getAddress() === $ip) {
                        $player->kick(
                            $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ip_ban_data),
                            false // don't say Kicked by admin
                        );
                    }
                }

                $this->getPlugin()->formatAndSend($sender, 'success.ipban', $ip_ban_data);

                // send details on discord server
                $whook = $this->getConfig()->get('webh');
                $webhook = new Webhook($whook);

                $msg = new Message();
                $msg->setUsername("HoennPE SysBan");
                $msg->setAvatarURL("https://i.imgur.com/KTToMRu.png");
                $list = array("wowowowowow", "uh-oh", "nice", "bruuhh", "lmao", "xD", "oh wow!", "heyyyyy", "lol", "rip", "ggwp");
                $msg->setContent("");

                $embed = new Embed();
                $embed->setTitle("IP-BANNED");
                $embed->setColor(0xFF0000);
                $embed->addField(array_rand($list), "> [Someone's IP] has been ip-banned by " . $sender->getName() . " for " . $expiration . " due to " . $reason);
                $embed->setFooter("cucumber for HoennPE", "https://github.com/HoennPE/cucumber");
                $msg->addEmbed($embed);

                $webhook->send($msg);

                return true;
            } catch (CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $ip_matches = [];
        preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $target, $ip_matches);
        if ($ip_matches === []) {
            if ($player = CucumberPlayer::getOnlinePlayer($target)) {
                $ip_ban($player->getAddress());
            } else {
                $this->doIfTargetExists(
                    function (array $rows) use ($ip_ban) {
                        $ip_ban($rows[0]['ip']);
                    },
                    $sender,
                    $target
                );
            }
        } else {
            $ip_ban($ip_matches[0]);
        }

        /*if ($target_name) {
            if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                $ip_ban($target->getAddress());
            } else {
                $this->doIfTargetExists(
                    function (array $rows) use ($ip_ban) {
                        $ip_ban($rows[0]['ip']);
                    },
                    $sender,
                    $target_name
                );
            }
            // don't return in case ip flag is set
        }

        if ($ip) {
            $ip_ban($ip);
        }

        if (!$target_name && !$ip) {
            $sender->sendMessage(
                MessageFactory::colorize("&cAt least one of flag &b-p &cand flag &b-ip&c must be set!")
            );
        }*/

        return true;
    }

}