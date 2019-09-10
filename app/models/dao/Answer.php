<?php

namespace app\models\dao;

/**
 * @property integer $id
 * @property integer $uid                 用户id
 * @property integer $totalPoints         总分
 * @property integer $frustrationPoints   抗挫能力分数
 * @property integer $responsiblePoints   责任心分数
 * @property integer $debuggingPoints     心理调试能力分数
 * @property integer $assistancePoints    团队协助能力分数
 * @property integer $selfEfficacyPoints  自我效能感分数
 * @property integer $subscalePoints      分量表分数
 * @property integer $ctime               创建时间
 *
 * Answer
 *
 * @uses     Answer
 * @version  2019-09-09
 * @author   Generate
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Answer extends Model
{
    public static function tableName()
    {
        return 'answer';
    }

    public function maps(): array
    {
        return [
            'total_points'         => 'totalPoints',
            'frustration_points'   => 'frustrationPoints',
            'responsible_points'   => 'responsiblePoints',
            'debugging_points'     => 'debuggingPoints',
            'assistance_points'    => 'assistancePoints',
            'self_efficacy_points' => 'selfEfficacyPoints',
            'subscale_points'      => 'subscalePoints',
        ];
    }
}
