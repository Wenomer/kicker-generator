<?php

namespace Kicker\Controller\Api;

use Kicker\Controller\Controller;
use Kicker\Http\SuccessJsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function saveMatchAction(Request $request)
    {
        $match = $request->get('match');

        $redTeamId = $this->getTeamRepository()->getOrCreateId($match['red_goalkeeper_id'], $match['red_forward_id']);
        $blueTeamId = $this->getTeamRepository()->getOrCreateId($match['blue_goalkeeper_id'], $match['blue_forward_id']);
        $this->getSquadRepository()->save($match['red_goalkeeper_id'], $match['red_forward_id']);
        $this->getSquadRepository()->save($match['blue_goalkeeper_id'], $match['blue_forward_id']);

        $match['id'] = $this->getMatchRepository()->save($redTeamId, $blueTeamId, $match['red_score'], $match['blue_score']);
        $match['red_team_id'] = $redTeamId;
        $match['blue_team_id'] = $blueTeamId;

        $this->getPlayerRatingRepository()->update($match, $this->getPlayerRepository(), $this->getTeamRepository());
        $this->getTeamRatingRepository()->update($match, $this->getTeamRepository());
        $this->getSquadRatingRepository()->update($match, $this->getSquadRepository());

        return new SuccessJsonResponse();
    }

    public function calculateRatingAction()
    {
        $matches = $this->getMatchRepository()->getHistory('asc');
        $this->getSquadRepository()->generate($this->getTeamRepository()->fetchAll());
        $this->resetRatings();

        foreach ($matches as $match) {
            $this->getPlayerRatingRepository()->update($match, $this->getPlayerRepository(), $this->getTeamRepository());
            $this->getTeamRatingRepository()->update($match, $this->getTeamRepository());
            $this->getSquadRatingRepository()->update($match, $this->getSquadRepository());
        }

        return new SuccessJsonResponse('');
    }

    public function probabilityAction(Request $request)
    {
        $match = $request->get('match');

        $redTeamId = $this->getTeamRepository()->getOrCreateId($match['red_goalkeeper_id'], $match['red_forward_id']);
        $blueTeamId = $this->getTeamRepository()->getOrCreateId($match['blue_goalkeeper_id'], $match['blue_forward_id']);

        return new SuccessJsonResponse($this->getTeamRepository()->getWinProbability($redTeamId, $blueTeamId));
    }

    private function resetRatings()
    {
        $this->getPlayerRepository()->resetRating();
        $this->getPlayerRatingRepository()->reset();
        $this->getTeamRepository()->resetRating();
        $this->getTeamRatingRepository()->reset();
        $this->getSquadRepository()->resetRating();
        $this->getSquadRatingRepository()->reset();
    }
}