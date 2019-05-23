<?php

namespace Dice;

use Dice\Player\Roller;
use PHPUnit\Framework\TestCase;

class RollerTest extends TestCase
{
    /**
     * @test
     * @dataProvider rollerDataProvider
     */
    public function it_rolls_dice_and_returns_the_result_as_an_array(int $count)
    {
        // Arrange
        $roller = new Roller();
        // Act
        $result = $roller->roll($count);
        // Assert

        $this->assertCount($count, $result);
        foreach ($result as $item) {
            $this->assertIsInt($item);
            $this->assertGreaterThanOrEqual(1, $item);
            $this->assertLessThanOrEqual(6, $item);
        }
    }

    /**
     * @test
     * @dataProvider invalidRollTimesDataProvider
     */
    public function it_fails_if_given_bad_times_to_roll(int $times)
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Roller())->roll($times);
    }

    public function rollerDataProvider(): array
    {
        return [
          // expected count,
          [5],
          [4],
          [3],
          [2],
          [1],
        ];
    }

    public function invalidRollTimesDataProvider()
    {
        return [
            // times
            [-1],
            [-100000000],
            [-0],
            [0],
            [7],
            [6],
            [10],
            [1000000000],
        ];
    }
}
