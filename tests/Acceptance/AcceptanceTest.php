<?php

namespace Tests\Acceptance;

use Dice\Game\DiceGameFactory;
use Dice\Game\Factory;
use Dice\Interview;
use Dice\Game\Leaderboard;
use Dice\Game\Game;
use Dice\Game\GameResult;
use Dice\Player\CrummyRollingStrategy;
use Dice\Player\Player;
use Dice\Player\Roller;
use Dice\Game\Round;
use Dice\Player\SimpleRollingStrategy;
use MathPHP\Statistics\Descriptive;
use Tests\TestCase;

class AcceptanceTest extends TestCase
{
    /**
     * @test
     */
    public function it_plays_a_game_and_displays_a_winner()
    {
        ob_start();
        (new Interview)->playGame();
        $resultString = ob_get_clean();
        $resultArray = explode('|', $resultString);

        // Name: Test Name | Score: 45
        $nameString = $resultArray[0];
        $nameHeading = 'Name: ';
        $name = trim(str_replace($nameHeading, '', $nameString));
        $this->assertStringContainsString($nameHeading, $nameString);
        $this->assertTrue(strlen($name) > 0);

        $scoreString = $resultArray[1];
        $scoreHeading = 'Score: ';
        $score = trim(str_replace($scoreHeading, '', $scoreString));
        $maxGameScore = 30 * 4;
        $this->assertIsNumeric($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual($maxGameScore, $score);
    }

    /**
     * @test
     */
    public function it_roughly_evenly_distributes_wins_when_players_are_using_the_same_algorithm()
    {
        // Arrange
        $trials = 1000;

        $players = $this->createPlayers();

        $winsTally = [
            spl_object_hash($players[0]) => 0,
            spl_object_hash($players[1]) => 0,
            spl_object_hash($players[2]) => 0,
            spl_object_hash($players[3]) => 0,
        ];

        // Act
        for ($i = 0; $i < $trials; $i++) {
            $game = new Game(new DiceGameFactory());
            $winsTally[spl_object_hash($game->play($players)->leaderboard()->leader()->player())]++;
        }

        $stats = Descriptive::describe($winsTally, true);

        // Assert
        $this->assertLessThan(70, $stats['range']);
    }

    /**
     * @test
     */
    public function it_favors_better_algorithms()
    {
        // Arrange
        $trials = 1000;

        $simpleRollingStrategy = new SimpleRollingStrategy();
        $crummyRollingStrategy = new CrummyRollingStrategy();
        $roller = new Roller();
        $players = [
            new Player($this->faker()->name, $crummyRollingStrategy, $roller),
            new Player($this->faker()->name, $crummyRollingStrategy, $roller),
            new Player($this->faker()->name, $crummyRollingStrategy, $roller),
            new Player($this->faker()->name, $simpleRollingStrategy, $roller),
        ];

        $winsTally = [
            spl_object_hash($players[0]) => 0,
            spl_object_hash($players[1]) => 0,
            spl_object_hash($players[2]) => 0,
            spl_object_hash($players[3]) => 0,
        ];

        // Act
        for ($i = 0; $i < $trials; $i++) {
            $game = new Game(new DiceGameFactory());
            $winsTally[spl_object_hash($game->play($players)->leaderboard()->leader()->player())]++;
        }

        $stats = Descriptive::describe($winsTally, true);

        // Assert
        $this->assertGreaterThan(300, $stats['range']);
        $this->assertGreaterThan(450, $winsTally[spl_object_hash($players[3])]);
    }

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
}
