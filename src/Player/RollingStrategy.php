<?php

namespace Dice\Player;

abstract class RollingStrategy
{
    abstract public function keep(array $dice): array;
}