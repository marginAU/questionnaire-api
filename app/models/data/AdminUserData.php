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
        return AdminUser::findOne(['id' => $uid, 'status' => CommonConst::STATUS_YES]);
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

        if (!empty($params['password'])) {
            $params['password'] = md5(md5($user->salt) . $params['password']);
        }

        $user->setAttributes($params, false);
        return $user->save();
    }

    /**
     * @param array $params
     *
     * @return bool
     * @throws \Throwable
     */
    public function add(array $params): bool
    {
        $params['ctime']  = time();
        $params['status'] = CommonConst::STATUS_YES;

        $model = new AdminUser();

        if (!empty($params['password'])) {
            $params['password'] = md5(md5($params['salt']) . $params['password']);
        }

        $model->setAttributes($params, false);
        $result = $model->insert();
        if ($result === false) {
            return 0;
        }

        return \Yii::$app->db->getLastInsertID();
    }

    public function getList(array $cond, int $page, int $size)
    {
        return AdminUser::find()->where($cond)->offset(($page - 1) * $size)->limit($size)->orderBy('ctime DESC')->all();
    }

    public function getCount(array $cond)
    {
        return AdminUser::find()->where($cond)->count('id');
    }

}
