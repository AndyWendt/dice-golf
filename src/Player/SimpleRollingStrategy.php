<?php

namespace Dice\Player;

use Dice\Player\RollingStrategy;

/**
 * Keeps anything less than or equal to 2 unless none are found for the roll
 * in which case the smallest number is kept
 */
class SimpleRollingStrategy extends RollingStrategy
{
    public function keep(array $dice): array
    {
            if (count($dice) === 1) {
                return $dice;
            }

            sort($dice);
            $out = [];

            foreach ($dice as $die) {
                // always take the zeros
                if ($die === 4) {
                    array_push($out, $die);
                    continue;
                }

                // what to keep
                // Expectation for rolling one di = 2.83 = ((1+2+3+5+6)÷6)
                if ($die <= 2) {
                    array_push($out, $die);
                    continue;
                }
            }

            // Default case if none matched
            if (count($out) === 0) {
                array_push($out, $dice[0]);
            }

            return $out;
    }
}