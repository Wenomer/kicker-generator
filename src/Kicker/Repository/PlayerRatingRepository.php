<?php

namespace Kicker\Repository;


class PlayerRatingRepository extends Repository
{
    static $table = 'player_rating_log';

    public function getLog()
    {
        $sql = <<<SQL
            SELECT players.name as player_name, players.id as player_id, player_rating_log.rating as rating
            FROM player_rating_log
            JOIN players ON players.id = player_id
            JOIN matches ON matches.id = match_id
            ORDER BY matches.date ASC
SQL;

        return $this->db->fetchAll($sql);
    }
}