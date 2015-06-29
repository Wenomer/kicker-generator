<?php

namespace Kicker\Repository;

class DaysMetricsResult extends Repository
{

    /**
     * @var array
     */
    private $metrics;

    public function __construct($metrics)
    {
        $this->metrics = $metrics;
    }

    public function getMatchesCount()
    {
        return $this->sum('matches');
    }

    public function getGoalsCount()
    {
        return $this->sum('goals');
    }

    public function getDaysCount()
    {
        return count($this->metrics);
    }

    public function getMaxMatches()
    {
        $maxRow = $this->max('matches');
        return $maxRow['matches'];
    }

    public function getAvgMatches()
    {
        return round($this->getMatchesCount() / $this->getDaysCount());
    }

    public function getPopularDay()
    {
        $maxRow = $this->max('matches');
        return $maxRow['day'];
    }

    private function sum($column)
    {
       $sum = 0;

       foreach ($this->metrics as $metric) {
           $sum += $metric[$column];
       }

       return $sum;
    }

    private function max($column)
    {
        $max = 0;
        $row = null;

        foreach ($this->metrics as $metric) {
            if ($metric[$column] > $max) {
                $max = $metric[$column];
                $row = $metric;
            }
        }

        return $row;
    }
}