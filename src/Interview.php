<?php

namespace Dice;

use Dice\Game\DiceGameFactory;
use Dice\Game\Game;
use Dice\Player\Player;
use Dice\Player\Roller;
use Dice\Player\SimpleRollingStrategy;
use Faker\Generator;

class Interview
{
    /**
     * Creates a new game and prints out the winner
     */
    public function playGame(): void
    {
        $players = $this->createPlayers();
        $game = new Game(new DiceGameFactory());
        $result = $game->play($players);
        $playerScore = $result->leaderboard()->leader();
        echo $this->createWinnerString($playerScore);
    }

    /**
     * @param \Dice\Player\PlayerScore $playerScore
     * @return string
     */
    private function createWinnerString(\Dice\Player\PlayerScore $playerScore): string
    {
        return sprintf("Name: %s | Score: %s", $playerScore->player()->name(), $playerScore->score());
    }

    /**
     * Creates a few players with fake names for our purposes.
     * The composer.json has faker required for prod because of this
     *
     * @return array
     */
    private function createPlayers(): array
    {
        $simpleRollingStrategy = new SimpleRollingStrategy();
        $roller = new Roller();
        return [
            new Player($this->faker()->name, $simpleRollingStrategy, $roller),
            new Player($this->faker()->name, $simpleRollingStrategy, $roller),
            new Player($this->faker()->name, $simpleRollingStrategy, $roller),
            new Player($this->faker()->name, $simpleRollingStrategy, $roller),
        ];
    }

    private function faker(): Generator
    {
        return \Faker\Factory::create();
    }
}