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

    public function historyAction(Request $request)
    {
        return json_encode(['rows' => $this->app['repository.match']->getHistory()]);
    }

    public function calculateRatingAction()
    {
        $this->app['repository.player']->calculateRatings($this->app['repository.match']->getHistory('asc'));
        return 'DONE';
    }
}