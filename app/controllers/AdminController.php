<?php

namespace app\controllers;

use app\components\Controller;
use app\helpers\HeaderHelper;
use app\helpers\ResponseHelper;
use app\helpers\ValidatorHelper;
use app\models\logic\AdminLogic;

/**
 * @uses     AdminController
 * @version  2019年09月08日
 * @author   oujun <oujun@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class AdminController extends Controller
{

    public function logic()
    {
        return new AdminLogic();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionLogin()
    {
        $username = ValidatorHelper::validateString($_POST, 'username');
        $password = ValidatorHelper::validateString($_POST, 'password');

        $res = $this->logic()->login($username, $password);

        ResponseHelper::outputJson($res);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionLoginOut()
    {
        $uid = HeaderHelper::getUid();

        $this->logic()->loginOut($uid);

        ResponseHelper::outputJson([]);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionAnswerList()
    {
        $defaultTime = date('Y-m-d', strtotime('-7 day'));
        $startTime   = ValidatorHelper::validateString($_POST, 'startTime', null, null, $defaultTime);
        $endTime     = ValidatorHelper::validateString($_POST, 'endTime', null, null, date('Y-m-d'));
        $page        = ValidatorHelper::validateInteger($_POST, 'page', null, null, 1);
        $size        = ValidatorHelper::validateInteger($_POST, 'size', null, null, 10);

        $res = $this->logic()->answerList($startTime, $endTime, $page, $size);

        ResponseHelper::outputJson($res);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionAnswerDetail()
    {
        $uid = ValidatorHelper::validateInteger($_POST, 'id', 1);

        $res = $this->logic()->answerDetail($uid);

        ResponseHelper::outputJson($res);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionDownloadAnswer()
    {
        $defaultTime = date('Y-m-d', strtotime('-7 day'));
        $startTime   = ValidatorHelper::validateString($_POST, 'startTime', null, null, $defaultTime);
        $endTime     = ValidatorHelper::validateString($_POST, 'endTime', null, null, date('Y-m-d'));

        $this->logic()->download($startTime, $endTime);

        ResponseHelper::outputJson();
    }

}