<?php

namespace app\models\dao;


/**
 * @property integer  $id 
 * @property string  $name 单位名称
 * @property integer  $status 是否有效，1=是。2=否
 * @property integer  $ctime 
 *
 * WorkerPlace
 *
 * @uses     WorkerPlace
 * @version  2019-12-31
 * @author   Generate
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class WorkerPlace extends Model
{
    public static function tableName()
    {
        return 'worker_place';
    }
    
    public function maps(): array
    {
        return [

        ];
    }
}
