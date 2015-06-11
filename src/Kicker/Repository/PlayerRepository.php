<?php

namespace Kicker\Repository;

class PlayerRepository
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findAll()
    {
        return $this->db->fetchAll('SELECT * FROM players');
    }
}