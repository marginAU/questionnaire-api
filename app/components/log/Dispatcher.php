<?php

namespace app\components\log;

/**
 * 重写日志分发器
 *
 * @uses     Dispatcher
 * @version  2018年05月28日
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Dispatcher extends \yii\log\Dispatcher
{
    /**
     * Dispatcher constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        Log::setLogger($this->getLogger());
    }
}