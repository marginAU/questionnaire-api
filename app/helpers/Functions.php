<?php

if (!function_exists('headers')) {
    /**
     * @return array
     */
    function headers(): array
    {
        $headers   = [];
        $headerAry = request()->headers->toArray();

        foreach ($headerAry as $key => $value) {
            if (!isset($value[0])) {
                continue;
            }

            $headers[$key] = $value[0];
        }

        return $headers;
    }
}

if (!function_exists('request')) {

    /**
     * 获取request对象
     *
     * @param null $key     key
     * @param null $default 默认值
     *
     * @return mixed|\yii\console\Request|\yii\web\Request
     */
    function request($key = null, $default = null)
    {
        $request = Yii::$app->request;
        if ($key) {
            return $request->getBodyParam($key, $default);
        }

        return $request;
    }
}
if (!function_exists('get_traceid')) {
    /**
     * 日志全局唯一ID
     *
     * @return string
     */
    function get_traceid(): string
    {
        if (isset($_SERVER['HTTP_TRACEID']) && !empty($_SERVER['HTTP_TRACEID'])) {
            return strval($_SERVER['HTTP_TRACEID']);
        }

        $items = [
            gethostname(),
            uniqid(),
            microtime(true),
            mt_rand(1000, 9999),
        ];

        $traceid = implode('_', $items);
        $traceid = md5($traceid);

        $_SERVER['HTTP_TRACEID'] = $traceid;

        return $traceid;
    }
}

if (!function_exists('get_spanid')) {
    /**
     * 当前系统日志ID
     *
     * @return string
     */
    function get_spanid(): string
    {
        if (isset($_SERVER['SERVER_SPANID']) && !empty($_SERVER['SERVER_SPANID'])) {
            return strval($_SERVER['SERVER_SPANID']);
        }

        return uniqid();
    }
}

if (!function_exists('get_parentid')) {
    /**
     * 父请求日志ID(spanid)
     *
     * @return string
     */
    function get_parentid(): string
    {
        if (isset($_SERVER['HTTP_PARENTID']) && !empty($_SERVER['HTTP_PARENTID'])) {
            return strval($_SERVER['HTTP_PARENTID']);
        }

        return '0';
    }
}
if (!function_exists('select_db')) {
    /**
     * 初始化数据库切换
     *
     * @param array $config
     *
     * @return array
     */
    function select_db(array $config): array
    {
        if (!isset($config['params']['sub_database']) || $config['params']['sub_database'] == false) {
            return $config;
        }

        $bid = get_bid();
        if ($bid == 0) {
            return $config;
        }

        $config = select_db_by_bid($config, $bid);

        return $config;
    }
}

if (!function_exists('select_db_by_bid')) {
    /**
     * 根据品牌ID选择数据库
     *
     * @param array $config
     * @param int   $bid
     *
     * @return array
     */
    function select_db_by_bid(array $config, int $bid): array
    {
        if (isset($config['components']['db']['dsn'])) {
            $config['components']['db']['dsn'] = set_brand_db($bid, $config['components']['db']['dsn']);
        }

        if (isset($config['components']['db']['slaves']) && is_array($config['components']['db']['slaves'])) {
            $slaves = $config['components']['db']['slaves'];
            foreach ($slaves as $index => $slave) {
                if (!isset($slave['dsn'])) {
                    continue;
                }
                $config['components']['db']['slaves'][$index]['dsn'] = set_brand_db($bid, $slave['dsn']);
            }
        }

        return $config;
    }
}

if (!function_exists('set_brand_db')) {
    /**
     * 设置品牌数据库
     *
     * @param int    $bid
     * @param string $dsn
     *
     * @return string
     */
    function set_brand_db(int $bid, string $dsn): string
    {
        $dsnAry = explode(';', $dsn);
        foreach ($dsnAry as &$dsnItem) {
            list($key, $dbName) = explode('=', $dsnItem);
            if (strtoupper($key) == 'DBNAME') {
                $dbName = sprintf('%s_%d', $dbName, $bid);
            }

            $dsnItem = sprintf('%s=%s', $key, $dbName);
        }

        $dsn = implode(';', $dsnAry);

        return $dsn;
    }
}
if (!function_exists('get_uniqid')) {
    /**
     * 生成一个唯一字符串
     *
     * @return string
     */
    function get_uniqid(): string
    {
        $array  = [
            gethostname(),
            uniqid(),
            microtime(true),
            mt_rand(1, 9999),
        ];
        $result = md5(implode('@', $array));

        return $result;
    }
}
if (!function_exists('redis')) {
    /**
     * @return \app\Components\Redis
     */
    function redis()
    {
        return \Yii::$app->redis;
    }
}
