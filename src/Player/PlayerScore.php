<?php

namespace Dice\Player;

use Dice\Player\Player;

class PlayerScore
{
    /**
     * @var int
     */
    private $score;

    /**
     * @var Player
     */
    private $player;

    public function __construct(int $score, Player $player)
    {
        $this->score = $score;
        $this->player = $player;
    }

    public function score(): int
    {
        return $this->score;
    }

    public function player(): Player
    {
        return $this->player;
    }
}