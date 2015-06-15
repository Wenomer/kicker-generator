<?php

namespace Kicker\Controller;

class FrontendController extends Controller
{
    public function tournamentAction()
    {
        return $this->app['twig']->render('tournament.html.twig', [
            'target' => 'tournament',
            'players' => $this->app['repository.player']->findAll()
        ]);
    }

    public function manualMatchAction()
    {
        return $this->app['twig']->render('manualMatch.html.twig', [
            'target' => 'manual-match',
            'players' => $this->app['repository.player']->findAll()
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
        return $this->app['twig']->render('history.html.twig', [
            'target' => 'history',
        ]);
    }
}