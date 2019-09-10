<?php

$config = yii\helpers\ArrayHelper::merge(require(__DIR__ . '/../console.php'), [
    'components' => [
        // 日志配置
        'log' => [
            'targets' => [
                'notice'      => [
                    'json' => false,
                ],
                'application' => [
                    'json' => false,
                ],
            ],
        ],
    ],
    'params'     => [
    ]
]);

return $config;