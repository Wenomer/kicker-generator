<?php

namespace Kicker\Repository;

class PlayerRepository extends Repository
{
    public function findAll()
    {
        return $this->db->fetchAll('SELECT * FROM players');
    }
}