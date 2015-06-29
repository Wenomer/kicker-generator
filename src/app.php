<?php
$loader = require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
// Please set to false in a production environment
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array (
        'mysql' => array(
            'driver'    => 'pdo_mysql',
            'host'      => '127.0.0.1',
            'dbname'    => 'kicker',
            'user'      => 'root',
            'password'  => 'root',
            'charset'   => 'utf8mb4',
        )
    ),
));

$db = $app['db'];

$app['rating.calculator'] = $app->share(function() use ($db) {
    return new \Kicker\Rating\Elo();
});

//Repositories
$app['repository.player'] = $app->share(function() use ($app) {
    return new \Kicker\Repository\PlayerRepository($app['db'], $app['rating.calculator']);
});

$app['repository.team'] = $app->share(function() use ($app) {
    return new \Kicker\Repository\TeamRepository($app['db'], $app['rating.calculator']);
});

$app['repository.squad'] = $app->share(function() use ($app) {
    return new \Kicker\Repository\SquadRepository($app['db'], $app['rating.calculator']);
});

$app['repository.match'] = $app->share(function() use ($app) {
    return new \Kicker\Repository\MatchRepository($app['db'], $app['rating.calculator']);
});

$app['repository.player_rating'] = $app->share(function() use ($app) {
    return new \Kicker\Repository\PlayerRatingRepository($app['db'], $app['rating.calculator']);
});

$app['repository.team_rating'] = $app->share(function() use ($app) {
    return new \Kicker\Repository\TeamRatingRepository($app['db'], $app['rating.calculator']);
});

$app['repository.squad_rating'] = $app->share(function() use ($app) {
    return new \Kicker\Repository\SquadRatingRepository($app['db'], $app['rating.calculator']);
});

//Controllers

$app['controller.frontend'] = $app->share(function() use ($app) {
    return new \Kicker\Controller\FrontendController($app);
});

//API Controllers
$app['controller.api'] = $app->share(function() use ($app) {
    return new \Kicker\Controller\Api\ApiController($app);
});

$app['controller.api.statistics'] = $app->share(function() use ($app) {
    return new \Kicker\Controller\Api\StatisticsController($app);
});

$app['controller.api.metrics'] = $app->share(function() use ($app) {
    return new \Kicker\Controller\Api\MetricsController($app);
});

//Routes pages

$app->get('/', 'controller.frontend:manualMatchAction');
$app->get('/tournament', 'controller.frontend:tournamentAction');
$app->get('/statistics', 'controller.frontend:statisticsAction');
$app->get('/history', 'controller.frontend:historyAction');

//Routes Actions Api

$app->post('/api/match', 'controller.api:saveMatchAction');
$app->get('/api/history', 'controller.api:historyAction');
$app->get('/api/probability', 'controller.api:probabilityAction');
$app->get('/api/calculate-rating', 'controller.api:calculateRatingAction');

//Routes Statistics API

$app->get('/api/statistics/team', 'controller.api.statistics:teamStatisticsAction');
$app->get('/api/statistics/player', 'controller.api.statistics:playerStatisticsAction');
$app->get('/api/statistics/squad', 'controller.api.statistics:squadStatisticsAction');
$app->get('/api/statistics/color', 'controller.api.statistics:colorStatisticsAction');
$app->get('/api/statistics/rating-log', 'controller.api.statistics:ratingLogAction');

//Routes Metrics Api

$app->get('/api/metrics/matches', 'controller.api.metrics:matchesAction');
$app->get('/api/metrics/game-days', 'controller.api.metrics:gameDaysAction');
$app->get('/api/metrics/goals', 'controller.api.metrics:goalsAction');

return $app;