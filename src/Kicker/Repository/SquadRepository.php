<?php

namespace Kicker\Repository;

class SquadRepository extends ParticipantRepository
{
    static $table = 'squads';

    public function generate(array $teams)
    {
        if (!count($this->fetchAll())) {
            foreach ($teams as $team) {
                $this->save($team['goalkeeper_id'], $team['forward_id']);
            }
        }
    }

    public function save($player1Id, $player2Id)
    {
        $table = static::$table;

        $this->getConnection()->executeUpdate(<<<SQL
                    INSERT IGNORE INTO `{$table}` (`id`, `p1_id`, `p2_id`) VALUES (:squadId, :p1Id, :p2Id)
SQL
            , [
                'squadId' => $this->composeId($player1Id, $player2Id),
                'p1Id' => $player1Id,
                'p2Id' => $player2Id
            ]);
    }

    public function composeId($player1Id, $player2Id)
    {
        $key = [$player1Id, $player2Id];
        sort($key);

        return implode('_', $key);
    }

    public function getStatistics($sort, $order)
    {
        $sql = <<<SQL
            SELECT CONCAT(p1.name, ', ', p2.name) as squad, count(m.id) as matches, sq.rating as squad_rating,
            SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) as wins,
            ROUND((SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score))/ count(m.id)) * 100) as win_percent

            FROM squads sq
            JOIN teams t ON (t.forward_id = sq.p1_id AND t.goalkeeper_id = p2_id) OR (t.goalkeeper_id = sq.p1_id AND t.forward_id = p2_id)
            JOIN players p1 ON p1.id = sq.p1_id
            JOIN players p2 ON p2.id = sq.p2_id
            LEFT JOIN matches m ON m.red_team_id = t.id OR m.blue_team_id = t.id
            WHERE p1.is_active = 1 AND p2.is_active = 1

            GROUP BY sq.id
            ORDER BY {$sort} {$order}
            limit 10
SQL;

        return $this->getConnection()->fetchAll($sql);
    }
}