<?php

namespace app\helpers;

/**
 * 环境 helper
 *
 * @uses     EnvHelper
 * @version  2018年08月15日
 * @author   oujun <oujun@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class EnvHelper
{
    /**
     * 生产环境
     *
     * @var string
     */
    const PRODUCTION_ENV = 'production';

    /**
     * 测试环境
     *
     * @var string
     */
    const TESTING_ENV = 'testing';

    /**
     * 开发环境
     *
     * @var string
     */
    const DEVELOPER_ENV = 'develop';

    /**
     * 验证是否是生产环境
     *
     * @return bool
     */
    public static function isProduct()
    {
        if (APPLICATION_ENV == self::PRODUCTION_ENV) {
            return true;
        }

        return false;
    }

    /**
     * 验证是否是生产环境
     *
     * @return bool
     */
    public static function isProduction()
    {
        return self::isProduct();
    }

    /**
     * 验证是否是测试环境
     *
     * @return bool
     */
    public static function isTesting()
    {
        if (APPLICATION_ENV == self::TESTING_ENV) {
            return true;
        }

        return false;
    }

    /**
     * 验证是否是开发环境
     *
     * @return bool
     */
    public static function isDeveloper()
    {
        if (APPLICATION_ENV == self::DEVELOPER_ENV) {
            return true;
        }

        return false;
    }

    /**
     * 获取环境
     *
     * @return string
     */
    public static function getEnv()
    {
        if (APPLICATION_ENV == self::PRODUCTION_ENV) {
            $env = self::PRODUCTION_ENV;
        } elseif (APPLICATION_ENV == self::TESTING_ENV) {
            $env = self::TESTING_ENV;
        } else {
            $env = self::DEVELOPER_ENV;
        }

        return $env;
    }

}