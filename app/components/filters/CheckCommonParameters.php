<?php

namespace app\components\filters;

use app\helpers\HeaderHelper;
use app\helpers\ValidatorHelper;
use yii\base\ActionFilter;

/**
 * 公共参数验证
 *
 * @uses     CheckCommonParameters
 * @version  2018年08月14日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class CheckCommonParameters extends ActionFilter
{
    /**
     * @var int
     */
    public $uid = 0;


    /**
     * @var string
     */
    public $token = '';

    /**
     * @var string
     */
    public $lon = 0;

    /**
     * @var string
     */
    public $lat = 0;


    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \Exception
     */
    public function beforeAction($action): bool
    {
        $headers = headers();

        $this->uid   = ValidatorHelper::validateInteger($headers, 'uid', null, null, 0);
        $this->token = ValidatorHelper::validateString($headers, 'token', null, null, '');
        $this->lon   = ValidatorHelper::validateString($headers, 'lon', null, null, 0);
        $this->lat   = ValidatorHelper::validateString($headers, 'lat', null, null, 0);

        $data = [
            'uid'   => $this->uid,
            'token' => $this->token,
            'lon'   => $this->lon,
            'lat'   => $this->lat,
        ];

        HeaderHelper::init($data);

        return true;
    }
}