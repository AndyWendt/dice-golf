<?php

namespace Dice;

use Dice\Game\DiceGameFactory;
use Dice\Game\Leaderboard;
use Dice\Game\Round;
use Dice\Player\Player;

class RoundTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function it_selects_the_winner_as_the_player_with_the_lowest_score()
    {
        $player1Mock = \Mockery::mock(Player::class);
        $player1Mock->shouldReceive('roll')
            ->andReturn(10);

        $player2Mock = \Mockery::mock(Player::class);
        $player2Mock->shouldReceive('roll')
            ->andReturn(12);

        $player3Mock = \Mockery::mock(Player::class);
        $player3Mock->shouldReceive('roll')
            ->andReturn(3);

        $player4Mock = \Mockery::mock(Player::class);
        $player4Mock->shouldReceive('roll')
            ->andReturn(25);

        // Arrange
        $players = [$player1Mock, $player2Mock, $player3Mock, $player4Mock];
        $round = new Round(new DiceGameFactory());

        // Act
        $result = $round->play($players);

        // Assert
        $this->assertInstanceOf(Leaderboard::class, $result);
        $this->assertSame($player3Mock, $result->leader()->player());
    }

    /**
     * @test
     */
    public function it_fails_if_no_players_are_passed_in()
    {
        $this->expectException(\InvalidArgumentException::class);
        $players = [];
        (new Round(new DiceGameFactory()))->play($players);
    }
}
