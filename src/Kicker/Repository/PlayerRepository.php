<?php

namespace Kicker\Repository;

class PlayerRepository extends ParticipantRepository
{
    static $table = 'players';

    public function getStatistics($sort, $order)
    {
        $sql = <<<SQL
            SELECT p.name player, p.rating as rating,
            SUM(IF(p.id = t.goalkeeper_id AND t.id = m.red_team_id, m.blue_score, IF(p.id = t.goalkeeper_id AND t.id = m.blue_team_id, m.red_score, 0))) as passed_goals,
            ROUND(SUM(IF(p.id = t.goalkeeper_id AND t.id = m.red_team_id, m.blue_score, IF(p.id = t.goalkeeper_id AND t.id = m.blue_team_id, m.red_score, 0))) / SUM(IF(p.id = t.goalkeeper_id, 1, 0)), 2) as avg_passed_goals,
            SUM(IF(p.id = t.goalkeeper_id, 1, 0)) as matches_as_goalkeeper,
            SUM(IF(p.id = t.forward_id, 1, 0)) as matches_as_forward,
            SUM(IF(p.id = t.forward_id AND t.id = m.red_team_id, m.red_score, IF(p.id = t.forward_id AND t.id = m.blue_team_id, m.blue_score, 0))) as goals,
            ROUND(SUM(IF(p.id = t.forward_id AND t.id = m.red_team_id, m.red_score, IF(p.id = t.forward_id AND t.id = m.blue_team_id, m.blue_score, 0))) / SUM(IF(p.id = t.forward_id, 1, 0)), 2) as avg_goals,
            ROUND((SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) / COUNT(m.id)) * 100 ) as win_percent,

            SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) as wins,
            COUNT(m.id) as matches
            FROM players p
            JOIN teams t ON p.id = t.forward_id OR p.id = t.goalkeeper_id
            JOIN matches m ON t.id = m.red_team_id OR t.id = m.blue_team_id
            WHERE p.is_active = 1

            GROUP BY p.id
            ORDER BY {$sort} {$order}
            limit 10
SQL;

        return $this->getConnection()->fetchAll($sql, [':sort' => $sort, ':order' => $order]);
    }

    public function getActive()
    {
        $sql = <<<SQL
            SELECT * FROM players WHERE is_active = 1
SQL;
        return $this->getConnection()->fetchAll($sql);
    }


}