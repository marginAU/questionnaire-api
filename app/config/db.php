<?php

return [
    'class'        => \yii\db\Connection::class,
    'dsn'          => 'mysql:host=localhost;dbname=questionnaire',
    'username'     => 'root',
    'password'     => '?VF!:e2#we/S',
    'charset'      => 'utf8mb4',
    'commandClass' => \app\components\Command::class,
    'attributes'   => [
        PDO::ATTR_TIMEOUT => 5,
    ],
    'slaves'       => [
        [
            'dsn' => 'mysql:host=localhost;dbname=questionnaire',
        ],
    ],
    'slaveConfig'  => [
        'username'   => 'root',
        'password'   => '?VF!:e2#we/S',
        'charset'    => 'utf8mb4',
        'attributes' => [
            PDO::ATTR_TIMEOUT => 5,
        ],
    ],
];