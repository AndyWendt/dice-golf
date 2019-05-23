<?php

namespace Dice;

use Dice\Game\Leaderboard;
use Dice\Player\Player;
use Dice\Player\PlayerScore;
use Dice\Player\Roller;
use Dice\Player\SimpleRollingStrategy;
use Mockery;
use PHPUnit\Framework\TestCase;

class LeaderboardTest extends TestCase
{
    /**
     * @test
     */
    public function it_adds_a_score_to_the_ranker()
    {
        // Arrange
        $ranker = new Leaderboard();
        $player = Mockery::mock(Player::class);
        $score = new PlayerScore(20, $player);

        // Act
        $ranker->add($score);
    }

    /**
     * @test
     * Once​ ​ each​ ​ player​ ​ has​ ​ rolled​ ​ the​ ​ player​ ​ who​ ​ scored​ ​ the​ ​ lowest​ ​ wins.
     */
    public function it_returns_the_lowest_score_as_the_leader()
    {
        // Arrange
        $ranker = new Leaderboard();
        $playerScore1 = Mockery::mock(PlayerScore::class);
            $playerScore1->shouldReceive('score')
                ->andReturn(10);
        $playerScore1->shouldReceive('player');

        $playerScore2 = Mockery::mock(PlayerScore::class);
            $playerScore2->shouldReceive('score')
                ->andReturn(13);
        $playerScore2->shouldReceive('player');

        // Act
        $ranker->add($playerScore2);
        $ranker->add($playerScore1);
        $lowest = $ranker->leader();

        // Assert
        $this->assertSame($playerScore1, $lowest);
    }

    /**
     * @test
     */
    public function it_throws_if_scores_is_empty_and_lowest_is_called()
    {
        $this->expectException(\RuntimeException::class);
        (new Leaderboard())->leader();
    }

    /**
     * @test
     */
    public function it_combines_player_scores_for_the_same_player()
    {
        // Arrange
        $leaderboard = new Leaderboard();
        $simpleRollingStrategy = new SimpleRollingStrategy();
        $roller = new Roller();
        $player = new Player("Test Player", $simpleRollingStrategy, $roller);
        $player2 = new Player("Test Player 2", $simpleRollingStrategy, $roller);
        $playerScore1 = new PlayerScore(10, $player);
        $playerScore2 = new PlayerScore(21, $player);
        $playerScore3 = new PlayerScore(32, $player2);

        // Act
        $leaderboard->add($playerScore1);
        $leaderboard->add($playerScore3);
        $leaderboard->add($playerScore2);

        // Assert

        $this->assertSame(31, $leaderboard->leader()->score());
        $this->assertSame($player, $leaderboard->leader()->player());
    }

    /**
     * @test
     */
    public function it_converts_a_leaderboard_to_an_array()
    {
        // Arrange
        $ranker = new Leaderboard();
        $playerScore1 = Mockery::mock(PlayerScore::class);
        $playerScore1->shouldReceive('score')
            ->andReturn(10);
        $playerScore1->shouldReceive('player');

        $playerScore2 = Mockery::mock(PlayerScore::class);
        $playerScore2->shouldReceive('score')
            ->andReturn(13);
        $playerScore2->shouldReceive('player');

        // Act
        $ranker->add($playerScore2);
        $ranker->add($playerScore1);
        $result = $ranker->toArray();

        // Assert
        $this->assertSame($playerScore1, $result[0]);
        $this->assertSame($playerScore2, $result[1]);
    }

    /**
     * @test
     */
    public function it_combines_rankers()
    {
        // Arrange
        $leaderboardA = new Leaderboard();
        $leaderboardB = new Leaderboard();
        $simpleRollingStrategy = new SimpleRollingStrategy();
        $roller = new Roller();
        $player = new Player("Test Player", $simpleRollingStrategy, $roller);
        $player2 = new Player("Test Player 2", $simpleRollingStrategy, $roller);
        $playerScore1 = new PlayerScore(10, $player);
        $playerScore2 = new PlayerScore(21, $player);
        $playerScore3 = new PlayerScore(32, $player2);

        // Act
        $leaderboardA->add($playerScore1);
        $leaderboardA->add($playerScore3);
        $leaderboardA->add($playerScore2);
        $leaderboardB->add($playerScore1);
        $leaderboardB->add($playerScore3);
        $leaderboardB->add($playerScore2);
        $leaderboardA->combine($leaderboardB);

        $result = $leaderboardA->toArray();

        // Assert
        $this->assertSame(62, $result[0]->score());
        $this->assertSame(64, $result[1]->score());
        $this->assertSame($player, $result[0]->player());
        $this->assertSame($player2, $result[1]->player());
    }
}
