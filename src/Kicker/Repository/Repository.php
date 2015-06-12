<?php

namespace Kicker\Repository;

class Repository
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
}