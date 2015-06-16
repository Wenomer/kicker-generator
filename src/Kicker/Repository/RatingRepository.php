<?php

namespace Kicker\Repository;


class RatingRepository extends Repository
{
    static $table = 'rating_log';

    public function getLog()
    {
        $sql = <<<SQL
            SELECT players.name as player_name, players.id as player_id, rating_log.rating as rating
            FROM rating_log
            JOIN players ON players.id = player_id
            JOIN matches ON matches.id = match_id
            ORDER BY matches.date ASC
SQL;

        return $this->db->fetchAll($sql);
    }
}