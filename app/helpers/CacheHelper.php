<?php

namespace app\helpers;

use app\components\CacheConst;

/**
 * 缓存KEY
 *
 * @uses     CacheHelper
 * @version  2018年08月08日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class CacheHelper
{
    /**
     * @var string
     */
    public static $prefix = 'demo_';

    /**
     * @param int $uid
     *
     * @return string
     */
    public static function getUserInfoKey(int $uid): string
    {
        return self::$prefix . CacheConst::USER_INFO . $uid;
    }
}