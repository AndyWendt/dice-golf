<?php

namespace Dice;

use Dice\Player\SimpleRollingStrategy;
use PHPUnit\Framework\TestCase;

class SimpleRollingStrategyTest extends TestCase
{
    /**
     * @test
     * @dataProvider diceDataProvider
     */
    public function it_keeps_dice(array $dice, array $expected)
    {
        $result = (new SimpleRollingStrategy())->keep($dice);
        $this->assertSame(sort($expected), sort($result));
    }

    public function diceDataProvider(): array
    {
        return [
            // dice, expected result
            [[4,2,1,4,1], [4,2,1,4,1]], // everything is a keeper
            [[6,6,6,6,6], [6]], // forced to take one of them
            [[6], [6]], // forced to take one of them
            [[6,3,5], [3]], // forced to take one of them
            [[4,5], [4]], // forced to take one of them
        ];
    }
}
