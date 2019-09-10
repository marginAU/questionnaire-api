<?php

namespace app\models\dao;

/**
 * @property integer $id
 * @property string  $username 用户账户
 * @property string  $password 密码
 * @property integer $status   状态，1=有效，2=无效
 * @property integer $ctime    创建时间
 * @property string  $token    token
 * @property string  $salt     salt
 *
 * AdminUser
 *
 * @uses     AdminUser
 * @version  2019-09-08
 * @author   Generate
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class AdminUser extends Model
{
    public static function tableName()
    {
        return 'admin_user';
    }

    public function maps(): array
    {
        return [

        ];
    }
}
