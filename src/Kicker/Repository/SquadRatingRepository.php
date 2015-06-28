<?php

namespace Kicker\Repository;


class SquadRatingRepository extends RatingRepository
{
    static $table = 'squad_rating_log';
    static $participantFieldName = 'squad_id';

    public function update($match, ParticipantRepository $squadRepository, ParticipantRepository $supportRepository = null)
    {
        /** @var SquadRepository $squadRepository */
        $redSquadId = $squadRepository->composeId($match['red_goalkeeper_id'], $match['red_forward_id']);
        $blueSquadId = $squadRepository->composeId($match['blue_goalkeeper_id'], $match['blue_forward_id']);

        $squadRatings = $squadRepository->getRating([$redSquadId, $blueSquadId]);

        $squads = [
            [
                'id' => $redSquadId,
                'old' => $squadRatings[$redSquadId],
                'opponent' => $squadRatings[$blueSquadId],
                'score' => $match['red_score'] > $match['blue_score'] ? 1 : 0
            ], [
                'id' => $blueSquadId,
                'old' => $squadRatings[$blueSquadId],
                'opponent' => $squadRatings[$redSquadId],
                'score' => $match['blue_score'] > $match['red_score'] ? 1 : 0
            ],
        ];

        $this->saveRating($squads, $match['id'], $squadRepository);
    }
}