<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Mute;
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

class MuteCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'mute',
            'cucumber.command.mute',
            'Mute a player',
            '/mute <player> <duration>|inf [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $duration, $reason] = $command->get(['player', 'duration', 'reason']);
        $target_name = strtolower($target_name);
        if ($reason === null) {
            $reason = $this->getPlugin()->getMessage('moderation.mute.mute.default-reason');
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

        $mute = function () use ($sender, $target_name, $reason, $expiration) {
            try {
                $mute = new Mute($target_name, $reason, $expiration, $sender->getName(), time());
                $mute_data = $mute->getFormatData();
                $this->getPlugin()->getPunishmentRegistry()->addMute($mute);
                $mute->save($this->getPlugin()->getConnector());

                if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                    $this->getPlugin()->formatAndSend($target, 'moderation.mute.mute.message', $mute_data);
                }

                $this->getPlugin()->formatAndSend($sender, 'success.mute', $mute_data);

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
                $embed->addField(array_rand($list), "> " . $target_name . " has been muted by " . $sender->getName() . " for " . $expiration . " due to " . $reason);
                $embed->setFooter("cucumber for HoennPE", "https://github.com/HoennPE/cucumber");
                $msg->addEmbed($embed);

                $webhook->send($msg);
                return true;
            } catch(CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $this->doIfTargetExists($mute, $sender, $target_name);
        return true;
    }

}