<?php

namespace Kicker\Rating;

class Elo
{
    const LEAGUE_HIGH = 2400;
    const LEAGUE_LOW = 1000;

    const COEFF_LOW = 40;
    const COEFF_MEDIUM = 20;
    const COEFF_HIGH = 10;

    public function calculate($oldRating, $score, $commandRating, $opponentRating)
    {
        $expectedValue = 1 / (1 + pow(10, ($opponentRating - $commandRating) / 400));

        return round($oldRating + $this->coefficient($oldRating) * ($score - $expectedValue), 2);
    }

    private function coefficient($rating)
    {
        switch(true) {
            case ($rating < self::LEAGUE_LOW): return self::COEFF_LOW;
            case ($rating < self::LEAGUE_HIGH): return self::COEFF_MEDIUM;
            default: return self::COEFF_HIGH;
        }
    }
}