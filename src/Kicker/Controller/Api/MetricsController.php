<?php

namespace Kicker\Controller\Api;

use Kicker\Controller\Controller;
use Kicker\Http\SuccessJsonResponse;

class MetricsController extends Controller
{
    public function matchesAction()
    {
        return new SuccessJsonResponse(count($this->getMatchRepository()->fetchAll()));
    }

    public function gameDaysAction()
    {
        $metrics = $this->getMatchRepository()->getMetrics();
        return new SuccessJsonResponse($metrics['count']);
    }

    public function goalsAction()
    {
        $metrics = $this->getMatchRepository()->getMetrics();
        return new SuccessJsonResponse($metrics['goals']);
    }
}