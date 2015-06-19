<?php

namespace Kicker\Controller;

class FrontendController extends Controller
{
    public function tournamentAction()
    {
        return $this->app['twig']->render('tournament.html.twig', [
            'target' => 'tournament',
            'players' => $this->app['repository.player']->fetchAll()
        ]);
    }

    public function manualMatchAction()
    {
        return $this->app['twig']->render('manualMatch.html.twig', [
            'target' => 'manual-match',
            'players' => $this->app['repository.player']->fetchAll()
        ]);
    }

    public function statisticsAction()
    {
        return $this->app['twig']->render('statistics.html.twig', [
            'target' => 'statistics'
        ]);
    }

    public function historyAction()
    {
        $history = $this->app['repository.match']->getHistory();
        $grouppedHistory = [];

        foreach ($history as $match) {
            if (!isset($grouppedHistory[$match['day']])) {
                $grouppedHistory[$match['day']] = [];
            }

            $grouppedHistory[$match['day']][] = $match;
        }

        return $this->app['twig']->render('history.html.twig', [
            'target' => 'history',
            'players' => $this->app['repository.player']->fetchAll(),
            'history' => $grouppedHistory,
        ]);
    }
}