<?php

namespace app\components;

use app\components\filters\CheckCommonParameters;
use app\components\filters\CheckUserToken;
use app\components\log\Log;
use app\helpers\ResponseHelper;
use yii\base\InlineAction;
use yii\filters\AccessControl;

/**
 * 基类控制器
 *
 * @uses     Controller
 * @version  2018年05月29日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Controller extends \yii\web\Controller
{
    /**
     * Init
     */
    public function init()
    {
        parent::init();

        Log::pushlog('params', $_REQUEST);
        Log::pushlog('servers', $_SERVER);
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'checkCommonParameters' => array(
                'class' => CheckCommonParameters::class,
            ),
            'checkUserToken'        => [
                'class' => CheckUserToken::class,
            ],
            'access'                => [
                'class'        => AccessControl::class,
                'rules'        => $this->accessRules(),
                'denyCallback' => [$this, 'accessDenied'],
            ],
        ];
    }

    /**
     * 验证失败处理
     *
     * @param $rule
     * @param $action
     *
     * @throws \yii\base\ExitException
     */
    public function accessDenied($rule, $action)
    {
        Log::warning("need login!");
        ResponseHelper::outputJson([], "Need Login!", 304);
    }

    /**
     * @return array
     */
    public function accessRules(): array
    {
        return [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'allow'   => true,
                'actions' => ['login'],
                'roles'   => ['?'],
            ],
            [
                'allow'       => true,
                'controllers' => ['admin'],
                'actions'     => ['download-answer'],
                'roles'       => ['?'],
            ],
        ];
    }

    /**
     * @param string $id
     *
     * @return null|object|\yii\base\Action|\yii\base\InlineAction
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function createAction($id)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }

        $actionMap = $this->actions();
        if (isset($actionMap[$id])) {
            return \Yii::createObject($actionMap[$id], [$id, $this]);
        } elseif (preg_match('/^[a-zA-Z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));
            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);
                if ($method->isPublic() && $method->getName() === $methodName) {
                    return new InlineAction($id, $this, $methodName);
                }
            }
        }

        return null;
    }

    /**
     * @param string $id
     * @param array  $params
     *
     * @return mixed|null
     *
     * @throws \yii\base\ExitException
     */
    public function runAction($id, $params = [])
    {
        $result = null;

        try {
            $result = parent::runAction($id, $params);
        } catch (\Throwable $throwable) {
            Log::error($throwable);

            ResponseHelper::outputJson([], $throwable->getMessage(), Errno::FATAL);
        }

        return $result;
    }
}