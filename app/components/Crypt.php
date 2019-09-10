<?php

namespace app\components;

/**
 * 加解密服务
 *
 * @uses     Crypt
 * @version  2018年08月06日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Crypt extends BaseComponent
{
    /**
     * 加密
     */
    const TYPE_ENCRYPT = 1;

    /**
     * 解密
     */
    const TYPE_DECRYPT = 2;

    /**
     * @var int
     */
    public $timeout = 3;

    /**
     * @var string
     */
    public $uri = '/inner/crypt';

    /**
     * 加解密
     *
     * @param array $keys 加解密key数组
     * @param int   $type 类型1=加密 2=解密
     *
     * @return array
     * @example
     * [
     *   "fHkd7rGMDvIBSnPdyUyxcg==" => "135xxxxxxx",
     *   "nwC2b79gEz4Bc2OxvLXWUQ==" => "185xxxxxxx"
     *   ......
     * ]
     *
     * @throws \Exception
     */
    public function crypt(array $keys, int $type = self::TYPE_ENCRYPT): array
    {
        $notEmptyKeys = [];
        foreach ($keys as $key) {
            if (!empty(trim($key))) {
                $notEmptyKeys[] = $key;
            }
        }

        if (empty($notEmptyKeys)) {
            return [];
        }

//        $data = [
//            'keys' => implode(',', $notEmptyKeys),
//            'type' => $type,
//        ];

        $url = $this->getHost($this->uri);
//        $result = InnerHelper::post($url, $data, $this->timeout);

        return $result ?? [];
    }
}