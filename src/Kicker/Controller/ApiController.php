<?php

namespace Kicker\Controller;

use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function saveMatchAction(Request $request)
    {
        $match = $request->get('match');

        $redTeamId = $this->app['repository.team']->getOrCreateTeamId($match['red_goalkeeper_id'], $match['red_forward_id']);
        $blueTeamId = $this->app['repository.team']->getOrCreateTeamId($match['blue_goalkeeper_id'], $match['blue_forward_id']);

        $match['id'] = $this->app['repository.match']->save($redTeamId, $blueTeamId, $match['red_score'], $match['blue_score']);
        $match['red_team_id'] = $redTeamId;
        $match['blue_team_id'] = $blueTeamId;

        $this->app['repository.player']->updateRating($match);

        return json_encode(['success' => true]);
    }

    public function teamStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return json_encode(['rows' => $this->app['repository.team']->getStatistics($sort, $order)]);
    }

    public function playerStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return json_encode(['rows' => $this->app['repository.player']->getStatistics($sort, $order)]);
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
        $this->app['repository.player']->calculateRatings($this->app['repository.match']->getHistory('asc'));
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

        $redTeamId = $this->app['repository.team']->getOrCreateTeamId($match['red_goalkeeper_id'], $match['red_forward_id']);
        $blueTeamId = $this->app['repository.team']->getOrCreateTeamId($match['blue_goalkeeper_id'], $match['blue_forward_id']);

        return json_encode($this->app['repository.team']->getWinProbability($redTeamId, $blueTeamId));
    }
}