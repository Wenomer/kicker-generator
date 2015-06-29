<?php
namespace Kicker\Repository;

class ParticipantRepository extends Repository
{
    public function getRating(array $members)
    {
        $rating = [];
        $members = '"' . implode('","', $members) . '"';
        $table = static::$table;

        $sql = <<<SQL
          SELECT id, rating
          FROM {$table}
          WHERE id IN ({$members})
SQL;

        $scores =  $this->getConnection()->fetchAll($sql);

        foreach ($scores as $score) {
            $rating[$score['id']] = $score['rating'];
        }

        return $rating;
    }

    public function resetRating()
    {
        $this->getConnection()->update(static::$table, ['rating' => 0], [1 => 1]);
    }

    public function saveRating($id, $rating)
    {
        $this->getConnection()->update(static::$table, ['rating' => $rating], ['id' => $id]);
    }
}