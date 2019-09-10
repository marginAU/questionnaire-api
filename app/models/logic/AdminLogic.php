<?php


namespace app\models\logic;

use app\components\CacheConst;
use app\components\CommonConst;
use app\components\log\Log;
use app\models\data\AdminUserData;
use app\models\data\AnswerData;
use app\models\data\UserData;
use yii\redis\Connection;

/**
 * @uses     AdminLogic
 * @version  2019年09月08日
 * @author   oujun <oujun@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class AdminLogic extends Logic
{

    private $adminData;
    private $userData;
    private $answerData;

    private static $expire = 3600 * 12;
    //'frustrationContent'  => $user['frustrationContent'],
    //                'responsibleContent'  => $user['responsibleContent'],
    //                'debuggingContent'    => $user['debuggingContent'],
    //                'assistanceContent'   => $user['assistanceContent'],
    //                'selfEfficacyContent' => $user['selfEfficacyContent'],
    //                'subscaleContent'     => $user['subscaleContent'],
    //总体
    const TOTAL_CONTENT = [

    ];
    //抗挫能力分数
    const FRUSTRATION_CONTENT = [

    ];
    //责任心分数
    const RESPONSIBLE_CONTENT = [

    ];

    //心理调试能力分数
    const DEBUGGING_CONTENT = [

    ];

    //团队协助能力分数
    const ASSISTANCE_CONTENT = [

    ];

    //自我效能感分数
    const SELF_EFFICACY_CONTENT = [

    ];

    //分量表分数
    const SUBSCALE_CONTENT = [

    ];

    public function __construct()
    {
        $this->adminData  = new AdminUserData();
        $this->userData   = new UserData();
        $this->answerData = new AnswerData();
    }

    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     *
     * @return array
     * @throws \Exception
     */
    public function login(string $username, string $password)
    {
        $user = $this->adminData->getDetail($username);
        if (empty($user)) {
            Log::error('用户不存在，username=' . $username);
            throw new \Exception('用户不存在');
        }

        $password = md5(md5($user->salt) . $password);
        if ($password != $user->password) {
            Log::error('用户密码不正确，username=' . $username . ',password=' . $password);
            throw new \Exception('用户用户密码不正确');
        }

        [$token, $expires] = $this->setToken($user->id);
        $user->token   = $token;
        $user->expires = $expires;
        $user->save();

        return [
            'uid'      => $user->id,
            'username' => $user->username,
            'token'    => $token,
        ];
    }

    /**
     * @param int    $uid
     * @param string $token
     *
     * @return bool
     */
    public function checkToken(int $uid, string $token): bool
    {
        $tokenArr = $this->getToken($uid);
        if (empty($tokenArr['token']) || $tokenArr['token'] != $token || $tokenArr['expire'] <= time()) {
            Log::error('token无效,uid=' . $uid . ',token=' . $token);
            return false;
//            throw new \Exception('token无效');
        }

        return true;
    }

    /**
     * @param int $uid
     *
     * @return array|mixed
     */
    private function getToken(int $uid)
    {
        $key   = $this->loginInfoKey($uid);
        $token = redis()->get($key);
        if (empty($token)) {
            $user  = $this->adminData->getDetailByUid($uid);
            $token = empty($user) ? [] : $user->toArray();
        }

        return $token;
    }

    private function loginInfoKey($uid)
    {
        return $key = CacheConst::LOGIN_INFO . $uid;
    }

    /**
     * @param $uid
     *
     * @return array
     */
    private function setToken($uid): array
    {
        $expire   = self::$expire;
        $token    = get_uniqid();
        $tokenArr = [
            'token'  => $token,
            'expire' => $expire + time(),
        ];

        $key = $this->loginInfoKey($uid);
        redis()->set($key, $tokenArr, $expire);

        return [$token, $expire + time()];
    }

    /**
     * 退出
     *
     * @param int $uid
     */
    public function loginOut(int $uid): void
    {
        $key = $this->loginInfoKey($uid);
        redis()->expire($key, 0);

        $this->adminData->update($uid, ['token' => '']);
    }

    /**
     * 答案列表
     *
     * @param string $startTime
     * @param string $endTime
     * @param int    $page
     * @param int    $size
     *
     * @return array
     */
    public function answerList(string $startTime, string $endTime, int $page, int $size)
    {
        $conditions = [
            'and',
            ['>=', 'ctime', strtotime($startTime)],
            ['<', 'ctime', strtotime($endTime.' 23:59:59')],
            ['status' => CommonConst::STATUS_YES]
        ];

        $count    = $this->userData->getCount($conditions);
        $userList = $this->userData->getList($conditions, $page, $size);
        if (empty($userList)) {
            return [
                'page'  => $page,
                'total' => $count,
                'list'  => []
            ];
        }

        $uids       = array_column($userList, 'id');
        $answerList = $this->answerData->getList(['uid' => $uids]);
        $answerList = array_column($answerList, null, 'uid');

        foreach ($userList as &$user) {
            $user   = $user->toArray();
            $answer = empty($answerList[$user['id']]) ? [] : $answerList[$user['id']]->toArray();
            $user   = [
                'id'          => $user['id'],
                'username'    => $user['username'],
                'idcard'      => $user['idcard'],
                'sex'         => $user['sex'],
                'mobile'      => $user['mobile'],
                'sourcePlace' => $user['sourcePlace'],
                'position'    => $user['position'],
                'totalPoints' => $answer['totalPoints'] ?? 0,
                'ctime'       => date('Y-m-d H:i:s', $user['ctime']),
            ];
        }

        return [
            'page'  => $page,
            'total' => $count,
            'list'  => $userList
        ];


    }

