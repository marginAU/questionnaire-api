<?php

namespace app\components;

use yii\redis\Connection;

/**
 * 重写 redis方法
 *
 * @uses     Redis
 * @version  2018年05月29日
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Redis extends Connection
{
    /**
     * @var string
     */
    public $profilePrefix = 'redis.';

    /**
     * @var string
     */
    public $countingPrefix = 'redis.hit/req.';


    /**
     * 获取KEY值
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        $result = parent::get($key);
//        Log::counting($this->countingPrefix . __FUNCTION__, $result == false ? 0 : 1, 1);

        if ($result == false) {
            return false;
        }

        $data = @unserialize($result);
        if ($data === false) {
            return $result;
        }

        return $data;
    }

    /**
     * 设置KEY值，value为数组
     *
     * @param string $key    key值
     * @param mixed  $value  value值
     * @param int    $expire 默认永久
     *
     * @return mixed
     */
    public function set(string $key, $value, $expire = 0)
    {
        $value = serialize($value);
        if ($expire > 0) {
            $ret = parent::setex($key, $expire, $value);
        } else {
            $ret = parent::set($key, $value);
        }

        return $ret;
    }

    /**
     * 设置多个KEY-VALUE
     *
     * @param array $keyValues values数组
     * @param int   $expire    默认永久
     *
     * @return mixed
     */
    public function mset(array $keyValues, $expire = 0)
    {
        $keyValuePairs = [];
        foreach ($keyValues as $key => $value) {
            $keyValuePairs[] = $key;
            $keyValuePairs[] = serialize($value);
        }

        $result = parent::mset(...$keyValuePairs);
        foreach ($keyValues as $k => $v) {
            if ($expire != 0) {
                parent::expire($k, $expire);
            }
        }

        $result;
    }

    /**
     * 获取多个KEY值
     *
     * @param array $keys
     *
     * @return array|bool|mixed
     */
    public function mget(array $keys)
    {
        $result = parent::mget(...$keys);
        if ($result == false || !is_array($keys)) {
            return false;
        }

        $data = [];
        foreach ($result as $key => $value) {
            if ($value == false) {
                continue;
            }

            $sValue = @unserialize($value);
            $sValue = ($sValue === false) ? $value : $sValue;

            $index        = $keys[$key];
            $data[$index] = $sValue;
        }

//        Log::counting($this->countingPrefix . __FUNCTION__, count($data), count($keys));

        return $data;
    }


    /**
     * 魔术方法
     *
     * @param string $name
     * @param array  $params
     *
     * @return mixed
     */
    public function __call($name, $params)
    {
//        Log::profileStart($this->profilePrefix . $name);
        $result = parent::__call($name, $params);
//        Log::profileEnd($this->profilePrefix . $name);

        return $result;
    }

    /**
     * @desc批量写入hash
     *
     * @param string $key
     * @param array  $keyValues
     * @param int    $expire
     *
     * @return mixed
     */
    public function hmset(string $key, array $keyValues = [], $expire = 0)
    {
        foreach ($keyValues as $k => $v) {
            parent::hset($key, $k, $v);
        }
        if (!$expire) {
            parent::expire($key, $expire);
        }
    }

    /**
     * @desc批量获取hash
     * @param string $key
     * @param array  $hashKeys
     *
     * @return mixed
     */
    public function hmget(string $key, array $hashKeys = [])
    {
        return parent::hmget($key, ...$hashKeys);

    }

    /**
     * @desc 获取所有hash
     * @param string $key
     * @param array  $default
     *
     * @return array|mixed
     */
    public function hgetall(string $key,$default = [])
    {
        $data = [];

        if (!parent::exists($key)) {
            return $default;
        }
        $keys = parent::hkeys($key);
        $vals = parent::hvals($key);

        for ($i = 0; $i < count($keys); $i++) {
            if (isset($keys[$i])) {
                $data[$keys[$i]] = $vals[$i];
            }
        }
        return $data;
    }

}