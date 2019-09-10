<?php

$config = yii\helpers\ArrayHelper::merge(require(__DIR__ . '/../console.php'), [
    'components'          => [
    ],
    'params'              => [

    ],
]);

return $config;