//下载
    public function download(string $startTime, string $endTime)
    {

    }

    /**
     * 答案分析详情
     *
     * @param int $uid
     *
     * @return array
     * @throws \Exception
     */
    public function answerDetail(int $uid)
    {
        $user   = $this->userData->getDetail($uid);
        $answer = $this->answerData->getDetail($uid);

        if (empty($user) || empty($answer)) {
            Log::error('问卷不存在,uid' . $uid);
            throw new \Exception('问卷不存在');
        }

        $user    = $user->toArray();
        $answer  = empty($answer) ? [] : $answer->toArray();
        $useTime = ($answer['ctime'] ?? 0) - $user['ctime'];
        return [
            'answerDetail' => [
                'id'                  => $uid,
                'username'            => $user['username'],
                'idcard'              => $user['idcard'],
                'sex'                 => $user['sex'],
                'mobile'              => $user['mobile'],
                'sourcePlace'         => $user['sourcePlace'],
                'position'            => $user['position'],
                'useTime'             => $useTime < 0 ? 0 : $useTime,
                'ctime'               => date('Y-m-d H:i:s', $user['ctime']),
                'totalPoints'         => $answer['totalPoints'],
                'frustrationPoints'   => $answer['frustrationPoints'],
                'responsiblePoints'   => $answer['responsiblePoints'],
                'debuggingPoints'     => $answer['debuggingPoints'],
                'assistancePoints'    => $answer['assistancePoints'],
                'selfEfficacyPoints'  => $answer['selfEfficacyPoints'],
                'subscalePoints'      => $answer['subscalePoints'],
                'totalPointsContent'  => self::TOTAL_CONTENT[$answer['totalPoints']] ?? '',
                'frustrationContent'  => self::FRUSTRATION_CONTENT[$answer['frustrationPoints']] ?? '',
                'responsibleContent'  => self::RESPONSIBLE_CONTENT[$answer['frustrationPoints']] ?? '',
                'debuggingContent'    => self::DEBUGGING_CONTENT[$answer['frustrationPoints']] ?? '',
                'assistanceContent'   => self::ASSISTANCE_CONTENT[$answer['frustrationPoints']] ?? '',
                'selfEfficacyContent' => self::SELF_EFFICACY_CONTENT[$answer['frustrationPoints']] ?? '',
                'subscaleContent'     => self::SUBSCALE_CONTENT[$answer['frustrationPoints']] ?? '',
            ]
        ];

    }


}