<?php

namespace Tests\Acceptance;

use Dice\Match;
use Dice\Player;
use Tests\TestCase;

class MatchTest extends TestCase
{
    /**
     * @test
     */
    public function it_conducts_a_match_consisting_of_n_rounds_and_chooses_the_winner_based_on_lowest_combined_score()
    {
        // Arrange
        $rounds = 4;
        $players = [
            new Player($this->faker()->name),
            new Player($this->faker()->name),
            new Player($this->faker()->name),
            new Player($this->faker()->name),
        ];

        $match = new Match($rounds, $players);
        // Act
        // Assert
    }
}
