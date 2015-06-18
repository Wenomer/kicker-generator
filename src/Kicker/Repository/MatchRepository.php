<?php

namespace Kicker\Repository;

class MatchRepository extends Repository
{
    static $table = 'matches';

    public function save($redTeamId, $blueTeamId, $redScore, $blueScore)
    {
        $this->db->executeUpdate("INSERT INTO matches (`red_team_id`, `blue_team_id`, `red_score`, `blue_score`)
                                  VALUES (:redTeamId, :blueTeamId, :redScore, :blueScore)", [
            'redTeamId' => $redTeamId,
            'blueTeamId' => $blueTeamId,
            'redScore' => $redScore,
            'blueScore' => $blueScore
        ]);

        return $this->db->lastInsertId();
    }

    public function getHistory($order = 'DESC')
    {
        $sql = <<<SQL
            SELECT
                matches.id as id,
                red_goalkeeper.id as red_goalkeeper_id,
                red_goalkeeper.name as red_goalkeeper_name,
                red_forward.id as red_forward_id,
                red_forward.name as red_forward_name,
                blue_goalkeeper.id as blue_goalkeeper_id,
                blue_goalkeeper.name as blue_goalkeeper_name,
                blue_forward.id as blue_forward_id,
                blue_forward.name as blue_forward_name,
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
            ORDER BY date {$order}
SQL;
        return $this->db->fetchAll($sql);
    }
}