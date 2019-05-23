<?php

namespace Dice\Game;

use Dice\Player\PlayerScore;

class Leaderboard
{

    /**
     * @var PlayerScore[]
     */
    private $scores = [];

    /**
     * Adds a player score and sorts the scores
     *
     * @param PlayerScore $score
     */
    public function add(PlayerScore $score)
    {
        // need to catch duplicate players being added
        $this->addScore($score);
        $this->sortScores();
    }

    /**
     * @return PlayerScore
     * @throws \RuntimeException
     */
    public function leader(): PlayerScore
    {
        // can't return a score if the array is empty
        if (empty($this->scores)) {
            throw new \RuntimeException("Scores is empty");
        }

        return $this->scores[0];
    }

    /**
     * It combines Leaderboards to create a composite leaderboard
     *
     * @param Leaderboard $leaderboard
     */
    public function combine(Leaderboard $leaderboard)
    {
        foreach ($leaderboard->toArray() as $playerScore) {
            $this->addScore($playerScore);
        }
    }

    public function toArray(): array
    {
        return $this->scores;
    }

    private function addScore(PlayerScore $score)
    {
        if ($this->updatePlayerScoreIfFound($score)) {
            return;
        }

        array_push($this->scores, $score);
    }

    /**
     * Sort PlayerScores ascending
     */
    private function sortScores(): void
    {
        usort($this->scores, function (PlayerScore $scoreA, PlayerScore $scoreB) {
            if ($scoreA->score() === $scoreB->score()) {
                return 0;
            }

            return ($scoreA->score() < $scoreB->score()) ? -1 : 1;
        });
    }

    /**
     * @param PlayerScore $score
     * @return bool
     */
    private function updatePlayerScoreIfFound(PlayerScore $score): bool
    {
        $updated = false;

        array_walk($this->scores, function (PlayerScore &$compareScore) use ($score, &$updated) {
            if ($this->playerForScoresAreTheSame($compareScore, $score)) {
                $updated = true;
                $combinedScore = $compareScore->score() + $score->score();
                $compareScore = new PlayerScore($combinedScore, $compareScore->player());
            }

            return $compareScore;
        });

        return $updated;
    }

    /**
     * @param PlayerScore $compareScore
     * @param PlayerScore $score
     * @return bool
     */
    private function playerForScoresAreTheSame(PlayerScore &$compareScore, PlayerScore $score): bool
    {
        return spl_object_hash($score->player()) === spl_object_hash($compareScore->player());
    }
}