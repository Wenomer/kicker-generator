<?php

namespace Kicker\Repository;


class PlayerRatingRepository extends RatingRepository
{
    static $table = 'player_rating_log';
    static $participantFieldName = 'player_id';

    public function getLog()
    {
        $sql = <<<SQL
            SELECT players.name as player_name, players.id as player_id, player_rating_log.rating as rating, match_id
            FROM player_rating_log
            JOIN players ON players.id = player_id
            JOIN matches ON matches.id = match_id
            ORDER BY matches.date ASC
SQL;

        return $this->getConnection()->fetchAll($sql);
    }

    public function update($match, ParticipantRepository $playerRepository, ParticipantRepository $teamRepository = null)
    {
        $redGId = $match['red_goalkeeper_id'];
        $redFId = $match['red_forward_id'];
        $blueGId = $match['blue_goalkeeper_id'];
        $blueFId = $match['blue_forward_id'];

        $redTeamId = $match['red_team_id'];
        $blueTeamId = $match['blue_team_id'];

        $playerRatings = $playerRepository->getRating([$redGId, $redFId, $blueGId, $blueFId]);
        $teamRatings = $teamRepository->getRating([$redTeamId, $blueTeamId]);

        $players = [
            [
                'id' => $redGId,
                'old' => $playerRatings[$redGId],
                'opponent' => $teamRatings[$blueTeamId],
                'command' => $teamRatings[$redTeamId],
                'score' => $match['red_score'] > $match['blue_score'] ? 1 : 0
            ],[
                'id' => $redFId,
                'old' => $playerRatings[$redFId],
                'opponent' => $teamRatings[$blueTeamId],
                'command' => $teamRatings[$redTeamId],
                'score' => $match['red_score'] > $match['blue_score'] ? 1 : 0
            ],[
                'id' => $blueGId,
                'old' => $playerRatings[$blueGId],
                'opponent' => $teamRatings[$redTeamId],
                'command' => $teamRatings[$blueTeamId],
                'score' => $match['blue_score'] > $match['red_score'] ? 1 : 0
            ],[
                'id' => $blueFId,
                'old' => $playerRatings[$blueFId],
                'opponent' => $teamRatings[$redTeamId],
                'command' => $teamRatings[$blueTeamId],
                'score' => $match['blue_score'] > $match['red_score'] ? 1 : 0
            ],
        ];

        $this->saveRating($players, $match['id'], $playerRepository);
    }

    public function getWeekRatingChange($sort, $order, $date)
    {
        $sql = <<<SQL
            SELECT
              SUM(diff) as rating_diff,
              players.name as player,
              matches.date as date
            FROM player_rating_log
            JOIN matches ON matches.id = player_rating_log.`match_id`
            JOIN players ON player_rating_log.player_id = players.id
            WHERE date_format(matches.date, '%u %Y') = date_format('2015-08-13', '%u %Y')
            GROUP BY player_rating_log.player_id
            ORDER BY {$sort} {$order}
SQL;

        return $this->getConnection()->fetchAll($sql, ['date' => $date]);
    }
}