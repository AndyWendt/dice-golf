<?php

namespace Dice\Game;

use Dice\Game\Factory;
use Dice\Game\Leaderboard;
use Dice\Player\Player;
use Dice\Player\PlayerScore;

class Round
{
    /**
     * @var Factory
     */
    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $players
     * @return \Dice\Game\Leaderboard
     * @throws \InvalidArgumentException
     */
    public function play(array $players): Leaderboard
    {
        $this->assertAtLeastOnePlayerWasProvided($players);

        $leaderboard = $this->factory->createLeaderboard();

        /** @var Player $player */
        foreach ($players as $player) {
            $leaderboard->add(new PlayerScore($player->roll(), $player));
        }

        return $leaderboard;
    }

    /**
     * @param array $players
     * @return bool
     */
    private function noPlayersProvided(array $players): bool
    {
        return empty($players);
    }

    /**
     * @param array $players
     * @throws \InvalidArgumentException
     */
    private function assertAtLeastOnePlayerWasProvided(array $players): void
    {
        if ($this->noPlayersProvided($players)) {
            throw new \InvalidArgumentException("Players array must contain at least one element");
        }
    }
}