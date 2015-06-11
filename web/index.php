<?php
$loader = require_once __DIR__ . '/../vendor/autoload.php';
//$loader->add('Kicker', __DIR__.'/../src');

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

$app['frontend.controller'] = $app->share(function() use ($app) {
    return new \Kicker\Controller\FrontendController($app);
});

$db = $app['db'];

$app->get('/', 'frontend.controller:generationAction');

//$app->get('/', function() use ($app, $db) {
//    $players = $db->fetchAll('SELECT * FROM players');
//    return $app['twig']->render('generator.html.twig', ['players' => $players]);
//});

//$app->get('/{stockcode}', function (Silex\Application $app, $stockcode) use ($toys) {
//    if (!isset($toys[$stockcode])) {
//        $app->abort(404, "Stockcode {$stockcode} does not exist.");
//    }
//    return json_encode($toys[$stockcode]);
//});

$app->run();