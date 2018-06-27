<?php

namespace cucumber\event;

/**
 * @allowHandle
 */

abstract class CEvent
{

    abstract public function getData(): array;

}