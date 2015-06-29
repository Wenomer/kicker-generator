<?php

namespace Kicker\Controller\Api;

use Kicker\Controller\Controller;
use Kicker\Http\SuccessJsonResponse;

class MetricsController extends Controller
{
    public function matchesAction()
    {
        $metrics = $this->getMatchRepository()->getDaysMetrics();
        return new SuccessJsonResponse($metrics->getMatchesCount());
    }

    public function gameDaysAction()
    {
        $metrics = $this->getMatchRepository()->getDaysMetrics();
        return new SuccessJsonResponse($metrics->getDaysCount());
    }

    public function goalsAction()
    {
        $metrics = $this->getMatchRepository()->getDaysMetrics();
        return new SuccessJsonResponse($metrics->getGoalsCount());
    }

    public function maxMatchesAction()
    {
        $metrics = $this->getMatchRepository()->getDaysMetrics();
        return new SuccessJsonResponse($metrics->getMaxMatches());
    }

    public function avgMatchesAction()
    {
        $metrics = $this->getMatchRepository()->getDaysMetrics();
        return new SuccessJsonResponse($metrics->getAvgMatches());
    }

    public function popularDayAction()
    {
        $metrics = $this->getMatchRepository()->getDaysMetrics();
        return new SuccessJsonResponse($metrics->getPopularDay());
    }
}