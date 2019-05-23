<?php

namespace Dice\Game;

use Dice\Game\Factory;
use Dice\Game\GameResult;
use Dice\Game\Leaderboard;

class Game
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
     * @param $players
     * @return \Dice\Game\GameResult
     * @throws \InvalidArgumentException
     */
    public function play(array $players): GameResult
    {
        $this->assertAtLeastOnePlayerWasProvided($players);

        /** @var Leaderboard[] $rounds */
        $rounds = [];
        $roundCount = count($players);

        // Need to randomize the players before starting so that a random player starts
        shuffle($players);

        $leaderboard = $this->factory->createLeaderboard();
        for ($i = 0; $i < $roundCount; $i++) {
            $rounds = $this->playRound($players, $leaderboard, $rounds);
            $players = $this->rotateStartingRoller($players);
        }

        return new GameResult($rounds, $leaderboard);
    }

    private function rotateStartingRoller(array $players): array
    {
        array_push($players, array_shift($players));
        return $players;
    }

    /**
     * @param $players
     * @param \Dice\Game\Leaderboard $leaderboard
     * @param $rounds
     * @return array
     */
    private function playRound($players, \Dice\Game\Leaderboard $leaderboard, $rounds): array
    {
        $roundLeaderboard = $this->factory->createRound()->play($players);
        $leaderboard->combine($roundLeaderboard);
        $rounds[] = $roundLeaderboard;
        return $rounds;
    }

    /**
     * @param $players
     * @throws \InvalidArgumentException
     */
    private function assertAtLeastOnePlayerWasProvided($players): void
    {
        if (!count($players) > 0) {
            throw new \InvalidArgumentException("At least one player must be provided");
        }
    }
}