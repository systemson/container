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

	$key = 'key';
	$string = 'string';
	$number = 1;
	$array = [1, 2, 3, 4, 5];
	$class = Model::class;
	$object = new $class();

	$app->bindMultiple([
		$key => $string,
		'number' => $number,
		'array' => $array,
		'class' => $class,
		'object' => $object
	]);

	$app->bind(Model::class);
	$app->bind(View::class);

	$service = $app->register(Controller::class)
	->setArguments(['optional' => 2])
	->afterConstruct('setId', 53);

	$app->get(Controller::class);
	$app->get($key);
	$app->get('number');
	$app->get('array');
	$app->get('class');
	$app->get('object');
	$app->drop();
	$app->clear();
});

$benchmark->add('cached', function () use ($app) {
	$app->clear();
	$app->pick();

	$key = 'key';
	$string = 'string';
	$number = 1;
	$array = [1, 2, 3, 4, 5];
	$class = Model::class;
	$object = new $class();

	$app->bindMultiple([
		$key => $string,
		'number' => $number,
		'array' => $array,
		'class' => $class,
		'object' => $object
	]);

	$app->bind(Model::class);
	$app->bind(View::class);

	$service = $app->register(Controller::class)
	->setArguments(['optional' => 2])
	->afterConstruct('setId', 53);

	$app->get(Controller::class);
	$app->get($key);
	$app->get('number');
	$app->get('array');
	$app->get('class');
	$app->get('object');
	$app->drop();
	$app->clear();
});

//$benchmark->guessCount(10);
//$benchmark->setCount(10);
$benchmark->run();

$app->clear();
$app->getCache()->clear();
