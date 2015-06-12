<?php

namespace Kicker\Controller;

class Controller
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
}