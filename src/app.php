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

$app['controller.frontend'] = $app->share(function() use ($app) {
    return new \Kicker\Controller\FrontendController($app);
});

$app['controller.api'] = $app->share(function() use ($app) {
    return new \Kicker\Controller\ApiController($app);
});

$app->get('/', 'controller.frontend:manualMatchAction');
$app->get('/tournament', 'controller.frontend:tournamentAction');
$app->get('/statistics', 'controller.frontend:statisticsAction');
$app->get('/history', 'controller.frontend:historyAction');

$app->post('/api/match', 'controller.api:saveMatchAction');
$app->get('/api/history', 'controller.api:historyAction');
$app->get('/api/statistics/team', 'controller.api:teamStatisticsAction');
$app->get('/api/statistics/player', 'controller.api:playerStatisticsAction');
$app->get('/api/statistics/squad', 'controller.api:squadStatisticsAction');
$app->get('/api/statistics/color', 'controller.api:colorStatisticsAction');
$app->get('/api/statistics/rating-log', 'controller.api:ratingLogAction');
$app->get('/api/probability', 'controller.api:probabilityAction');
$app->get('/api/calculate-rating', 'controller.api:calculateRatingAction');

return $app;