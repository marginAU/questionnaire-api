<?php

namespace app\controllers;

use app\components\log\Log;
use app\helpers\ResponseHelper;
use app\helpers\ValidatorHelper;
use app\models\logic\IndexLogic;
use app\components\Controller;

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
     * @throws \yii\base\ExitException
     */
    public function actionSaveUser()
    {
        $params = [
            'sex'              => ValidatorHelper::validateString($_POST, 'sex', null, null, 'A'),
            'username'         => ValidatorHelper::validateString($_POST, 'username'),
            'idcard'           => ValidatorHelper::validateString($_POST, 'idcard', null, null, ''),
            'mobile'           => ValidatorHelper::validateString($_POST, 'mobile', null, null, ''),
            'position'         => ValidatorHelper::validateString($_POST, 'position', null, null, ''),
            'sourcePlace'      => ValidatorHelper::validateString($_POST, 'sourcePlace', null, null, ''),
            'age'              => ValidatorHelper::validateInteger($_POST, 'age', null, null, 0),
            'nation'           => ValidatorHelper::validateString($_POST, 'nation', null, null, ''),
            'politicalStatus'  => ValidatorHelper::validateInteger($_POST, 'politicalStatus', null, null, 1),
            'maritalStatus'    => ValidatorHelper::validateString($_POST, 'maritalStatus'),
            'education'        => ValidatorHelper::validateString($_POST, 'education'),
            'workTime'         => ValidatorHelper::validateInteger($_POST, 'workTime', null, null, 0),
            'workPlace'        => ValidatorHelper::validateString($_POST, 'workPlace', null, null, ''),
            'childrenOrNot'    => ValidatorHelper::validateString($_POST, 'childrenOrNot', null, null, 'B'),
            'childrenSex'      => ValidatorHelper::validateString($_POST, 'childrenSex', null, null, ''),
            'childrenAge'      => ValidatorHelper::validateInteger($_POST, 'childrenAge', null, null, 0),
            'parentWorkStatus' => ValidatorHelper::validateString($_POST, 'parentWorkStatus', null, null, 'A'),
            'socialScale'      => ValidatorHelper::validateInteger($_POST, 'socialScale', null, null, 10),
        ];

        $result = $this->logic()->saveUserInfo($params);

        ResponseHelper::outputJson($result);
    }

    /**
     * @throws \yii\base\ExitException
     * @throws \yii\db\Exception
     */
    public function actionSaveAnswer()
    {
        $uid        = ValidatorHelper::validateInteger($_POST, 'uid');
        $answerList = $_POST['answerList'] ?? [];
        if (empty($answerList)) {
            Log::error('answerList不能为空');
            throw new \Exception('提交信息错误');
        }

        foreach ($answerList as $row) {
            ValidatorHelper::validateInteger($row, 'id');
            ValidatorHelper::validateInteger($row, 'answer');
        }

        $this->logic()->saveAnswer($uid, $answerList);

        ResponseHelper::outputJson();
    }
}