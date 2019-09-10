<?php

mb_internal_encoding("UTF-8");
$hostname = gethostname();

if (strpos($hostname, 'cd-production') !== false || strpos($hostname, 'sh-pro') !== false) {
    define('APPLICATION_ENV', \app\helpers\EnvHelper::PRODUCTION_ENV);
} elseif (strpos($hostname, 'cd-testing') !== false) {
    define('APPLICATION_ENV', \app\helpers\EnvHelper::TESTING_ENV);
} else {
    define('APPLICATION_ENV', \app\helpers\EnvHelper::DEVELOPER_ENV);
}