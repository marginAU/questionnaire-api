<?php

namespace app\models\data;

use app\models\dao\WorkerPlace;

/**
 *
 * workerPlaceData
 *
 * @uses     WorkerPlaceData
 * @version  2019-12-31
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class WorkerPlaceData
{
    /**
     * @param array $cond
     * @param int   $page
     * @param int   $size
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList(array $cond, int $page, int $size)
    {
        return WorkerPlace::find()->where($cond)
            ->offset(($page - 1) * $size)->limit($size)->orderBy('ctime DESC')->all();
    }
}
