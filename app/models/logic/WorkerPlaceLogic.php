<?php

namespace app\models\logic;

use app\models\data\WorkerPlaceData;

/**
 *
 * workerPlaceLogic
 *
 * @uses     WorkerPlaceLogic
 * @version  2019-12-31
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class WorkerPlaceLogic
{

    private $workerPlaceData;

    public function __construct()
    {
        $this->workerPlaceData = new WorkerPlaceData();
    }

    public function getList()
    {
        return $this->workerPlaceData->getList([], 1, 999);
    }

}
