<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
// Please set to false in a production environment
$app['debug'] = true;

$toys = array(
    '00001'=> array(
        'name' => 'Racing Car',
        'quantity' => '53',
        'description' => '...',
        'image' => 'racing_car.jpg',
    ),
    '00002' => array(
        'name' => 'Raspberry Pi',
        'quantity' => '13',
        'description' => '...',
        'image' => 'raspberry_pi.jpg',
    ),
);

$app->get('/', function() use ($toys) {
    return json_encode($toys);
});

$app->get('/{stockcode}', function (Silex\Application $app, $stockcode) use ($toys) {
    if (!isset($toys[$stockcode])) {
        $app->abort(404, "Stockcode {$stockcode} does not exist.");
    }
    return json_encode($toys[$stockcode]);
});

$app->run();