<?php

namespace app\models\dao;

/**
 * @property integer $id
 * @property string  $code   登录码
 * @property integer $status 是否有效，1=是，2=否
 * @property integer $expire 有效截止时间戳
 * @property integer $ctime  创建时间
 * @property integer $utime
 *
 * RandCode
 *
 * @uses     RandCode
 * @version  2019-12-31
 * @author   Generate
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class RandCode extends Model
{
    public static function tableName()
    {
        return 'rand_code';
    }

    public function maps(): array
    {
        return [

        ];
    }
}
