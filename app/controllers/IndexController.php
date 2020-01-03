<?php

namespace app\controllers;

use app\components\log\Log;
use app\helpers\ResponseHelper;
use app\helpers\ValidatorHelper;
use app\models\logic\IndexLogic;
use app\components\Controller;
use app\models\logic\WorkerPlaceLogic;

/**
 * 默认控制器
 *
 * @uses     IndexController
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class IndexController extends Controller
{

    private function logic()
    {
        return new IndexLogic();
    }

    /**
     * @throws \Exception
     */
    public function actionIndex()
    {
        ResponseHelper::outputJson([], 'this is index page!');
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionQuestionList()
    {
        $page = ValidatorHelper::validateInteger($_POST, 'page');
        $res  = $this->logic()->questionList($page);

        ResponseHelper::outputJson($res);
    }

    /**
     * @throws \Throwable
     * @throws \yii\base\ExitException
     */
    public function actionSaveUser()
    {
        $loginCode = ValidatorHelper::validateString($_POST, 'loginCode');
        $params    = [
            'sex'              => ValidatorHelper::validateString($_POST, 'sex', null, null, 'A'),
            'username'         => ValidatorHelper::validateString($_POST, 'username'),
            'nation'           => ValidatorHelper::validateString($_POST, 'nation', null, null, ''),
            'mobile'           => ValidatorHelper::validateString($_POST, 'mobile', null, null, ''),
            'birthday'         => ValidatorHelper::validateString($_POST, 'birthday', null, null, ''),
            'age'              => ValidatorHelper::validateInteger($_POST, 'age', null, null, 0),
            'maritalStatus'    => ValidatorHelper::validateString($_POST, 'maritalStatus'),
            'education'        => ValidatorHelper::validateString($_POST, 'education'),
            'childrenOrNot'    => ValidatorHelper::validateString($_POST, 'childrenOrNot', null, null, 'B'),
            'childrenNum'      => ValidatorHelper::validateString($_POST, 'childrenNum', null, null, 'A'),
            'parentWorkStatus' => ValidatorHelper::validateString($_POST, 'parentWorkStatus', null, null, 'A'),
            'politicalStatus'  => ValidatorHelper::validateInteger($_POST, 'politicalStatus', null, null, 1),
        ];

        $result = $this->logic()->saveUserInfo($params, $loginCode);

        ResponseHelper::outputJson($result);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionSaveAnswer()
    {
        $uid        = ValidatorHelper::validateInteger($_POST, 'uid');
        $loginCode  = ValidatorHelper::validateString($_POST, 'loginCode');
        $answerList = $_POST['answerList'] ?? [];
        if (empty($answerList)) {
            Log::error('answerList不能为空');
            throw new \Exception('提交信息错误');
        }

        foreach ($answerList as $row) {
            ValidatorHelper::validateInteger($row, 'id');
            ValidatorHelper::validateInteger($row, 'answer');
        }

        $this->logic()->saveAnswer($uid, $answerList, $loginCode);

        ResponseHelper::outputJson();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionVerifyLoginCode()
    {
        $loginCode = ValidatorHelper::validateString($_POST, 'loginCode');

        $this->logic()->verifyLoginCode($loginCode);

        ResponseHelper::outputJson();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionGetWorkerPlace()
    {
        $res = (new WorkerPlaceLogic())->getList();

        ResponseHelper::outputJson($res);
    }
}