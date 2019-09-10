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

    //问题列表
    public function questionList(int $page): array
    {
        $questionList = [
            1 => [
                ['id' => 1,],
//性别： A 男   B 女
//2,年龄：___岁
//3,民族：______
//4,政治面貌：______
//5,婚姻状态：A 已婚  B 未婚  C离异  D丧偶
//6,受教育程度： A初中及以下
//B高中/中专
//C 大专/高职
//D 大学本科
//E 硕士（包括MBA,EMBA,MPA等）
//F 博士
//7,从事气象行业工作时间：___年     单位：___________
//8,有无子女：A有  B无
//子女性别：A 男   B 女
//子女年龄：_____岁
//9,父母或近亲中是否有人在气象行业工作：A有  B无
//10,下图显示了一个10级的阶梯，这个梯子代表人们在社会在所处的位置。从01至10级，在梯子顶端的是位置最高的人们，他们的财富最多，受教育程度最高，有最好的工作；在梯子最下面的人们位置最低，他们的财富最少，受程度程度最低，工作不好或者没有工作。您认为您的家庭处于这个阶梯的哪一层？请填入1-10之间的数字。（   ）
            ]
        ];

        return ['questionList' => []];
    }


    public function saveUserInfo(array $params)
    {
        $conditions = [
            'idcard' => $params['idcard'],
            'status' => CommonConst::STATUS_YES
        ];
        $user       = $this->userData->getDetailByCondition($conditions);
        if (!empty($user)) {
            Log::error('该身份证号用户已提交问卷,params=' . json_encode($params));
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
     * todo 计算分 算出区间
     *
     * @param array $answerList
     *
     * @return array
     */
    private function getPoint(array $answerList)
    {
        $totalPoint         = 0;
        $frustrationPoints  = 0;
        $suuportPoints      = 0;//承受因子
        $tenaciousPoints    = 0;//顽强因子
        $suuportPoints      = 0;//坚定因子
        $suuportPoints      = 0;//沉着因子
        $responsiblePoints  = 0;
        $debuggingPoints    = 0;
        $assistancePoints   = 0;
        $selfEfficacyPoints = 0;
        $subscalePoints     = 0;

        //-51 待定
        $allReverse   = [7, 13, 16, 18, 22, 32, 41, 51, 46, 53, 60, 68, 70, 75, 87, 93, 96];
        $reversePoint = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];

        foreach ($answerList as $row) {
            $id    = $row['id'];
            $point = $row['answer'];
            if (in_array($id, $allReverse)) {
                $point = $reversePoint[$point] ?? 0;
            }

            switch (true) {
                //1.抗压因素：反映个体的挫折耐受力。
                //承受因子
                case in_array($id, [17, 35, 51, 66, 83, 98]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //顽强因子
                case in_array($id, [3, 19, 36, 67, 84, 96]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //坚定因子
                case in_array($id, [13, 29, 45, 61, 77, 94]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //沉着因子
                case in_array($id, [7, 24, 40, 55, 72, 88]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;

                //2.抗拉因素：反映个体的抗心理冲突能力或选择能力。
                //判断因子
                case in_array($id, [8, 25, 41, 56, 73, 89]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //决策因子
                case in_array($id, [18, 33, 50, 65, 81, 99]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //果断因子
                case in_array($id, [14, 30, 46, 62, 78, 95]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //应变因子
                case in_array($id, [12, 28, 44, 60, 76, 92]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;

                //3.责任心因素：反映个体的责任感以及对工作的忠实度
                //责任因子
                case in_array($id, [6, 23, 39, 54, 71, 74, 79, 85, 87]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //忠实因子
                case in_array($id, [5, 21, 38, 53, 57, 69, 86]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;

                //4。自我调节因素：反映个体经受挫折的心理能力和行为品质
                //适应因子
                case in_array($id, [27, 42, 43, 58, 75, 91]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;
                //调节因子
                case in_array($id, [16, 31, 47, 63, 66, 79, 83, 97, 98]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;

                //5.团体协作因素：反映个体在团队中与他人的合作情况。
                //乐群因子
                case in_array($id, [4, 9, 10, 15, 20, 26, 32, 37, 52, 68, 85, 90, 97]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;

                //6､测谎题：包括11,-22,34,49,59,-70,82,-93共8个条目。
                case in_array($id, [11, 22, 34, 49, 59, 70, 82, 93]):
                    $frustrationPoints += $point;
                    $frustrationPoints += $point;

                    $totalPoint += $point;
                    break;

                default:
                    break;
            }
        }

//        $totalPoint = $frustrationPoints + $responsiblePoints + $debuggingPoints + $assistancePoints + $selfEfficacyPoints + $subscalePoints;

        return [
            'totalPoints'        => $totalPoint,
            'frustrationPoints'  => $frustrationPoints,
            'responsiblePoints'  => $responsiblePoints,
            'debuggingPoints'    => $debuggingPoints,
            'assistancePoints'   => $assistancePoints,
            'selfEfficacyPoints' => $selfEfficacyPoints,
        ];

    }

    /**
     * @param int   $uid
     * @param array $answerList
     *
     * @return bool
     * @throws \yii\db\Exception
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