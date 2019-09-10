<?php

namespace app\models\data;

use app\components\CommonConst;
use app\models\dao\AdminUser;

/**
 *
 * adminUserData
 *
 * @uses     AdminUserData
 * @version  2019-09-08
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class AdminUserData
{
    /**
     * @param $username
     *
     * @return AdminUser|null
     */
    public function getDetail($username)
    {
        return AdminUser::findOne(['username' => $username, 'status' => CommonConst::STATUS_YES]);
    }

    /**
     * @param int $uid
     *
     * @return AdminUser|null
     */
    public function getDetailByUid(int $uid)
    {
        return AdminUser::findOne(['uid' => $uid, 'status' => CommonConst::STATUS_YES]);
    }


    /**
     * @param int   $uid
     * @param array $params
     *
     * @return bool
     */
    public function update(int $uid, array $params): bool
    {
        $user = AdminUser::findOne(['id' => $uid]);
        if (empty($user)) {
            return true;
        }

        $user->setAttributes($params, false);
        return $user->save();
    }
}
