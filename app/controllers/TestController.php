<?php

namespace app\controllers;

use app\components\Controller;
use app\components\Errno;
use app\models\dao\Demo;
use app\models\logic\DemoLogic;
use app\models\logic\MqLogic;
use hxh\components\crypt\Crypt;
use hxh\helpers\ResponseHelper;
use hxh\helpers\ValidatorHelper;
use hxh\log\Log;


/**
 * 控制器
 *
 * @uses     TestController
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class TestController extends Controller
{
    public function actionOut()
    {
        $list = [
            [
                'id'         => 1,
                'type'       => 1, // 非套餐
                'number'     => 1,
                'price'      => 12,
                'attributes' => [
                    [
                        'id'      => 1,
                        'valueId' => 1,
                    ],
                    [
                        'id'      => 2,
                        'valueId' => 2,
                    ],
                ],
            ],
            [
                'id'     => 1,
                'type'   => 2, // 套餐
                'number' => 1,
                'price'  => 12,
                'goodsList'  => [
                    [
                        'id'         => 1,
                        'type'       => 1,
                        'baseId'     => 1,
                        'number'     => 1,
                        'attributes' => [
                            [
                                'id'      => 1,
                                'valueId' => 1,
                            ],
                            [
                                'id'      => 2,
                                'valueId' => 2,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $data = [
            'goods' => json_encode($list)
        ];

        ResponseHelper::outputJson($data);
    }


    /**
     * @throws \Throwable
     */
    public function actionValidate()
    {
        $uid = ValidatorHelper::validateInteger($_REQUEST, 'uid');

        ResponseHelper::outputJson([], 'ok');
    }

    /**
     * 错误测试
     */
    public function actionError()
    {
        $this->afaf();
    }

    /**
     * 日志
     */
    public function actionLog(): void
    {
        Log::profileStart('logtime');

        $exception = new \Exception('this is exception');
        Log::error($exception);
        Log::error('error message');
        Log::info('info message');
        Log::trace('trace message');
        Log::warning('warning message');
        Log::pushlog('key', 'value');
        Log::pushlog('key2', 'value2');
        Log::counting('count', 1, 10);

        Log::profileEnd('logtime');

        var_dump('log test');
    }

    /**
     * 驼峰控制器
     */
    public function actionCamelCases(): void
    {
        var_dump('camel-cases');
    }

    /**
     * 返回数据
     *
     * @throws \yii\base\ExitException
     */
    public function actionResponse()
    {
        if (true) {
            ResponseHelper::outputJson([], 'suc');
        }

        ResponseHelper::outputJson([], 'error');
    }

    /**
     * Redis 测试
     */
    public function actionRedis()
    {
        /* @var \hxh\Redis $redis */
        $redis = \Yii::$app->redis;

        $result = $redis->set('name', 'redis', 180);
        var_dump($redis->get('name'), redis()->get('name'));

        redis()->set('ary', ['name' => 'name', 'sex' => 1]);

        var_dump(redis()->get('ary'));

        $result = redis()->mset(['key1' => 'value1', 'key2' => ['name' => 'name', 'age' => 18]], 180);
        var_dump($result);

        var_dump(redis()->mget(['key1', 'key2']));

        var_dump(redis()->mget(['key1', 'key4', 'key2', 'key3']));

        redis()->incr('incKey');
        var_dump(redis()->get('incKey'));

    }

    /**
     * Http 测试
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionHttp()
    {
        $client = new \GuzzleHttp\Client();
        $res    = $client->request('GET', 'http://www.baidu.com');
        echo $res->getBody()->getContents();
    }

    /**
     * Db 数据库测试
     */
    public function actionDb()
    {
        $demo                 = new Demo();
        $demo->name           = uniqid();
        $demo->age            = mt_rand(1, 100);
        $demo->demoCreateTime = time();

        $ret = $demo->save();
        $id  = \Yii::$app->db->getLastInsertID();
        var_dump($ret, $id);
        $result = Demo::findOne(['id' => $id])->toArray();
        var_dump($result);

        /* @var Demo $demo2 */
        $demo2 = Demo::findOne(['id' => $id]);
        var_dump($demo2->demoCreateTime);
    }

    /**
     * @throws \Exception
     */
    public function actionInner()
    {
        $result = demo_service()->getData(1, 'name');
        ResponseHelper::outputJson($result);
    }

    /**
     * Id
     */
    public function actionModel()
    {
        $logic = new MqLogic();
        $id    = $logic->getId();

        var_dump($id);
    }

    public function actionT()
    {
        $result  = \Yii::t('app', 'name', ['name' => 'stelin'], 'zh');
        $result2 = \Yii::t('app', 'name', ['name' => 'stelin'], 'en');
        var_dump($result, $result2);
        //
        $result = \Yii::t('app.test', 'name', ['name' => 'stelin'], 'zh');
        var_dump($result);
    }

    public function actionServer()
    {
        echo json_encode($_SERVER);
    }

    /**
     * @throws \Exception
     */
    public function actionCrypt()
    {
        $keys = [
            '13555555555',
            '13855555555',
            '13255555555',
        ];

        var_dump(hxh_crypt()->crypt($keys, Crypt::TYPE_ENCRYPT));

        var_dump(hxh_crypt()->crypt(['uQyFske1e5i4l2evf8lFuw'], Crypt::TYPE_DECRYPT));
    }

    /**
     * @throws \Exception
     */
    public function actionMq()
    {
        $logic  = new DemoLogic();
        $result = $logic->sendMq();

        if ($result) {
            ResponseHelper::outputJson([], 'ok');
        }

        ResponseHelper::outputJson([], 'ok', Errno::FATAL);
    }
}