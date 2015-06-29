<?php

namespace Kicker\Controller;

use Kicker\Repository\MatchRepository;
use Kicker\Repository\SquadRatingRepository;
use Kicker\Repository\SquadRepository;
use Kicker\Repository\TeamRepository;
use Kicker\Repository\TeamRatingRepository;
use Kicker\Repository\PlayerRepository;
use Kicker\Repository\PlayerRatingRepository;

class Controller
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @return PlayerRepository
     */
    public function getPlayerRepository()
    {
        return $this->app['repository.player'];
    }

    /**
     * @return PlayerRatingRepository
     */
    public function getPlayerRatingRepository()
    {
        return $this->app['repository.player_rating'];
    }

    /**
     * @return TeamRepository
     */
    public function getTeamRepository()
    {
        return $this->app['repository.team'];
    }

    /**
     * @return TeamRatingRepository
     */
    public function getTeamRatingRepository()
    {
        return $this->app['repository.team_rating'];
    }

    /**
     * @return MatchRepository
     */
    public function getMatchRepository()
    {
        return $this->app['repository.match'];
    }

    /**
     * @return SquadRepository
     */
    public function getSquadRepository()
    {
        return $this->app['repository.squad'];
    }

    /**
     * @return SquadRatingRepository
     */
    public function getSquadRatingRepository()
    {
        return $this->app['repository.squad_rating'];
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->app['twig'];
    }
}