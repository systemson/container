<?php

require_once 'vendor/autoload.php';

use Lavoiesl\PhpBenchmark\Benchmark;
use Amber\Container\Container;
use Amber\Cache\Driver\SimpleCache as Cache;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\View;

$benchmark = new Benchmark();

$app = new Container();
$cache = new Cache(getcwd() . '\tmp\cache\services');
$app->setCache($cache);

$benchmark->add('app', function () use ($app) {
    $app->clear();
    $app->bind(Model::class);
    $app->bind(View::class);

    $service = $app->register(Controller::class)
    ->setArguments(['optional' => 2])
    ->afterConstruct('setId', 53);

    $controller = $app->get(Controller::class);
    $app->drop();
});

$benchmark->add('cached', function () use ($app) {
    $app->clear();
    $app->pick();

    $app->bind(Model::class);
    $app->bind(View::class);

    $service = $app->register(Controller::class)
    ->setArguments(['optional' => 2])
    ->afterConstruct('setId', 53);

    $controller = $app->get(Controller::class);
    $app->drop();
});

//$benchmark->guessCount(10);
//$benchmark->setCount(10);
$benchmark->run();

$app->clear();
$app->getCache()->clear();
