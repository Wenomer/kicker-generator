<?php

namespace Kicker\Repository;

use Doctrine\DBAL\Connection;
use Kicker\Rating\Elo;

abstract class Repository
{
    /**
     * @var Connection
     */
    private $db;
    /**
     * @var Elo
     */
    protected $elo;

    static $table;

    public function __construct($db, $elo) {
        $this->db = $db;
        $this->elo = $elo;
    }

    public function fetchAll($limit = null)
    {
        $limit = $limit ? "LIMIT " . intval($limit) : '';
        return $this->db->fetchAll("SELECT * FROM " . static::$table . " " . $limit);
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->db;
    }
}