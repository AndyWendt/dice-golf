<?php

namespace Dice\Game;

use Dice\Game\Leaderboard;

class GameResult
{
    /**
     * @var Leaderboard[]
     */
    private $rounds;
    /**
     * @var Leaderboard
     */
    private $leaderboard;

    /**
     * @param Leaderboard[] $rounds
     * @param Leaderboard $leaderboard
     */
    public function __construct(array $rounds, Leaderboard $leaderboard)
    {
        $this->rounds = $rounds;
        $this->leaderboard = $leaderboard;
    }

    public function leaderboard(): Leaderboard
    {
        return $this->leaderboard;
    }

    /**
     * @return Leaderboard[]
     */
    public function rounds(): array
    {
        return $this->rounds;
    }
}