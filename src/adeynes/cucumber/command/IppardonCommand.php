<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;

class IppardonCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'ippardon',
            'cucumber.command.ippardon',
            'Pardon an IP',
            '/ippardon <ip>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$ip] = $command->get(['ip']);

        try {
            $this->getPlugin()->getPunishmentRegistry()->removeIpBan($ip);
            $this->getPlugin()->getConnector()->executeChange(
                Queries::CUCUMBER_PUNISH_IP_UNBAN,
                ['ip' => $ip]
            );

            $this->getPlugin()->formatAndSend($sender, 'success.ippardon', ['ip' => $ip]);

            // send details on discord server
            $whook = $this->getConfig()->get('webh');
            $webhook = new Webhook($whook);

            $msg = new Message();
            $msg->setUsername("HoennPE SysBan");
            $msg->setAvatarURL("https://cdn.discordapp.com/attachments/834138834999705670/836139083981520926/HoennPE_SummerLogo_00000.png");
            $list = array("wowowowowow", "nice", "oh wow!", "heyyyyy");
            $msg->setContent("");

            $embed = new Embed();
            $embed->setTitle("IP-PARDON");
            $embed->setColor(0x00FF00);
            $embed->addField(array_rand($list), "> [Someone's IP] is now pardoned by " . $sender->getName());
            $embed->setFooter("cucumber for HoennPE", "https://github.com/HoennPE/cucumber");
            $msg->addEmbed($embed);

            $webhook->send($msg);
            return true;
        } catch (CucumberException $exception) {
            $sender->sendMessage($exception->getMessage());
            return false;
        }
    }

}