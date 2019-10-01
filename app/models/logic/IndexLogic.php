<?php

namespace app\models\logic;

use app\components\CommonConst;
use app\components\log\Log;
use app\models\data\AnswerData;
use app\models\data\AnswerSourceData;
use app\models\data\UserData;

/**
 * @uses     IndexLogic
 * @version  2019年09月08日
 * @author   oujun <oujun@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class IndexLogic extends Logic
{

    private $userData;
    private $answerData;
    private $answerSourceData;

    public function __construct()
    {
        $this->userData         = new UserData();
        $this->answerData       = new AnswerData();
        $this->answerSourceData = new AnswerSourceData();
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws \Throwable
     */
    public function saveUserInfo(array $params)
    {
        $conditions = [
            'mobile' => $params['mobile'],
            'status' => CommonConst::STATUS_YES
        ];
        $user       = $this->userData->getDetailByCondition($conditions);
        if (!empty($user)) {
            Log::error('该电话号码用户已提交问卷,params=' . json_encode($params));
            throw new \Exception('您已提交问卷');
        }

        $id = $this->userData->save($params);
        if (!$id) {
            Log::error('提交失败,params=' . json_encode($params));
            throw new \Exception('提交失败,请重试');
        }

        return ['uid' => $id];
    }

    /**
     *  计算分 算出区间
     *
     * @param array $answerList
     *
     * @return array
     */
    private function getPoint(array $answerList)
    {
        $totalPoint         = 0;
        $frustrationPoints  = 0;//抗挫能力分数
        $responsiblePoints  = 0;//责任心分数
        $debuggingPoints    = 0;//心理调试能力分数
        $assistancePoints   = 0;//团队协助能力分数
        $selfEfficacyPoints = 0;//自我效能感分数
        $subscalePoints     = 0;//测谎分数

        $allReverse   = [7, 13, 16, 18, 22, 26, 32, 41, 46, 51, 52, 53, 60, 70, 75, 87, 93, 96];
        $reversePoint = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];

        foreach ($answerList as $row) {
            $id    = $row['id'];
            $point = $row['answer'];
            if (in_array($id, $allReverse)) {
                $point = $reversePoint[$point] ?? 0;
            }

            switch (true) {
                //1.抗压因素：反映个体的挫折耐受力。
                case in_array($id, [
                    17,
                    35,
                    51,
                    66,
                    83,
                    98,
                    3,
                    19,
                    36,
                    67,
                    84,
                    96,
                    13,
                    29,
                    45,
                    61,
                    77,
                    94,
                    7,
                    24,
                    40,
                    55,
                    72,
                    88,
                    8,
                    25,
                    41,
                    56,
                    73,
                    89,
                    18,
                    33,
                    50,
                    65,
                    81,
                    99,
                    14,
                    30,
                    46,
                    62,
                    78,
                    95,
                    12,
                    28,
                    44,
                    60,
                    76,
                    92
                ]):
                    $frustrationPoints += $point;
                    break;

                //2.责任心因素：反映个体的责任感以及对工作的忠实度
                case in_array($id, [6, 23, 39, 54, 71, 74, 79, 85, 87, 5, 21, 38, 53, 57, 69, 86]):
                    $responsiblePoints += $point;
                    break;

                //3.自我调节因素：反映个体经受挫折的心理能力和行为品质
                case in_array($id, [27, 42, 43, 58, 75, 91, 16, 31, 47, 63, 66, 79, 83, 97, 98]):
                    $debuggingPoints += $point;
                    break;

                //4.团体协作因素：反映个体在团队中与他人的合作情况。
                case in_array($id, [4, 9, 10, 15, 20, 26, 32, 37, 52, 68, 85, 90, 97]):
                    $assistancePoints += $point;
                    break;

                //5.自我效能感因素
                case in_array($id, [28, 30, 39, 48, 62, 73, 76, 77, 92, 95, 100]):
                    $selfEfficacyPoints += $point;
                    break;

                //6､测谎题：包括-22,34,49,59,-70,82,-93 共8题。
                case in_array($id, [22, 34, 49, 59, 70, 82, 93]):
                    $subscalePoints += $point;
                    break;

                default:
                    break;
            }
        }

        $frustrationPoints  = ($frustrationPoints - 75) / 165 * 100;
        $responsiblePoints  = ($responsiblePoints - 23) / 57 * 100;
        $debuggingPoints    = ($debuggingPoints - 23) / 52 * 100;
        $assistancePoints   = ($assistancePoints - 23) / 41 * 100;
        $selfEfficacyPoints = ($selfEfficacyPoints - 11) / 44 * 100;

        $totalPoint += $frustrationPoints * 0.1;
        $totalPoint += $responsiblePoints * 0.3;
        $totalPoint += $debuggingPoints * 0.1;
        $totalPoint += $assistancePoints * 0.2;
        $totalPoint += $selfEfficacyPoints * 0.3;

        return [
            'totalPoints'        => (int)$totalPoint,
            'frustrationPoints'  => (int)$frustrationPoints,
            'responsiblePoints'  => (int)$responsiblePoints,
            'debuggingPoints'    => (int)$debuggingPoints,
            'assistancePoints'   => (int)$assistancePoints,
            'selfEfficacyPoints' => $selfEfficacyPoints,
            'subscalePoints'     => (int)$subscalePoints,
        ];
    }

    /**
     * @param int   $uid
     * @param array $answerList
     *
     * @return bool
     * @throws \Exception
     */
    public function saveAnswer(int $uid, array $answerList)
    {
        $user = $this->userData->getDetail($uid, CommonConst::STATUS_NO);
        if (empty($user)) {
            Log::error('用户不存在，uid=' . $uid);
            throw new \Exception('请先完善用户信息');
        }

        $pointResult = $this->getPoint($answerList);
        $pointParams = [
            'uid'                  => $uid,
            'total_points'         => $pointResult['totalPoints'] ?? 0,
            'frustration_points'   => $pointResult['frustrationPoints'] ?? 0,
            'responsible_points'   => $pointResult['responsiblePoints'] ?? 0,
            'debugging_points'     => $pointResult['debuggingPoints'] ?? 0,
            'assistance_points'    => $pointResult['assistancePoints'] ?? 0,
            'self_efficacy_points' => $pointResult['selfEfficacyPoints'] ?? 0,
            'subscale_points'      => $pointResult['subscalePoints'] ?? 0,
        ];

        $trans = \Yii::$app->db->beginTransaction();
        try {
            $this->answerSourceData->save(['uid' => $uid, 'answerList' => json_encode($answerList)]);

            $this->answerData->save($pointParams);

            $this->userData->update($uid, ['status' => CommonConst::STATUS_YES]);

            $trans->commit();
        } catch (\Throwable $e) {
            $trans->rollBack();
            Log::error('写入问卷失败,uid=' . $uid . ',answerList=' . json_encode($answerList) . ',' . $e);
            throw new \Exception('提交失败,请重新提交');
        }

        return true;
    }

}