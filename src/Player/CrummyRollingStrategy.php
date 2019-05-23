<?php

namespace Dice\Player;

use Dice\Player\RollingStrategy;

/**
 * A naive rolling strategy taking anything that is 5 or less
 * unless no die less than 5 are found in which case a 6 die would be chosen
 */
class CrummyRollingStrategy extends RollingStrategy
{
    public function keep(array $dice): array
    {
            if (count($dice) === 1) {
                return $dice;
            }

            sort($dice);
            $out = [];

            foreach ($dice as $die) {
                if ($die <= 5) {
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