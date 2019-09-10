<?php
//
//
//namespace app\controllers;
//
//use app\components\Controller;
//use app\components\Errno;
//use app\models\worker\logic\LoginLogic;
//use hxh\exception\ApplicationException;
//use hxh\helpers\ResponseHelper;
//use hxh\helpers\ValidatorHelper;
//use hxh\log\Log;
//
//
///**
// *
// * @uses     UserController
// * @version  2018年08月15日
// * @author   longyuxiang <longyuxiang@kzl.com.cn>
// * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
// */
//class UserController extends Controller
//{
//    /**
//     * @return LoginLogic
//     */
//    public function loginLogic()
//    {
//        return new LoginLogic();
//    }
//
//    /**
//     * 发送短信验证码
//     *
//     * @throws \Throwable
//     * @throws \yii\base\ExitException
//     */
//    public function actionSendCode()
//    {
//        $mobile = ValidatorHelper::validateMobile(body(), 'mobile');
//        try {
//
//            $res = $this->loginLogic()->sendCode($mobile);
//        } catch (ApplicationException $e) {
//            Log::error($e);
//            ResponseHelper::outputJson([], $e->getMessage(), Errno::SUCCESS, $e->getCode());
//        } catch (\Throwable $e) {
//            Log::error($e);
//            ResponseHelper::outputJson([], '发送过于频繁，稍后再试', Errno::SUCCESS, Errno::SEND_CODE_FAIL);
//        }
//
//        if (!$res) {
//            ResponseHelper::outputJson([], '发送过于频繁，稍后再试', Errno::SUCCESS, Errno::SEND_CODE_FAIL);
//        }
//
//        ResponseHelper::outputJson([], 'ok');
//    }
//
//    /**
//     * 手机号登录
//     *
//     * @throws \Throwable
//     * @throws \hxh\exception\ParameterException
//     * @throws \yii\base\ExitException
//     */
//    public function actionLogin()
//    {
//        $mobile = ValidatorHelper::validateMobile(body(), 'mobile');
//        $code   = ValidatorHelper::validateString(body(), 'code', 3, 5);
//
//
//        try {
//            $result = $this->loginLogic()->loginByMobile($mobile, $code);
//
//        } catch (ApplicationException $e) {
//            ResponseHelper::outputJson([], $e->getMessage(), Errno::SUCCESS, $e->getCode());
//        } catch (\Throwable $e) {
//            Log::error($e->getMessage());
//            ResponseHelper::outputJson([], $e->getMessage(), Errno::SUCCESS, Errno::FATAL);
//        }
//
//        if (empty($result)) {
//            Log::error($e->getMessage());
//            ResponseHelper::outputJson([], $e->getMessage(), Errno::FATAL);
//        }
//
//        ResponseHelper::outputJson($result, 'ok');
//    }
//
//
//    /**
//     * 退出登陆
//     *
//     * @throws \yii\base\ExitException
//     */
//    public function actionLoginOut()
//    {
//        try {
//            $this->loginLogic()->loginOut();
//        } catch (\Throwable $e) {
//
//            Log::error($e);
//            ResponseHelper::outputJson([], $e->getMessage(), $e->getCode());
//        }
//
//        ResponseHelper::outputJson([], 'ok');
//    }
//
//    /**
//     * 获取微信openid和unionid
//     *
//     * @throws \Throwable
//     * @throws \hxh\exception\ParameterException
//     * @throws \yii\base\ExitException
//     * @throws \yii\base\InvalidConfigException
//     */
//    public function actionGetWeChatUnionid()
//    {
//        $iv            = v_string('iv');
//        $code          = v_string('code');
//        $encryptedData = v_string('encryptedData');
//
//        try {
//            $result = $this->loginLogic()->auth($iv, $code, $encryptedData);
//        } catch (ApplicationException $e) {
//            ResponseHelper::outputJson([], $e->getMessage(), Errno::SUCCESS, $e->getCode());
//        }
//
//        ResponseHelper::outputJson($result);
//    }
//
//}
