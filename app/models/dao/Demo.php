<?php

namespace app\models\dao;

/**
 * demo
 *
 * @property string $name           名称
 * @property int    $age            年龄
 * @property int    $demoCreateTime 时间
 * @property int    $ctime          创建时间
 * @property int    $utime          更新时间
 *
 *
 * @uses     Demo
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Demo extends Model
{
    /**
     * @return array
     */
    public function maps(): array
    {
        return [
            'demo_create_time' => 'demoCreateTime',
        ];
    }

    /**
     * @return bool
     */
    public static function add(): bool
    {
        $demo                 = new Demo();
        $demo->name           = uniqid();
        $demo->age            = mt_rand(1, 100);
        $demo->demoCreateTime = time();

        return $demo->save();
    }
}