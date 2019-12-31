<?php

namespace app\models\data;

use app\components\CommonConst;
use app\models\dao\RandCode;

/**
 *
 * randCodeData
 *
 * @uses     RandCodeData
 * @version  2019-12-31
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class RandCodeData
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

        $model = new RandCode();

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
        $model           = RandCode::findOne(['id' => $id]);
        $model->setAttributes($params, false);
        return $model->save();
    }


    public function getList(array $cond, int $page, int $size)
    {
        return RandCode::find()->where($cond)->offset(($page - 1) * $size)->limit($size)->orderBy('ctime DESC')->all();
    }

    public function getCount(array $cond)
    {
        return RandCode::find()->where($cond)->count('id');
    }

    public function getDetailByCondition(array $conditions)
    {
        return RandCode::find()->where($conditions)->one();
    }

    public function batchInsert(array $columns, array $rows)
    {
        return RandCode::batchInsert($columns, $rows);
    }
}
