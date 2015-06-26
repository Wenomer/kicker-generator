<?php

namespace Kicker\Repository;

abstract class Repository
{
    protected $db;

    static $table;

    public function __construct($db) {
        $this->db = $db;
    }

    public function fetchAll($limit = null)
    {
        $limit = $limit ? "LIMIT " . intval($limit) : '';
        var_dump("SELECT * FROM " . static::$table . " " . $limit);
        return $this->db->fetchAll("SELECT * FROM " . static::$table . " " . $limit);
    }
}