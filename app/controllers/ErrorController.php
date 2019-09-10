<?php

namespace app\controllers;

use app\components\Controller;
use yii\web\Response;

/**
 * 统一错误和异常处理
 */
class ErrorController extends Controller
{

    /**
     * 统一异常或错误，输出数据
     *
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        $exception = \Yii::$app->errorHandler->exception;

        \Yii::$app->response->format     = Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = 200;

        $data = [
            'data'       => "",
            'status'     => 500,
            'message'    => "server  error",
            'serverTime' => microtime(true),
        ];

        if ($exception != null) {
            $data['message'] = $exception->getMessage() . $exception->getFile();
        }

        \Yii::$app->response->data = $data;

        return \Yii::$app->response;
    }
}
