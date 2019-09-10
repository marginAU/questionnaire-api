<?php

namespace app\helpers;

use app\components\log\Log;
use yii\web\Response;

/**
 * @uses     ResponseHelper
 * @version  2019年09月07日
 * @author   oujun <oujun@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class ResponseHelper
{
    /**
     * @param array  $data    数据
     * @param string $message 提示信息
     * @param int    $status  请求状态，200成功，非200失败
     * @param int    $code    业务状态，0成功
     *
     * @throws \yii\base\ExitException
     */
    public static function outputJson(array $data = [], string $message = '', int $status = 200, int $code = 0): void
    {
        if (!headers_sent()) {
            header('Content-type: application/json');
        }

        if (empty($data)) {
            $data = new \stdClass();
        }

        if (\Yii::$app->controller) {
            \Yii::$app->controller->layout = false;
        }

        $data = [
            'data'       => $data,
            'status'     => $status,
            'code'       => $code,
            'message'    => $message,
            'serverTime' => microtime(true),
        ];

        Log::pushlog("status", $status);
        \Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        \Yii::$app->getResponse()->data   = $data;

        \Yii::$app->end();
    }

    /**
     * 原样输出
     *
     * @param string $data   需要输出的数据
     * @param int    $status 请求状态，200成功，非200失败
     *
     * @throws \yii\base\ExitException
     * @return void
     */
    public static function output(string $data, int $status = 200): void
    {
        if (\Yii::$app->controller) {
            \Yii::$app->controller->layout = false;
        }

        Log::pushlog("status", $status);
        \Yii::$app->getResponse()->content = $data;

        \Yii::$app->end();
    }
}