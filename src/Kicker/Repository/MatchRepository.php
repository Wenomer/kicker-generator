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
}