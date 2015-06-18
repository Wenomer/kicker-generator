<?php

namespace Kicker\Repository;

use Kicker\Rating\Elo;

class PlayerRepository extends Repository
{
    static $table = 'players';

    public function getStatistics($sort, $order)
    {
        $sql = <<<SQL
            SELECT p.name player, p.rating as rating,
            SUM(IF(p.id = t.goalkeeper_id AND t.id = m.red_team_id, m.blue_score, IF(p.id = t.goalkeeper_id AND t.id = m.blue_team_id, m.red_score, 0))) as passed_goals,
            ROUND(SUM(IF(p.id = t.goalkeeper_id AND t.id = m.red_team_id, m.blue_score, IF(p.id = t.goalkeeper_id AND t.id = m.blue_team_id, m.red_score, 0))) / SUM(IF(p.id = t.goalkeeper_id, 1, 0)), 2) as avg_passed_goals,
            SUM(IF(p.id = t.forward_id AND t.id = m.red_team_id, m.red_score, IF(p.id = t.forward_id AND t.id = m.blue_team_id, m.blue_score, 0))) as goals,
            ROUND(SUM(IF(p.id = t.forward_id AND t.id = m.red_team_id, m.red_score, IF(p.id = t.forward_id AND t.id = m.blue_team_id, m.blue_score, 0))) / SUM(IF(p.id = t.forward_id, 1, 0)), 2) as avg_goals,
            ROUND((SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) / COUNT(m.id)) * 100 ) as win_percent,

            SUM((t.id = m.red_team_id AND m.red_score > m.blue_score) OR (t.id = m.blue_team_id AND m.blue_score > m.red_score)) as wins,
            COUNT(m.id) as matches
            FROM players p
            JOIN teams t ON p.id = t.forward_id OR p.id = t.goalkeeper_id
            JOIN matches m ON t.id = m.red_team_id OR t.id = m.blue_team_id

            GROUP BY p.id
            ORDER BY {$sort} {$order}
            limit 10
SQL;

        return $this->db->fetchAll($sql, [':sort' => $sort, ':order' => $order]);
    }

    public function resetRating()
    {
        $this->db->executeUpdate("UPDATE players SET rating = 0");
        $this->db->executeUpdate("UPDATE teams SET rating = 0");
        $this->db->executeUpdate("TRUNCATE TABLE player_rating_log");
        $this->db->executeUpdate("TRUNCATE TABLE team_rating_log");
    }

    public function calculateRatings($matches)
    {
        $this->resetRating();

        foreach ($matches as $match) {
            $this->updateRating($match);
        }
    }

    public function updateRating($match)
    {
        $playerRatings = $this->getPlayersRating($match['red_goalkeeper_id'], $match['red_forward_id'], $match['blue_goalkeeper_id'], $match['blue_forward_id']);
        $teamRatings = $this->getTeamsRating($match['red_team_id'], $match['blue_team_id']);

        $newPlayerRating = $this->calculateRating(
            $playerRatings[$match['red_goalkeeper_id']],
            $match['red_score'] > $match['blue_score'] ? 1 : 0,
            $teamRatings[$match['red_team_id']],
            $teamRatings[$match['blue_team_id']]
        );
        $this->savePlayerRating($match['red_goalkeeper_id'], $match['id'], $newPlayerRating);

        $newPlayerRating = $this->calculateRating(
            $playerRatings[$match['red_forward_id']],
            $match['red_score'] > $match['blue_score'] ? 1 : 0,
            $teamRatings[$match['red_team_id']],
            $teamRatings[$match['blue_team_id']]
        );
        $this->savePlayerRating($match['red_forward_id'], $match['id'], $newPlayerRating);

        $newPlayerRating = $this->calculateRating(
            $playerRatings[$match['blue_goalkeeper_id']],
            $match['blue_score'] > $match['red_score'] ? 1 : 0,
            $teamRatings[$match['blue_team_id']],
            $teamRatings[$match['red_team_id']]
        );
        $this->savePlayerRating($match['blue_goalkeeper_id'], $match['id'], $newPlayerRating);

        $newPlayerRating = $this->calculateRating(
            $playerRatings[$match['blue_forward_id']],
            $match['blue_score'] > $match['red_score'] ? 1 : 0,
            $teamRatings[$match['blue_team_id']],
            $teamRatings[$match['red_team_id']]
        );

        $this->savePlayerRating($match['blue_forward_id'], $match['id'], $newPlayerRating);

        $newTeamRating = $this->calculateRating(
            $teamRatings[$match['red_team_id']],
            $match['red_score'] > $match['blue_score'] ? 1 : 0,
            $teamRatings[$match['red_team_id']],
            $teamRatings[$match['blue_team_id']]
        );
        $this->saveTeamRating($match['red_team_id'], $match['id'], $newTeamRating);

        $newTeamRating = $this->calculateRating(
            $teamRatings[$match['blue_team_id']],
            $match['blue_score'] > $match['red_score'] ? 1 : 0,
            $teamRatings[$match['blue_team_id']],
            $teamRatings[$match['red_team_id']]
        );

        $this->saveTeamRating($match['blue_team_id'], $match['id'], $newTeamRating);
    }

    private function calculateRating($oldRating, $score, $commandRating, $opponentRating)
    {
        $rating = new Elo();
        return $rating->calculate($oldRating, $score, $commandRating, $opponentRating);
    }

    private function savePlayerRating($playerId, $matchId, $rating) {
        $this->db->executeUpdate(<<<SQL
          UPDATE players
            SET rating = :rating
            WHERE id = :id
SQL
        , ['rating' => $rating, 'id' => $playerId]);

        $this->db->executeUpdate(<<<SQL
          INSERT INTO player_rating_log VALUES(:player, :match, :rating)
SQL
        , ['player' => $playerId, 'match' => $matchId, 'rating' => $rating]);
    }

    private function saveTeamRating($teamId, $matchId, $rating) {
        $this->db->executeUpdate(<<<SQL
          UPDATE teams
            SET rating = :rating
            WHERE id = :id
SQL
        , ['rating' => $rating, 'id' => $teamId]);

        $this->db->executeUpdate(<<<SQL
          INSERT INTO team_rating_log VALUES(:team, :match, :rating)
SQL
            , ['team' => $teamId, 'match' => $matchId, 'rating' => $rating]);
    }

    private function getPlayersRating($redGoalkeeperId, $redForwardId, $blueGoalkeeperId, $blueForwardId)
    {
        $rating = [];

        $scores =  $this->db->fetchAll(<<<SQL
          SELECT id, rating
          FROM players
          WHERE id IN ({$redGoalkeeperId}, {$redForwardId}, {$blueGoalkeeperId}, {$blueForwardId})
SQL
        );

        foreach ($scores as $score) {
            $rating[$score['id']] = $score['rating'];
        }

        return $rating;
    }

    private function getTeamsRating($redTeamId, $blueTeamId)
    {
        $rating = [];

        $scores =  $this->db->fetchAll(<<<SQL
          SELECT id, rating
          FROM teams
          WHERE id IN ({$redTeamId}, {$blueTeamId})
SQL
        );

        foreach ($scores as $score) {
            $rating[$score['id']] = $score['rating'];
        }

        return $rating;
    }
}