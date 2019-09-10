<?php

namespace app\models\data;


use app\components\CommonConst;
use app\models\dao\Answer;

/**
 *
 * answerData
 *
 * @uses     AnswerData
 * @version  2019-09-09
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class AnswerData
{
    /**
     * @param array $params
     *
     * @return bool
     */
    public function save(array $params): bool
    {
        $params['ctime'] = time();

        $model = new Answer();

        $model->setAttributes($params, false);
        return $model->save();
    }


    public function getList(array $cond)
    {
        return Answer::find()->where($cond)->orderBy('ctime DESC')->all();
    }


    public function getDetail(int $uid)
    {
        return Answer::find()->where(['uid' => $uid])->orderBy('ctime DESC')->one();
    }
}
