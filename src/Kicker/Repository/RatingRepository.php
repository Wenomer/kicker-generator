<?php

namespace Kicker\Repository;

abstract class RatingRepository extends Repository
{
    static $participantFieldName = 'player_id';

    public function reset()
    {
        $this->getConnection()->executeUpdate("TRUNCATE TABLE " . static::$table);
    }

    protected function calculateRating($oldRating, $score, $opponentRating, $commandRating = null)
    {
        $commandRating = is_null($commandRating) ? $oldRating : $commandRating;

        $elo = $this->elo;
        return $elo::calculate($oldRating, $score, $commandRating, $opponentRating);
    }

    protected function saveLog($playerId, $matchId, $rating, $diff)
    {

        $this->getConnection()->insert(static::$table, [static::$participantFieldName => $playerId, 'match_id' => $matchId, 'rating' => $rating, 'diff' => $diff]);
    }

    protected function saveRating($rows, $matchId, ParticipantRepository $participantRepository)
    {
        foreach ($rows as $row) {
            $commandRating = isset($row['command']) ? $row['command'] : null;
            $rating = $this->calculateRating($row['old'], $row['score'], $row['opponent'], $commandRating);

            $participantRepository->saveRating($row['id'], $rating);
            $this->saveLog($row['id'], $matchId, $rating, $rating - $row['old']);
        }
    }

    abstract public function update($match, ParticipantRepository $participantRepository, ParticipantRepository $supportRepository = null);
}