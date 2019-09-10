<?php

namespace app\components\filters;

use app\components\User;
use app\helpers\HeaderHelper;
use app\components\log\Log;
use app\models\logic\AdminLogic;
use yii\base\ActionFilter;

/**
 * token验证
 *
 * @uses     CheckUserToken
 * @version  2018年08月14日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class CheckUserToken extends ActionFilter
{
    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     */
    public function beforeAction($action): bool
    {
        $uid   = HeaderHelper::getUid();
        $token = HeaderHelper::getToken();

        if ($uid == 0 || empty($token)) {
            return true;
        }

        //验证登录状态
        try {
            $result = (new AdminLogic())->checkToken($uid, $token);
            if ($result === false) {
                return true;
            }

            /* @var User $user */
            $user = \Yii::$app->user;
            $user->setUid($uid);

        } catch (\Exception $e) {
            Log::error($e);
            return true;
        }

        return true;
    }
}