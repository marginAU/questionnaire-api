<?php

namespace app\models\dao;

/**
 * @property integer $id
 * @property string  $answerList 原始答案
 * @property integer $uid        用户id
 * @property integer $ctime      提交时间
 *
 * AnswerSource
 *
 * @uses     AnswerSource
 * @version  2019-09-09
 * @author   Generate
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class AnswerSource extends Model
{
    public static function tableName()
    {
        return 'answer_source';
    }

    public function maps(): array
    {
        return [
            'answer_list' => 'answerList',
        ];
    }
}
