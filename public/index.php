<?php

date_default_timezone_set('PRC');

defined('YII_DEBUG') || define('YII_DEBUG', true);
defined('YII_ENV') || define('YII_ENV', 'dev');
defined('SYSTEM_NAME') || define('SYSTEM_NAME', 'questionnaire-api');


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require dirname(__FILE__) . '/../app/config/mode.php';

$config = require __DIR__ . '/../app/config/' . APPLICATION_ENV . '/main.php';

$config = select_db($config);
(new yii\web\Application($config))->run();
