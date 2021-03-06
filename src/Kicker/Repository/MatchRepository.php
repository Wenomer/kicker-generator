<?php

namespace Kicker\Repository;

class MatchRepository extends Repository
{
    static $table = 'matches';

    public function save($redTeamId, $blueTeamId, $redScore, $blueScore)
    {
        $sql = <<<SQL
            INSERT INTO matches (`red_team_id`, `blue_team_id`, `red_score`, `blue_score`)
            VALUES (:redTeamId, :blueTeamId, :redScore, :blueScore)
SQL;


        $this->getConnection()->executeUpdate($sql, [
            'redTeamId' => $redTeamId,
            'blueTeamId' => $blueTeamId,
            'redScore' => $redScore,
            'blueScore' => $blueScore
        ]);

        return $this->getConnection()->lastInsertId();
    }

    public function getHistory($order = 'DESC')
    {
        $sql = <<<SQL
            SELECT
                matches.id as id,
                red_goalkeeper.id as red_goalkeeper_id,
                red_goalkeeper.name as red_goalkeeper_name,
                red_goalkeeper_rating.diff as red_goalkeeper_rating_diff,
                red_forward.id as red_forward_id,
                red_forward.name as red_forward_name,
                red_forward_rating.diff as red_forward_rating_diff,
                blue_goalkeeper.id as blue_goalkeeper_id,
                blue_goalkeeper.name as blue_goalkeeper_name,
                blue_goalkeeper_rating.diff as blue_goalkeeper_rating_diff,
                blue_forward.id as blue_forward_id,
                blue_forward.name as blue_forward_name,
                blue_forward_rating.diff as blue_forward_rating_diff,
                red_team.id as red_team_id,
                blue_team.id as blue_team_id,
                red_score, blue_score, date,
                DATE_FORMAT(date, '%y-%m-%d') as day,
                DATE_FORMAT(date, '%H:%i') as time
            FROM matches
            JOIN teams as red_team ON red_team.id = red_team_id
            JOIN teams as blue_team ON blue_team.id = blue_team_id
            JOIN players as red_goalkeeper ON red_goalkeeper.id = red_team.goalkeeper_id
            JOIN players as red_forward ON red_forward.id = red_team.forward_id
            JOIN players as blue_goalkeeper ON blue_goalkeeper.id = blue_team.goalkeeper_id
            JOIN players as blue_forward ON blue_forward.id = blue_team.forward_id
            JOIN player_rating_log as red_goalkeeper_rating ON red_goalkeeper_rating.match_id = matches.id AND red_goalkeeper_rating.player_id = red_goalkeeper.id
            JOIN player_rating_log as red_forward_rating ON red_forward_rating.match_id = matches.id AND red_forward_rating.player_id = red_forward.id
            JOIN player_rating_log as blue_goalkeeper_rating ON blue_goalkeeper_rating.match_id = matches.id AND blue_goalkeeper_rating.player_id = blue_goalkeeper.id
            JOIN player_rating_log as blue_forward_rating ON blue_forward_rating.match_id = matches.id AND blue_forward_rating.player_id = blue_forward.id
            ORDER BY date {$order}
SQL;
        return $this->getConnection()->fetchAll($sql);
    }

    /**
     * @return DaysMetricsResult
     */
    public function getDaysMetrics()
    {
        $sql = <<<SQL
            SELECT
            DATE_FORMAT(date, '%y-%m-%d') as date,
            SUBSTRING(DATE_FORMAT(date, '%W'), 1, 3) as day,
            SUM(red_score) + SUM(blue_score) as goals,
            COUNT(id) as matches
            FROM matches GROUP BY DATE_FORMAT(date, '%y-%m-%d')
SQL;
        return new DaysMetricsResult($this->getConnection()->fetchAll($sql));
    }
}