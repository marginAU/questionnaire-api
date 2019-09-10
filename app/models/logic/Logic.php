<?php

namespace app\models\logic;

use app\components\Crypt;
use app\components\log\Log;

/**
 * 基础Logic
 *
 * @uses     Logic
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Logic
{
//    /**
//     * 加解密
//     *
//     * @param mixed $key
//     * @param int   $type
//     *
//     * @return mixed
//     * @throws \Exception
//     */
//    public function crypt($key, int $type = Crypt::TYPE_ENCRYPT)
//    {
//        if (empty(trim($key))) {
//            return '';
//        }
//
//        $cryptKey = Crypt::crypt([$key], $type);
//        if (!isset($cryptKey[$key])) {
//            Log::error('加解密返回数据不正确，data=' . json_encode($cryptKey, JSON_UNESCAPED_UNICODE));
//            throw new \Exception('加解密返回数据不正确，data=' . json_encode($cryptKey, JSON_UNESCAPED_UNICODE));
//        }
//
//        return $cryptKey[$key];
//    }
//
//    /**
//     * 加解密
//     *
//     * @param array $keys
//     * @param int   $type
//     *
//     * @return array
//     * @throws \Exception
//     */
//    public function crypts(array $keys, int $type = Crypt::TYPE_ENCRYPT)
//    {
//        $cryptValues = hxh_crypt()->crypt($keys, $type);
//        if (empty($cryptValues)) {
//            return [];
//        }
//
//        return $cryptValues;
//    }
//
//    /**
//     * 带索引关系加解密，支持一维及二维数组
//     *
//     * @param array $data  关联数据
//     *                     [
//     *                     'name'   => 'wo shi'
//     *                     'mobile' => '1300000000'
//     *                     ]
//     * @param array $field 字段，参与加解密的字段 如：['mobile'] 只对mobile字段进行处理
//     * @param int   $type  加解密类型
//     *
//     * @return array 被替换后的数据
//     * @throws \Exception
//     */
//    public function assocCrypts(array $data, array $field, int $type = Crypt::TYPE_ENCRYPT): array
//    {
//        if (empty($data)) {
//            return $data;
//        }
//
//        $index = key($data);
//        if (!is_numeric($index)) {
//            $data = [$data];
//        }
//
//        $mobiles = [];
//        foreach ($field as $item) {
//            $mobiles[] = array_column($data, $item);
//        }
//
//        if (empty($mobiles)) {
//            goto end;
//        }
//
//        $mobiles  = call_user_func_array('array_merge', $mobiles);
//        $mobiles  = array_unique($mobiles);
//        $cryptKey = hxh_crypt()->crypt($mobiles, $type);
//
//        foreach ($data as &$assocCrypts) {
//            foreach ($assocCrypts as $assocCryptsKey => $assocCryptsValue) {
//                if (in_array($assocCryptsKey, $field) && isset($cryptKey[$assocCryptsValue])) {
//                    $assocCrypts[$assocCryptsKey] = $cryptKey[$assocCryptsValue];
//                }
//            }
//        }
//
//        end:
//        if (!is_numeric($index)) {
//            $data = $data[0];
//        }
//
//        return $data;
//    }
}