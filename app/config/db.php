<?php

return [
    'class'        => \yii\db\Connection::class,
    'dsn'          => 'mysql:host=127.0.0.1;dbname=questionnaire',
    'username'     => 'root',
    'password'     => 'Oj949800!@#',
    'charset'      => 'utf8mb4',
    'commandClass' => \app\components\Command::class,
    'attributes'   => [
        PDO::ATTR_TIMEOUT => 5,
    ],
    'slaves'       => [
        [
            'dsn' => 'mysql:host=127.0.0.1;dbname=questionnaire',
        ],
    ],
    'slaveConfig'  => [
        'username'   => 'root',
        'password'   => 'Oj949800!@#',
        'charset'    => 'utf8mb4',
        'attributes' => [
            PDO::ATTR_TIMEOUT => 5,
        ],
    ],
];