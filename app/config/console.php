<?php
!defined('APP_PATH') && define('APP_PATH', dirname(__FILE__) . '/../');
!defined('RUNTIME_PATH') && define('RUNTIME_PATH', APP_PATH . '../runtime/' . SYSTEM_NAME);


$config = [
    'id'                  => SYSTEM_NAME,
    'basePath'            => dirname(__FILE__),
    'name'                => SYSTEM_NAME,
    'runtimePath'         => RUNTIME_PATH,
    'bootstrap'           => ['log'],
    'defaultRoute'        => 'index/index',
    'controllerNamespace' => 'app\\commands',

    // 组件配置
    'components'          => [

        // 日志配置
        'log'   => [
            'class'   => \app\components\log\Dispatcher::class,
            'targets' => [
                'notice'      => [
                    'class'          => \app\components\log\FileTarget::class,
                    'logFile'        => '@runtime/notice.log',
                    'levels'         => ['trace', 'info', 'notice'],
                    'enableRotation' => false,
                    'json'           => true,
                    'exportInterval' => 1,
                    'logVars'        => [],
                ],
                'application' => [
                    'class'          => \app\components\log\FileTarget::class,
                    'logFile'        => '@runtime/application.log',
                    'levels'         => ['error', 'warning'],
                    'enableRotation' => false,
                    'json'           => true,
                    'exportInterval' => 1,
                    'logVars'        => [],
                ],
            ],
            'logger'  => [
                'class'         => \app\components\log\Logger::class,
                'flushInterval' => 1,
            ],
        ],

        // redis 组件
        'redis' => require __DIR__ . '/redis.php',

        // db 组件
        'db'    => require __DIR__ . '/db.php',

        // mq 组件
//        'mq'    => require __DIR__ . '/mq.php',
    ],

    'controllerMap' => [
        'migrate'  => [
            'class'               => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
                'app\migrations',
            ],
            'migrationPath'       => "@app/../migrations", // allows to disable not namespaced migration completely
        ],
//        'generate' => [
//            'class' => \app\command\generate\GenerateController::class,
//        ],
        'generate' => [
            'class' => \app\commands\generate\GenerateController::class,
        ]
    ],

    // 参数配置
    'params'        => [
        'sub_database' => false,
    ],
];

return \yii\helpers\ArrayHelper::merge($config, require __DIR__ . '/service.php');
