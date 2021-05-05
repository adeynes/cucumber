<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Ban;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\CommandParser;
use adeynes\parsecmd\command\ParsedCommand;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;

class BanCommand extends CucumberCommand
{

    private $config_;

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ban',
            'cucumber.command.ban',
            'Ban a player by name',
            '/ban <player> <duration>|inf [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name, $duration, $reason] = $command->get(['player', 'duration', 'reason']);
        $target_name = strtolower($target_name);
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

        $ban = function () use ($sender, $target_name, $reason, $expiration) {
            try {
                $ban = new Ban($target_name, $reason, $expiration, $sender->getName(), time());
                $ban_data = $ban->getFormatData();
                $this->getPlugin()->getPunishmentRegistry()->addBan($ban);
                $ban->save($this->getPlugin()->getConnector());

                if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                    $target->kick(
                        $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $ban_data),
                        false // don't say Kicked by admin
                    );
                }

                $this->getPlugin()->formatAndSend($sender, 'success.ban', $ban_data);

                // send details on discord server
                $whook = $this->getConfig()->get('webh');
                $webhook = new Webhook($whook);

                $msg = new Message();
                $msg->setUsername("HoennPE SysBan");
                $msg->setAvatarURL("https://cdn.discordapp.com/attachments/834138834999705670/836139083981520926/HoennPE_SummerLogo_00000.png");
                $list = array("wowowowowow", "uh-oh", "nice", "bruuhh", "lmao", "xD", "oh wow!", "heyyyyy", "lol", "rip", "ggwp");
                $msg->setContent("");

                $embed = new Embed();
                $embed->setTitle("BANNED");
                $embed->setColor(0xFF0000);
                $embed->addField(array_rand($list), "> " . $target_name . "is banned by " . $sender->getName() . " for " . $expiration . " due to " . $reason);
                $embed->setFooter("cucumber for HoennPE", "https://github.com/HoennPE/cucumber");
                $msg->addEmbed($embed);

                $webhook->send($msg);

                return true;
            } catch(CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $this->doIfTargetExists($ban, $sender, $target_name);
        return true;
    }

    public function getConfig(): Config
    {
        return $this->config_;
    }

}