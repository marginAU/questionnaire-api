<?php

namespace app\components;

/**
 * 返回状态码
 *
 * @uses     Errno
 * @version  2018年05月29日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Errno
{
    // 用户不存在
    const USER_NOT_EXIST = 407;

    // 验证码错误
    const MOBILE_CODE_ERROR = 409;

    const FATAL = 500;
}