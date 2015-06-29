<?php

namespace Kicker\Controller\Api;

use Kicker\Controller\Controller;
use Kicker\Http\SuccessJsonResponse;
use Kicker\Http\TableJsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends Controller
{
    public function teamStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return new TableJsonResponse($this->getTeamRepository()->getStatistics($sort, $order));
    }

    public function playerStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return new TableJsonResponse($this->getPlayerRepository()->getStatistics($sort, $order));
    }

    public function squadStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return new TableJsonResponse($this->getSquadRepository()->getStatistics($sort, $order));
    }

    public function colorStatisticsAction(Request $request)
    {
        $sort = $request->get('sort');
        $order = $request->get('order');

        return new TableJsonResponse($this->getTeamRepository()->getColorStatistics($sort, $order));
    }

    public function historyAction()
    {
        return new TableJsonResponse($this->getMatchRepository()->getHistory());
    }

    public function ratingLogAction()
    {
        $logs = $this->getPlayerRatingRepository()->getLog();
        $players = $this->getPlayerRepository()->fetchAll();
        $matches = $this->getMatchRepository()->fetchAll();
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

        return new SuccessJsonResponse(array_values($chartData));
    }
}