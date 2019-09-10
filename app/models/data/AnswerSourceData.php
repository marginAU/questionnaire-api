<?php

namespace app\models\data;

use app\models\dao\AnswerSource;

/**
 *
 * answerSourceData
 *
 * @uses     AnswerSourceData
 * @version  2019-09-09
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class AnswerSourceData
{
    /**
     * @param array $params
     *
     * @return bool
     */
    public function save(array $params): bool
    {
        $params['ctime'] = time();

        $model = new AnswerSource();

        $model->setAttributes($params, false);
        return $model->save();
    }


    public function getList(array $cond)
    {
        return AnswerSource::find()->where($cond)->orderBy('ctime DESC')->all();
    }


    public function getDetail(int $uid)
    {
        return AnswerSource::find()->where(['uid' => $uid])->orderBy('ctime DESC')->one();
    }
}
