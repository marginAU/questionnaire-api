<?php

namespace app\commands;

use hxh\Command;

/**
 * 测试参数
 *
 * @uses     TestController
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class TestController extends Command
{
    /**
     * php yii index/index "params1" "params2"
     *
     * @param string $p1
     * @param string $p2
     */
    public function actionIndex(string $p1 = "", string $p2 = ""): void
    {
        echo 'bid=' . get_bid() . PHP_EOL;
        echo "command success! p1=$p1, p2=$p2 \n";
    }
}