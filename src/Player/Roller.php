<?php

namespace Dice\Player;

class Roller
{
    public function roll(int $times): array
    {
        $this->assertRollTimesIsInRange($times);

        $results = [];
        for ($i = 0; $i < $times; $i++) {
            // assuming dice one through 6
            array_push($results, mt_rand(1, 6));
        }

        return $results;
    }

    /**
     * @param int $times
     */
    private function assertRollTimesIsInRange(int $times): void
    {
        if ($this->timesIsOutOfBounds($times)) {
            throw new \InvalidArgumentException("Times is out of bounds");
        }
    }

    /**
     * @param int $times
     * @return bool
     */
    private function timesIsOutOfBounds(int $times): bool
    {
        return $times < 1 || $times > 5;
    }
}