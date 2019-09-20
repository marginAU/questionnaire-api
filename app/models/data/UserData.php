<?php

namespace app\models\data;

use app\components\CommonConst;
use app\models\dao\User;

/**
 *
 * userData
 *
 * @uses     UserData
 * @version  2019-09-08
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class UserData
{
    /**
     * @param array $params
     *
     * @return int
     * @throws \Throwable
     */
    public function save(array $params): int
    {
        $params['ctime'] = time();

        $model = new User();

        $model->setAttributes($params, false);
        $result = $model->insert();
        if ($result === false) {
            return 0;
        }

        return \Yii::$app->db->getLastInsertID();
    }

    /**
     * @param int   $id
     * @param array $params
     *
     * @return bool
     */
    public function update(int $id, array $params)
    {
        $params['utime'] = time();
        $model           = User::findOne(['id' => $id]);
        $model->setAttributes($params, false);
        return $model->save();
    }


    public function getList(array $cond, int $page, int $size)
    {
        return User::find()->where($cond)->offset(($page - 1) * $size)->limit($size)->orderBy('ctime DESC')->all();
    }

    public function getAllList(array $cond)
    {
        return User::find()->where($cond)->all();
    }

    public function getCount(array $cond)
    {
        return User::find()->where($cond)->count('id');
    }


    public function getDetail(int $uid, int $status = CommonConst::STATUS_YES)
    {
        return User::find()->where(['id' => $uid, 'status' => $status])->orderBy('ctime DESC')->one();
    }

    public function getDetailByCondition(array $conditions)
    {
        return User::find()->where($conditions)->orderBy('ctime DESC')->one();
    }
}
