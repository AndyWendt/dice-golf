<?php

namespace Tests\Unit;

use Dice\Game\DiceGameFactory;
use Dice\Game\Factory;
use Dice\Game\Leaderboard;
use Dice\Game\Game;
use Dice\Game\GameResult;
use Dice\Player\Player;
use Dice\Player\PlayerScore;
use Dice\Player\Roller;
use Dice\Game\Round;
use Dice\Player\SimpleRollingStrategy;
use MathPHP\Statistics\Descriptive;
use Tests\TestCase;

class GameTest extends TestCase
{
    /**
     * @test
     * After​ ​ all​ ​ four​ ​ rounds​ ​ have​ ​ been​ ​ completed​ ​ the​ ​ player​ ​ with​ ​ the​ ​ lowest​ ​ combined​ ​ score​ ​ wins
     */
    public function it_conducts_a_match_consisting_of_n_rounds_and_chooses_the_winner_based_on_lowest_combined_score()
    {
        // Arrange
        $roundsCount = 4;
        $players = $this->createPlayers();
        $game = new Game(new DiceGameFactory());

        // Act
        $result = $game->play($players);

        // Assert
        $this->assertInstanceOf(GameResult::class, $result);
        $this->assertSame($roundsCount, count($result->rounds()));
        $this->assertSame($result->leaderboard()->leader(), $result->leaderboard()->toArray()[0]);
        $this->assertLeaderboardIsInDescendingOrder($result->leaderboard());
        $this->assertCount(count($players), $result->leaderboard()->toArray());
    }

    /**
     * @test
     * * Play​ ​ starts​ ​ with​ ​ one​ ​ person​ ​ randomly​ ​ being​ ​ chosen​ ​ to​ ​ start​ ​ rolling​ ​ and​ ​ proceeds​ ​ in
     *      succession​ ​ until​ ​ all​ ​ players​ ​ have​ ​ rolled.
     * * Repeat​ ​ for​ ​ three​ ​ more​ ​ rounds​ ​ in​ ​ succession​ ​ so​ ​ that​ ​ the​ ​ next​ ​ person​ ​ starts​ ​ rolling​ ​
     *      first​ ​ (at the​ ​ end​ ​ each​ ​ player​ ​ will​ ​ have​ ​ started).
     */
    public function it_rotates_the_starting_roller()
    {
        // Arrange
        $players = $this->createPlayers();
        $roundMock = $this->getRoundMock($players);
        $leaderboardMock = \Mockery::mock(Leaderboard::class);
            $leaderboardMock->shouldReceive('combine')
            ->andReturn(new Leaderboard());

        $factoryMock = \Mockery::mock(Factory::class);
        $factoryMock->shouldReceive('createLeaderboard')
            ->andReturn($leaderboardMock);
        $factoryMock->shouldReceive('createRound')
            ->andReturn($roundMock);

        $game = new Game($factoryMock);

        // Act

        $game->play($players);
        // Assert
    }

    /**
     * @test
     */
    public function it_requires_at_least_one_player()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Game(new DiceGameFactory()))->play([]);
    }

    /**
     * @test
     */
    public function it_fails_if_passed_null_for_the_players()
    {
        $this->expectException(\TypeError::class);
        (new Game(new DiceGameFactory()))->play(null);
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

    private function createExpectedPlayerArrays(array $players): array
    {
        $out = [];

        for ($i = 0; $i < count($players); $i++) {
            array_push($out, $players);
            array_push($players, array_shift($players));
        }

        return $out;
    }

    /**
     * @return Round|\Mockery\MockInterface
     */
    private function getRoundMock()
    {
        $roundMock = \Mockery::mock(Round::class);
        $expectedPlayerArrays = [];
        $firstArgument = true;

        /*
         * This was done so that we could account for the shuffling of the players in the
         * implementation.  Otherwise, expectations couldn't be set reliably
         */
        $roundMock->shouldReceive("play")
            ->with(\Mockery::on(function (array $playersArgument) use (&$expectedPlayerArrays, &$firstArgument) {
                if ($firstArgument) {
                    $expectedPlayerArrays = $this->createExpectedPlayerArrays($playersArgument);
                    $firstArgument = false;
                }

                $expectedPlayerArray = array_shift($expectedPlayerArrays);

                $result = (
                    $playersArgument[0] == $expectedPlayerArray[0] &&
                    $playersArgument[1] == $expectedPlayerArray[1] &&
                    $playersArgument[2] == $expectedPlayerArray[2] &&
                    $playersArgument[3] == $expectedPlayerArray[3]
                );

                return $result;
            }));

        return $roundMock;
    }

    private function assertLeaderboardIsInDescendingOrder(Leaderboard $leaderboard)
    {
        /** @var PlayerScore|null $previous */
        $previous = null;

        /** @var PlayerScore $playerScore */
        foreach ($leaderboard->toArray() as $playerScore) {
            if ($previous === null) {
                $previous = $playerScore;
                continue;
            }

            if (!$playerScore->score() > $previous->score()) {
                $this->fail("Player score b is not greater than a");
            }

            $previous = $playerScore;
        }
    }
}
