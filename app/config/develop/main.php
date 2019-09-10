<?php
$config = yii\helpers\ArrayHelper::merge(require(__DIR__ . '/../base.php'), [
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
        'host' => 'https://cater-testing.gongzl.com',
    ],
]);

return $config;
