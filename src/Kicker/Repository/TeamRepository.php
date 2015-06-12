<?php

namespace Kicker\Repository;

class TeamRepository extends Repository
{
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

    private function create($goalkeeperId, $forwardId)
    {
        $this->db->executeUpdate("INSERT INTO teams (`goalkeeper_id`, `forward_id`) VALUES (:goalkeeper, :forward)", [
            'goalkeeper' => $goalkeeperId,
            'forward' => $forwardId
        ]);

        return $this->db->lastInsertId();
    }
}