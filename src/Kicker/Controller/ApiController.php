<?php

namespace Kicker\Controller;

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

        return json_encode(['success' => true]);
    }

    public function teamStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return json_encode(['rows' => $this->getTeamRepository()->getStatistics($sort, $order)]);
    }

    public function playerStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return json_encode(['rows' => $this->getPlayerRepository()->getStatistics($sort, $order)]);
    }

    public function squadStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return json_encode(['rows' => $this->getSquadRepository()->getStatistics($sort, $order)]);
    }

    public function colorStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return json_encode(['rows' => $this->app['repository.team']->getColorStatistics($sort, $order)]);
    }

    public function historyAction()
    {
        return json_encode(['rows' => $this->app['repository.match']->getHistory()]);
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

        return 'DONE';
    }

    public function ratingLogAction()
    {
        $logs = $this->app['repository.player_rating']->getLog();
        $players = $this->app['repository.player']->fetchAll();
        $matches = $this->app['repository.match']->fetchAll();
        $chartData = [];
        $dataMatches = [];

        foreach ($matches as $match) {
            $dataMatches[$match['id']] = null;
        }

        foreach ($players as $player) {
            $chartData[$player['id']] = ['name' => $player['name'], 'data' => $dataMatches];
        }

        foreach ($logs as $log) {
            $chartData[$log['player_id']]['data'][$log['match_id']] = floatval($log['rating']);
        }

        foreach ($players as $player) {
            $chartData[$player['id']]['data'] = array_values($chartData[$player['id']]['data']);
        }

        return json_encode(array_values($chartData));
    }

    public function probabilityAction(Request $request)
    {
        $match = $request->get('match');

        $redTeamId = $this->getTeamRepository()->getOrCreateId($match['red_goalkeeper_id'], $match['red_forward_id']);
        $blueTeamId = $this->getTeamRepository()->getOrCreateId($match['blue_goalkeeper_id'], $match['blue_forward_id']);

        return json_encode($this->getTeamRepository()->getWinProbability($redTeamId, $blueTeamId));
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