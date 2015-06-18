<?php

namespace Kicker\Repository;

class TeamRepository extends Repository
{
    static $table = 'teams';

    public function getOrCreateTeamId($goalkeeperId, $forwardId)
    {
        $team = $this->db->fetchAll('SELECT * FROM teams WHERE goalkeeper_id = :goalkeeper AND forward_id = :forward', [
            'goalkeeper' => $goalkeeperId,
            'forward' => $forwardId
        ]);

        if (empty($team)) {
            return $this->create($goalkeeperId, $forwardId);
        }

        return $team[0]['id'];
    }

    public function getStatistics($sort, $order)
    {
        $sql = <<<SQL
            SELECT p1.name as forward, p2.name as goalkeeper, count(m.id) as matches, t.rating as  team_rating,
            SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) as wins,
            ROUND((SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score))/ count(m.id)) * 100) as win_percent

            FROM teams t
            JOIN players p1 ON p1.id = t.forward_id
            JOIN players p2 ON p2.id = t.goalkeeper_id
            LEFT JOIN matches m ON m.red_team_id = t.id OR m.blue_team_id = t.id

            GROUP BY t.id
            ORDER BY {$sort} {$order}
            limit 10
SQL;

        return $this->db->fetchAll($sql, [':sort' => $sort, ':order' => $order]);
    }


    public function getColorStatistics($sort, $order)
    {
        $sql = <<<SQL
            SELECT *,
            IF(t.id = m.red_team_id, 'Red', 'Blue') as color,
            ROUND((SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) / COUNT(m.id)) * 100 ) as win_percent,
            SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) as wins,
            COUNT(m.id) as matches
            FROM teams t
            JOIN matches m ON t.id = m.red_team_id OR t.id = m.blue_team_id
            GROUP BY color
            ORDER BY {$sort} {$order}
SQL;

        return $this->db->fetchAll($sql, [':sort' => $sort, ':order' => $order]);
    }



    private function create($goalkeeperId, $forwardId)
    {
        $this->db->executeUpdate("INSERT INTO teams (`goalkeeper_id`, `forward_id`) VALUES (:goalkeeper, :forward)", [
            'goalkeeper' => $goalkeeperId,
            'forward' => $forwardId
        ]);

        return $this->db->lastInsertId();
    }
}