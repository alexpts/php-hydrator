<?php

return [
    'id' => [],
    'creAt' => [],
    'name' => [],
    'login' => [],
    'active' => [
        'pipe' => ['boolval']
    ],
    'email' => [],
    'refModel' => [
        'ref' => [
            'model' => \PTS\Hydrator\UserModel::class,
            'map' => 'dto'
        ]
    ],
    'refModels' => [
        'ref' => [
            'model' => \PTS\Hydrator\UserModel::class,
            'map' => 'dto',
            'collection' => true
        ]
    ],
];