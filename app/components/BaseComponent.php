<?php

namespace app\components;

use yii\base\Component;

/**
 * 基础组件
 *
 * @uses     BaseComponent
 * @version  2018年08月14日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class BaseComponent extends Component
{
    /**
     * 发送host
     *
     * @var string
     */
    public $host = 'http://127.0.0.1';

    /**
     * 获取URL接口地址
     *
     * @return string
     * @throws \Exception
     */
    protected function getHost(string $uri): string
    {
        $className = strtolower(static::class);
        $name = str_replace('\\', '/', $className);
        $name = basename($name);

        // 解析host
        $config = require __DIR__ . '/../config/config.php';
        if (!isset($config[$name]['host'])) {
            return $this->host . $uri;
        }

        $host = $config[$name]['host'];

        return $host . $uri;
    }
}