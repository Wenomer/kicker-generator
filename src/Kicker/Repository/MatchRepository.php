<?php

namespace Kicker\Repository;

class MatchRepository extends Repository
{
    public function save($redTeamId, $blueTeamId, $redScore, $blueScore)
    {
        $this->db->executeUpdate("INSERT INTO matches (`red_team_id`, `blue_team_id`, `red_score`, `blue_score`)
                                  VALUES (:redTeamId, :blueTeamId, :redScore, :blueScore)", [
            'redTeamId' => $redTeamId,
            'blueTeamId' => $blueTeamId,
            'redScore' => $redScore,
            'blueScore' => $blueScore
        ]);
    }

    public function getHistory()
    {
        $sql = <<<SQL
            SELECT red_goalkeeper.name as red_goalkeeper_name, red_forward.name as red_forward_name,
            blue_goalkeeper.name as blue_goalkeeper_name, blue_forward.name as blue_forward_name,
            red_score, blue_score, date
            FROM matches
            JOIN teams as red_team ON red_team.id = red_team_id
            JOIN teams as blue_team ON blue_team.id = blue_team_id
            JOIN players as red_goalkeeper ON red_goalkeeper.id = red_team.goalkeeper_id
            JOIN players as red_forward ON red_forward.id = red_team.forward_id
            JOIN players as blue_goalkeeper ON blue_goalkeeper.id = blue_team.goalkeeper_id
            JOIN players as blue_forward ON blue_forward.id = blue_team.forward_id
            ORDER BY date DESC
SQL;
        return $this->db->fetchAll($sql);
    }
}