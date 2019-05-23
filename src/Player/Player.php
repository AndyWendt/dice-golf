<?php

namespace Dice\Player;

use Dice\Player\Roller;
use Dice\Player\RollingStrategy;

class Player
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var RollingStrategy
     */
    private $rollingStrategy;
    /**
     * @var Roller
     */
    private $roller;

    public function __construct(string $name, RollingStrategy $rollingStrategy, Roller $roller)
    {
        $this->name = $name;
        $this->rollingStrategy = $rollingStrategy;
        $this->roller = $roller;
    }

    public function roll(): int
    {
        $diceLeft = 5;
        $score = 0;

        while($diceLeft > 0) {
            $dice = $this->roller->roll($diceLeft);
            $diceToKeep = $this->rollingStrategy->keep($dice);

            $score += $this->calculateScore($diceToKeep);
            $diceLeft -= count($diceToKeep);
        }

        return $score;
    }

    public function name(): string
    {
        return $this->name;
    }

    private function calculateScore(array $dice): int
    {
        $sum = 0;
        $keep = array_filter($dice, function (int $number) {
            return $number !== 4;
        });

        $sum += array_sum($keep);
        return $sum;
    }
}