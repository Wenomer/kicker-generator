<?php

namespace Kicker\Repository;

class Repository
{
    protected $db;

    static $table;

    public function __construct($db) {
        $this->db = $db;
    }

    public function fetchAll()
    {
        return $this->db->fetchAll("SELECT * FROM " . static::$table);
    }
}