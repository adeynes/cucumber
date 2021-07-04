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

class PardonCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'pardon',
            'cucumber.command.pardon',
            'Pardon a player',
            '/pardon <player>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target_name] = $command->get(['player']);
        $target_name = strtolower($target_name);

        try {
            $this->getPlugin()->getPunishmentRegistry()->removeBan($target_name);
            $this->getPlugin()->getConnector()->executeChange(
                Queries::CUCUMBER_PUNISH_UNBAN,
                ['player' => $target_name]
            );

            $this->getPlugin()->formatAndSend($sender, 'success.pardon', ['player' => $target_name]);

            // send details on discord server
            $whook = $this->getConfig()->get('webh');
            $webhook = new Webhook($whook);

            $msg = new Message();
            $msg->setUsername("HoennPE SysBan");
            $msg->setAvatarURL("https://i.imgur.com/KTToMRu.png");
            $list = array("wowowowowow", "nice", "oh wow!", "heyyyyy");
            $msg->setContent("");

            $embed = new Embed();
            $embed->setTitle("PARDON");
            $embed->setColor(0x00FF00);
            $embed->addField(array_rand($list), "> " . $target_name ." is now pardoned by " . $sender->getName());
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