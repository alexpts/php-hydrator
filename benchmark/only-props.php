<?php

use Blackfire\Client;
use Blackfire\Profile\Configuration;
use PTS\Hydrator\HydratorService;

require_once __DIR__  .'/../vendor/autoload.php';
require_once 'UserModel.php';

$iterations = $argv[1] ?? 1000;
$blackfire = $argv[2] ?? false;
$iterations++;

$service = new HydratorService;
$normalizer = new \PTS\Hydrator\Normalizer;
$faker = \Faker\Factory::create();

$dto =  [
    'id' => $faker->randomDigit,
    'creAt' => $faker->unixTime(),
    'name' => $faker->name,
    'login' => $faker->name,
    'active' => $faker->boolean,
    'email' => $faker->email,
];

$rules = [
    'id' => [],
    'creAt' => [],
    'name' => [],
    'login' => [],
    'active' => [],
    'email' => [],
];
$rules = $normalizer->normalize($rules);


if ($blackfire) {
    $client = new Client;
    $probe = $client->createProbe(new Configuration);
}

$startTime = microtime(true);

$hydrator = $service->getHydrator();
$extractor = $service->getExtractor();

while ($iterations--) {
    $model = $hydrator->hydrate($dto, UserModel::class, $rules);
    $newDto = $extractor->extract($model, $rules);
}

$diff = (microtime(true) - $startTime) * 1000;
echo sprintf('%2.3f ms', $diff);
echo "\n" . memory_get_peak_usage()/1024;

if ($blackfire) {
    $client->endProbe($probe);
}
