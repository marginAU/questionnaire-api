<?php

namespace app\helpers;

/**
 * header帮助类
 *
 * @uses     HeaderHelper
 * @version  2018年08月11日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class HeaderHelper
{
    /**
     * @var array
     */
    private static $headers = [];

    /**
     * @param array $headers
     */
    public static function init(array $headers)
    {
        self::$headers = $headers;
    }

    /**
     * @return int
     */
    public static function getUid(): int
    {
        return self::$headers['uid'];
    }

    /**
     * @return string
     */
    public static function getToken(): string
    {
        return self::$headers['token'];
    }

    /**
     * @return string
     */
    public static function getLon(): float
    {
        return self::$headers['lon'];
    }

    /**
     * @return string
     */
    public static function getLat(): float
    {
        return self::$headers['lat'];
    }

    /**
     * @return string
     */
    public static function getLocale(): string
    {
        return self::$headers['locale'];
    }

    /**
     * @return string
     */
    public static function getMiniVersion(): string
    {
        return self::$headers['miniVersion'];
    }

    /**
     * @return string
     */
    public static function getSdkVersion(): string
    {
        return self::$headers['sdkVersion'];
    }

    /**
     * @return string
     */
    public static function getBid(): int
    {
        return self::$headers['bid'];
    }


    /**
     * @return int
     */
    public static function getSid(): int
    {
        return self::$headers['sid'];
    }
}
