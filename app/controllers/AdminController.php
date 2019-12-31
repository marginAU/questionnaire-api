<?php

namespace app\controllers;

use app\components\Controller;
use app\helpers\HeaderHelper;
use app\helpers\ResponseHelper;
use app\helpers\ValidatorHelper;
use app\models\logic\AdminLogic;
use app\models\logic\WorkerPlaceLogic;

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
        $uid         = HeaderHelper::getUid();
        $mobile      = ValidatorHelper::validateString($_POST, 'mobile', null, null, '');
        $idcard      = ValidatorHelper::validateString($_POST, 'idcard', null, null, '');
        $startTime   = ValidatorHelper::validateString($_POST, 'startTime', null, null, $defaultTime);
        $endTime     = ValidatorHelper::validateString($_POST, 'endTime', null, null, date('Y-m-d'));
        $page        = ValidatorHelper::validateInteger($_POST, 'page', null, null, 1);
        $size        = ValidatorHelper::validateInteger($_POST, 'size', null, null, 10);

        $res = $this->logic()->answerList($uid, $startTime, $endTime, $mobile, $idcard, $page, $size);

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
        $uid         = HeaderHelper::getUid();

        $this->logic()->download($uid, $startTime, $endTime);

        ResponseHelper::outputJson();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionGenerateCode()
    {
        $num = ValidatorHelper::validateInteger($_POST, 'num', 0);

        $this->logic()->generateCode($num);

        ResponseHelper::outputJson();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionGetLoginCode()
    {
        $res = $this->logic()->getLoginCodeList();

        ResponseHelper::outputJson($res);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionGetWorkPlace()
    {
        $res = (new WorkerPlaceLogic())->getList();

        ResponseHelper::outputJson($res);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionGetAdminUserList()
    {
        $page = ValidatorHelper::validateInteger($_POST, 'page', null, null, 1);
        $size = ValidatorHelper::validateInteger($_POST, 'size', null, null, 10);
        $res  = $this->logic()->getAdminUserList($page, $size);

        ResponseHelper::outputJson($res);
    }

    /**
     * @throws \Throwable
     * @throws \yii\base\ExitException
     */
    public function actionAddAdminUserList()
    {
        $params = [
            'username'      => ValidatorHelper::validateString('username'),
            'password'      => ValidatorHelper::validateString('password'),
            'workerPlaceId' => ValidatorHelper::validateInteger('workerPlaceId'),
        ];

        $this->logic()->addAdminUser($params);

        ResponseHelper::outputJson();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionUpdateAdminUser()
    {
        $uid    = ValidatorHelper::validateInteger($_POST, 'uid');
        $params = [
            'username'      => ValidatorHelper::validateString('username'),
            'password'      => ValidatorHelper::validateString('password'),
            'workerPlaceId' => ValidatorHelper::validateInteger('workerPlaceId'),
        ];

        $this->logic()->updateAdminUser($uid, $params);

        ResponseHelper::outputJson();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionDelAdminUser()
    {
        $uid = ValidatorHelper::validateInteger($_POST, 'uid');

        $this->logic()->delAdminUser($uid);

        ResponseHelper::outputJson();
    }
}