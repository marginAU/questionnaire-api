<?php

namespace app\components\log;

use yii\log\Logger;

/**
 * 公共日志
 *
 * @uses     Log
 * @version  2018年05月28日
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Log
{
    /**
     * @var \app\components\log\Logger
     */
    private static $logger;

    /**
     * @param  mixed $message
     * @param string $category
     */
    public static function trace($message, string $category = 'application'): void
    {
        static::getLogger()->log(self::getTrace($message), Logger::LEVEL_TRACE, $category);
    }

    /**
     * @param  mixed $message
     * @param string $category
     */
    public static function error($message, string $category = 'application'): void
    {
        static::getLogger()->log(self::getTrace($message), Logger::LEVEL_ERROR, $category);
    }

    /**
     * @param  mixed $message
     * @param string $category
     */
    public static function warning($message, string $category = 'application'): void
    {
        static::getLogger()->log(self::getTrace($message), Logger::LEVEL_WARNING, $category);
    }

    /**
     * @param  mixed $message
     * @param string $category
     */
    public static function info($message, string $category = 'application'): void
    {
        static::getLogger()->log(self::getTrace($message), Logger::LEVEL_INFO, $category);
    }

    /**
     * @param $name
     * @param $value
     */
    public static function profile(string $name, int $value): void
    {
        static::getLogger()->profile($name, $value);
    }

    /**
     * @param string $name
     */
    public static function profileStart(string $name): void
    {
        static::getLogger()->profileStart($name);
    }

    /**
     * @param string $name
     */
    public static function profileEnd(string $name): void
    {
        static::getLogger()->profileEnd($name);
    }

    /**
     * @param string $key
     * @param  mixed $val
     */
    public static function pushlog(string $key, $val): void
    {
        static::getLogger()->pushLog($key, $val);
    }

    /**
     * @param string   $name
     * @param int      $hit
     * @param int|null $total
     */
    public static function counting(string $name, int $hit, int $total = null): void
    {
        static::getLogger()->counting($name, $hit, $total);
    }

    /**
     * @param mixed $message
     *
     * @return mixed
     */
    public static function getTrace($message)
    {
        $traces = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $count  = count($traces);
        $ex     = '';
        if ($count >= 2) {
            $info = $traces[1];
            if (isset($info['file'], $info['line'])) {
                $filename = basename($info['file']);
                $linenum  = $info['line'];
                $ex       = "$filename:$linenum";
            }
        }
        if ($count >= 3) {
            $info = $traces[2];
            if (isset($info['class'], $info['type'], $info['function'])) {
                $ex .= ',' . $info['class'] . $info['type'] . $info['function'];
            } elseif (isset($info['function'])) {
                $ex .= ',' . $info['function'];
            }
        }

        if (!empty($ex)) {
            $message = "trace[$ex] " . $message;
        }

        return $message;
    }

    /**
     * @return \app\components\log\Logger
     */
    public static function getLogger(): \app\components\log\Logger
    {
        return self::$logger;
    }

    /**
     * @param Logger $logger
     */
    public static function setLogger(Logger $logger): void
    {
        self::$logger = $logger;
    }
}