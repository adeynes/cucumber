<?php
declare(strict_types=1);

namespace adeynes\cucumber\event;

use adeynes\cucumber\utils\HasData;
use pocketmine\event\Event;

/**
 * The parent class for all Cucumber events,
 * used to listen for all of them to log
 * @allowHandle
 */
abstract class CucumberEvent extends Event implements HasData
{

}