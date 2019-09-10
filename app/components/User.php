<?php

namespace app\components;

/**
 * 用户验证
 *
 * @uses     User
 * @version  2018年06月05日
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class User extends \yii\web\User
{
    /**
     * @var int
     */
    private $uid;

    /**
     * @param bool $autoRenew
     *
     * @return int|null|\yii\web\IdentityInterface
     */
    public function getIdentity($autoRenew = true)
    {
        return $this->uid;
    }

    /**
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }
}