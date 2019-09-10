<?php
!defined('APP_PATH') && define('APP_PATH', dirname(__FILE__) . '/../');
!defined('RUNTIME_PATH') && define('RUNTIME_PATH', APP_PATH . '../runtime/' . SYSTEM_NAME);


$config = [
    'id'           => SYSTEM_NAME,
    'basePath'     => dirname(__FILE__),
    'name'         => SYSTEM_NAME,
    'runtimePath'  => RUNTIME_PATH,
    'bootstrap'    => ['log'],
    'defaultRoute' => 'index/index',

    // 组件配置
    'components'   => [
        'request'      => [
            // 屏蔽提交cookie验证
            "enableCsrfValidation"   => false,
            "enableCookieValidation" => false,
            "parsers"                => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'response'     => [
            'formatters' => [
                \yii\web\Response::FORMAT_XML => \yii\web\XmlResponseFormatter::class,
            ],
        ],
        'user'         => [
            'class'         => app\components\User::class,
            'enableSession' => false,
            'identityClass' => '',
        ],

        // 路由配置
        'urlManager'   => [
            'class'           => \yii\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [],
        ],

        // 日志配置
        'log'          => [
            'class'   => \app\components\log\Dispatcher::class,
            'targets' => [
                'notice'      => [
                    'class'          => \app\components\log\FileTarget::class,
                    'logFile'        => '@runtime/notice.log',
                    'levels'         => ['trace', 'info', 'notice'],
                    'enableRotation' => false,
                    'json'           => true,
                    'logVars'        => [],
                ],
                'application' => [
                    'class'          => \app\components\log\FileTarget::class,
                    'logFile'        => '@runtime/application.log',
                    'levels'         => ['error', 'warning'],
                    'enableRotation' => false,
                    'json'           => true,
                    'logVars'        => [],
                ],
            ],
            'logger'  => [
                'class' => \app\components\log\Logger::class,
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],

        'i18n' => [
            'translations' => [
                'app*' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/../messages',
                    'fileMap'  => [
                        'app.test' => 'test.php',
                    ],
                ],
            ],
        ],

        'redis' => require __DIR__ . '/redis.php',
        'db'    => require __DIR__ . '/db.php',
//        'mq'    => require __DIR__ . '/mq.php',
    ],

    // 参数配置
    'params'       => [
        'sub_database' => false,
    ],
];

return \yii\helpers\ArrayHelper::merge($config, require __DIR__ . '/service.php');
