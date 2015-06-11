<?php

namespace Kicker\Controller;

class FrontendController
{
    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

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
}