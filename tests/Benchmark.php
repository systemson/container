<?php

require_once 'vendor/autoload.php';

use Amber\Container\Injector;
use Lavoiesl\PhpBenchmark\Benchmark;
use Tests\Example\Model;

$benchmark = new Benchmark();

$container = new Injector();

$configs = [
	'cache' => [
		'cache_driver' => 'apcu',
	],
];

$container->setConfig($configs);

$container->bind(Model::class);

declare(ticks=1);

$benchmark->add('get', function () use ($container) {
    $container->bind(Model::class);
    $container->get(Model::class);
    $container->unbind(Model::class);

    return $container;
});

$benchmark->add('mount', function () use ($container) {
    $container->mount(Model::class);

    return $container;
});

$container->drop();

$container->clear();

$benchmark->add('pick', function () use ($container) {
    $container->pick();
    $container->get(Model::class);

    return $container;
});

$benchmark->run();

$container->clear(true);
