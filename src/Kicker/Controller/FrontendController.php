<?php

namespace Kicker\Controller;

class FrontendController extends Controller
{
    public function tournamentAction()
    {
        return $this->getTwig()->render('tournament.html.twig', [
            'target' => 'tournament',
            'players' => $this->getPlayerRepository()->fetchAll()
        ]);
    }

    public function manualMatchAction()
    {
        return $this->getTwig()->render('manualMatch.html.twig', [
            'target' => 'manual-match',
            'players' => $this->getPlayerRepository()->fetchAll()
        ]);
    }

    public function statisticsAction()
    {
        return $this->getTwig()->render('statistics.html.twig', [
            'target' => 'statistics'
        ]);
    }

    public function historyAction()
    {
        $history = $this->getMatchRepository()->getHistory();
        $groupedHistory = [];

        foreach ($history as $match) {
            if (!isset($groupedHistory[$match['day']])) {
                $groupedHistory[$match['day']] = [];
            }

            $groupedHistory[$match['day']][] = $match;
        }

        return $this->getTwig()->render('history.html.twig', [
            'target' => 'history',
            'players' => $this->getPlayerRepository()->fetchAll(),
            'history' => $groupedHistory,
        ]);
    }
}