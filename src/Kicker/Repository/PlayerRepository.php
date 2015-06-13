<?php

namespace Kicker\Repository;

class PlayerRepository extends Repository
{
    public function findAll()
    {
        return $this->db->fetchAll('SELECT * FROM players');
    }

    public function getStatistics($sort, $order)
    {
        $sql = <<<SQL
            SELECT p1.name as   forward, p2.name as goalkeeper, count(m.id) as matches,
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
}