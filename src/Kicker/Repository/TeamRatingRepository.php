<?php

namespace Kicker\Repository;


class TeamRatingRepository extends RatingRepository
{
    static $table = 'team_rating_log';
    static $participantFieldName = 'team_id';

    public function update($match, ParticipantRepository $teamRepository, ParticipantRepository $supportRepository = null)
    {
        $redTeamId = $match['red_team_id'];
        $blueTeamId = $match['blue_team_id'];

        $teamRatings = $teamRepository->getRating([$redTeamId, $blueTeamId]);

        $teams = [
            [
                'id' => $redTeamId,
                'old' => $teamRatings[$redTeamId],
                'opponent' => $teamRatings[$blueTeamId],
                'score' => $match['red_score'] > $match['blue_score'] ? 1 : 0
            ], [
                'id' => $blueTeamId,
                'old' => $teamRatings[$blueTeamId],
                'opponent' => $teamRatings[$redTeamId],
                'score' => $match['blue_score'] > $match['red_score'] ? 1 : 0
            ],
        ];

        $this->saveRating($teams, $match['id'], $teamRepository);
    }
}