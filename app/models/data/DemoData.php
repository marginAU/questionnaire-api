<?php

namespace app\models\data;

use app\helpers\DemoHelper;
use app\models\dao\Demo;
use hxh\models\Data;

/**
 * Data
 *
 * @uses     DemoData
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class DemoData extends Data
{

    /**
     * @return int
     */
    public function getId(): int
    {
        $demo                 = new Demo();
        $demo->name           = uniqid();
        $demo->age            = mt_rand(1, 100);
        $demo->demoCreateTime = time();

        $ret = $demo->save();
        $id  = \Yii::$app->db->getLastInsertID();

        return $id;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getInfo(): array
    {
        $demo                 = new Demo();
        $demo->name           = uniqid();
        $demo->age            = mt_rand(1, 100);
        $demo->demoCreateTime = time();
        $demo->ctime          = time();
        $demo->utime          = time();


        $ret    = $demo->save();
        $id     = \Yii::$app->db->getLastInsertID();
        $result = Demo::findOne(['id' => $id])->toArray();

        return $result;
    }

    /**
     * @return bool
     */
    public function add():bool
    {
        return Demo::add();
    }
}