<?php

namespace app\models\logic;

use app\components\Opcode;
use app\models\data\DemoData;
use hxh\helpers\MqHelper;
use app\components\log\Log;

/**
 * Logic
 *
 * @uses     DemoLogic
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class DemoLogic extends Logic
{
    /**
     * @var DemoData
     */
    private $demoData;

    /**
     * DemoLogic constructor.
     */
    public function __construct()
    {
        $this->demoData = new DemoData();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->demoData->getId();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getInfo(): array
    {
        return $this->demoData->getInfo();
    }

    /**
     * @throws \Exception
     */
    public function sendMq(): bool
    {
        $uid    = 12;
        $opinfo = [
            'name' => 'stelin',
            'age'  => 19,
        ];


        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $result = $this->demoData->add();
            if (!$result) {
                throw new \Exception('操作失败');
            }

            MqHelper::addMqMessage(Opcode::DEMO_ADD, $opinfo, $uid);

            $transaction->commit();

            MqHelper::send();
        } catch (\Throwable $e) {
            Log::error($e);
            $transaction->rollBack();
            return false;
        }

        return true;
    }

    /**
     * @param string $msgid
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public function doMq(string $msgid): bool
    {

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $result = $this->demoData->add();
            if (!$result) {
                throw new \Exception('操作失败');
            }

            MqHelper::addUnique($msgid);

            $transaction->commit();
        } catch (\Throwable $e) {
            Log::error($e);
            $transaction->rollBack();
            return false;
        }

        return true;
    }

}