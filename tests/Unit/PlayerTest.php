<?php

namespace Tests\Unit;

use Dice\Player\Player;
use Dice\Player\Roller;
use Dice\Player\RollingStrategy;
use Tests\TestCase;

class PlayerTest extends TestCase
{
    /**
     * @test
     * @dataProvider rollDataProvider
     */
    public function it_rolls_the_dice_for_a_turn(array $rollResults, array $keepResults)
    {
        $rollerMock = \Mockery::mock(Roller::class);
        foreach ($rollResults as $rollResult) {
            $rollerMock->shouldReceive('roll')
                ->with(count($rollResult))
                ->once()
                ->andReturn($rollResult);
        }

        $rollingStrategy = \Mockery::mock(RollingStrategy::class);
        foreach ($keepResults as $key => $keepResult) {
            $rollingStrategy->shouldReceive("keep")
                ->with($rollResults[$key])
                ->once()
                ->andReturn($keepResult);
        }

        $instance = new Player($this->faker()->name, $rollingStrategy, $rollerMock);
        $roll = $instance->roll();

        $expectedScore = array_reduce($keepResults, function (int $sum, array $keep) {
            $keep = array_filter($keep, function (int $number) {
                return $number !== 4;
            });

            $sum += array_sum($keep);
           return $sum;
        }, 0);

        $this->assertSame($expectedScore, $roll);
    }

    public function rollDataProvider()
    {
        return [
            [
                [[6,3,2,4,1],[6,3],[6]], // rolls
                [[1,2,4],[3],[6]] // kept
            ],
            [
                [[4,4,4,4,4]], // rolls
                [[4,4,4,4,4]] // kept
            ],
            [
                [[6,6,6,6,6], [6,6,6,6], [6,6,6], [6,6], [6]], // rolls
                [[6], [6], [6], [6], [6]] // kept
            ],
        ];
    }
}
