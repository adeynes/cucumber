<?php
declare(strict_types=1);

namespace adeynes\cucumber\utils;

interface Formattable
{

    public function getFormatData(): array;

    public function getMessagesPath(): string;

}