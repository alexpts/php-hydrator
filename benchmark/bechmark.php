<?php

use PTS\Hydrator\HydratorService;

require_once __DIR__  .'/../vendor/autoload.php';
require_once 'UserModel.php';

$iterations = $argv[1] ?? 1000;
$iterations++;
$startTime = microtime(true);

$service = new HydratorService;

while ($iterations--) {
    $dto =  [
        'id' => 1,
        'creAt' => time(),
        'name' => 'Alex',
        'login' => 'login',
        'active' => true,
        'email' => 'some@cloud.net'
    ];

    $rules = [
        'id' => [],
        'creAt' => [],
        'name' => [
            'get' => 'getName',
            'set' => 'setName'
        ],
        'login' => [],
        'active' => [],
        'email' => [
            'pipe' => ['strtolower']
        ],
    ];

    $model = $service->hydrate($dto, UserModel::class, $rules);
    $newDto = $service->extract($model, $rules);
}

$diff = (microtime(true) - $startTime) * 1000;
echo sprintf('%2.3f ms', $diff);
echo "\n" . memory_get_peak_usage()/1024;