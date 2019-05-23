<?php

namespace Dice\Game;

use Dice\Game\Leaderboard;
use Dice\Game\Round;

interface Factory
{
    public function createLeaderboard(): Leaderboard;
    public function createRound(): Round;
}