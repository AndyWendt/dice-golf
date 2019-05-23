<?php

namespace Dice\Game;

use Dice\Game\Factory;
use Dice\Game\Leaderboard;
use Dice\Game\Round;

class DiceGameFactory implements Factory
{
    public function createLeaderboard(): Leaderboard
    {
        return new Leaderboard();
    }

    public function createRound(): Round
    {
        return new Round($this);
    }
}