<?php

namespace cucumber\mod\lists;

use cucumber\mod\SimplePunishment;
use cucumber\utils\ErrorCodes;

abstract class SimplePunishmentList extends PunishmentList
{

    /**
     * @param SimplePunishment[] $punishments
     * @throws \Exception If one of the punishments exists twice
     */
    public function __construct(array $punishments = [])
    {
        foreach ($punishments as $punishment)
            $this->add($punishment);
    }

    /**
     * @param SimplePunishment $punishment
     * @throws \Exception If the player is already punished
     */

    public function add(SimplePunishment $punishment): void
    {
        $check = $punishment->getCheck();

        if (isset($this->punishments[$check]))
            throw new \Exception(
                'Attempted to add already-punished ' . $check . ' to punishment list',
                ErrorCodes::ATTEMPT_PUNISH_PUNISHED
            );

        $this->punishments[$check] = $punishment;
    }

    /**
     * @param mixed $check
     * @throws \Exception If the player isn't punished
     */
    public function remove($check): void
    {
        if (!isset($this->punishments[$check]))
            throw new \Exception(
                'Attempted tp remove not punished ' . $check . ' from punishment list',
                ErrorCodes::ATTEMPT_PARDON_NOT_PUNISHED
            );

        unset($this->punishments[$check]);
    }

}