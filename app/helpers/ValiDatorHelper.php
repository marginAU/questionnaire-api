<?php

namespace app\helpers;

use yii\validators\IpValidator;

/**
 * 通用验证器.
 *
 * @uses     \ValidatorHelper
 *
 * @version  2018年05月29日
 *
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class ValidatorHelper
{
    /**
     * 整形正则过滤.
     *
     * @var string
     */
    private static $numberPattern = '/^\s*[+-]?\d+\s*$/';

    /**
     * 浮点正则过滤.
     *
     * @var string
     */
    private static $floatPattern = '/^(-?\d+)(\.\d+)+$/';

    /**
     * 手机号正则.
     *
     * @var string
     */
    private static $mobilePattern = '/^1\d{10}$/';

    /**
     * 身份证号验证
     *
     * @var string
     */
    private static $idcardPattern = '/^\d{17}[0-9xX]{1}$/';

    /**
     * 邮箱正则.
     *
     * @var string
     */
    private static $emailPattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/';

    /**
     * 图片正则
     *
     * @var string
     */
    private static $imagePattern = '/.*(\.png|\.jpg|\.jpeg|\.gif)$/';

    /**
     * 参数是否为空验证，如果不定义默认值，参数为空验证不通过，抛出异常.
     *
     * @param array|mixed $mixed   源数据
     * @param string      $porp    取值key
     * @param mixed       $default 默认值
     *
     * @throws \Exception
     *
     * @return string
     *
     * 1. 验证一个变量是否为空
     * ValidatorHelper::checkEmpty($value);
     *
     * 2. 验证数组中一个key的值是否为空(POST|GET)
     * ValidatorHelper::checkEmpty($_POST, "sp", 10);设置默认值
     * ValidatorHelper::checkEmpty($_POST, "name");  未设置默认值，为空抛出异常
     */
    public static function checkEmpty($mixed, $porp = null, $default = null)
    {
        if (is_array($mixed)) {
            if (isset($mixed[$porp])) {
                $value = $mixed[$porp];
            } elseif ($default === null) {
                throw new \Exception("$porp is not json format!", 406);
            } else {
                return $default;
            }
        } else {
            $value = $mixed;
        }
        // 处理空白字符
        if (is_string($value)) {
            $value = trim($value);
        }

        if (!isset($value) || $value === null || strlen($value) == 0) {
            if ($default === null) {
                throw new \Exception("$porp is not json format!", 406);

            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * 参数是否为整数验证，如果不设置默认值，参数验证不通过，抛出异常.
     *
     * @param array|mixed $mixed   源数据
     * @param string      $porp    取值key
     * @param int         $min     最小值(不小于)
     * @param int         $max     最大值(不大于)
     * @param int         $default 默认值
     *
     * @throws \Exception
     *
     * @return int
     *
     * 1. 验证一个变量是否为整形
     * ValidatorHelper::validateInteger($value);
     *
     * 2. 验证数组中一个key的值是否为整形(POST|GET)
     * ValidatorHelper::validateInteger($_GET, "age", 10, 100, 20); 设置默认值
     * ValidatorHelper::validateInteger($_GET, "age", 10, 100);     未设置默认值，验证不通过，抛出异常
     */
    public static function validateInteger($mixed, $porp = null, $min = null, $max = null, $default = null): int
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        if (!preg_match(self::$numberPattern, "$value")) {
            if ($default === null) {
                throw new \Exception("$porp must be an integer.", 406);
            } else {
                return $default;
            }
        }

        $value = intval($value);

        if ($min !== null && $value < $min) {
            if ($default === null) {
                throw new \Exception("$porp is too small (minimum is $min)", 406);
            } else {
                return $default;
            }
        }
        if ($max !== null && $value > $max) {
            if ($default === null) {
                throw new \Exception("$porp is too big (maximum is $max)", 406);
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * 浮点数验证，如果没有设置默认值，参数验证不通过，抛出异常.
     *
     * @param array|mixed $mixed   源数据
     * @param string      $porp    取值key
     * @param float|int   $min     最小(不小于)
     * @param float|int   $max     最大(不大于)
     * @param float       $default 默认值
     *
     * @throws \Exception
     *
     * @return float
     *
     * 1. 验证一个变量是否为浮点
     * ValidatorHelper::validateFloat($value);
     *
     * 2. 验证数组中一个key的值是否为浮点(POST|GET)
     * ValidatorHelper::validateFloat($_GET, "cost", 10, 100, 20); 设置默认值
     * ValidatorHelper::validateFloat($_GET, "cost", 10, 100);     未设置默认值，验证不通过，抛出异常
     */
    public static function validateFloat($mixed, $porp = null, $min = null, $max = null, $default = null): float
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        if (!preg_match(self::$floatPattern, "$value")) {
            if ($default === null) {
                throw new \Exception("$porp must be an float.", 406);
            } else {
                return $default;
            }
        }

        $value = floatval($value);

        if ($min !== null && $value < $min) {
            throw new \Exception("$porp is too small (minimum is $min)", 406);
        }

        if ($max !== null && $value > $max) {
            throw new \Exception("$porp is too big (maximum is $max)", 406);
        }

        return $value;
    }

    /**
     * 字符串验证(兼容中文)，如果没有设置默认值，参数验证不通过，抛出异常.
     *
     * @param array|mixed $mixed   源数据
     * @param string      $porp    取值key
     * @param int         $min     最小长度
     * @param int         $max     最大长度
     * @param string      $default 默认值
     *
     * @throws \Exception
     *
     * @return string
     *
     * 1. 验证一个变量是否为字符串且满足一定的长度限制
     * ValidatorHelper::validateString($value, null, 10, 20);
     *
     * 2. 验证数组中一个key的值是否满足字符串长度限制(POST|GET)
     * ValidatorHelper::validateString($_GET, "desc", 10, 100, 20); 设置默认值
     * ValidatorHelper::validateString($_GET, "desc", 10, 100);     未设置默认值，验证不通过，抛出异常
     */
    public static function validateString($mixed, $porp = null, $min = null, $max = null, $default = null): string
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        //字符串特殊处理， 如果设置了默认值且字符串为空， 则返回默认值
        if ($default !== null) {
            if (empty($value)) {
                return $default;
            }
        }
        $length = mb_strlen($value); // 这里不能用strlen，字符串长度跟编码有关
        if ($min !== null && $length < $min) {
            throw new \Exception("$porp is too short (minimum is $min characters)", 406);
        }

        if ($max !== null && $length > $max) {
            throw new \Exception("$porp is too long (maximum is $max characters)", 406);
        }

        return $value;
    }

    /**
     * 字符串枚举类型验证，如果没有设置默认值，验证不通过，抛出异常
     * 验证一个值是否在枚举的数组中存在.
     *
     * @param array|string $mixed       源数据
     * @param string       $porp        取值key
     * @param array        $validValues 枚举数组
     * @param string       $default     默认值
     *
     * @throws \Exception
     *
     * @return string
     *
     * 1. 验证一个变量是在数组中
     * ValidatorHelper::validateEnumString($value, null, array("val", "val2"));
     *
     * 2. 验证数组中一个key的值是否在枚举数组中存在(POST|GET)
     * ValidatorHelper::validateEnumString($_GET, "type", array("home", "user"), "user"); 设置默认值
     * ValidatorHelper::validateEnumString($_GET, "type", array("home", "user"));     未设置默认值，验证不通过，抛出异常
     */
    public static function validateEnumString($mixed, $porp, $validValues, $default = null): string
    {
        $value = self::validateString($mixed, $porp, null, null, $default);
        if ($default === null && !in_array($value, $validValues)) {
            throw new \Exception("$porp is not valid enum!", 406);
        }

        return $value;
    }

    /**
     * 整数枚举类型验证，如果没有设置默认值，验证不通过，抛出异常
     * 验证一个值是否在枚举的数组中存在.
     *
     * @param array|int $mixed       源数据
     * @param string    $porp        取值key
     * @param array     $validValues 枚举数组
     * @param int       $default     默认值
     *
     * @throws \Exception
     *
     * @return int
     *
     * 1. 验证一个变量是在数组中
     * ValidatorHelper::validateEnumInteger($value, null, array(1, 2));
     *
     * 2. 验证数组中一个key的值是否在枚举数组中存在(POST|GET)
     * ValidatorHelper::validateEnumInteger($_GET, "type", array(1, 2), 1); 设置默认值
     * ValidatorHelper::validateEnumInteger($_GET, "type", array(1, 2));     未设置默认值，验证不通过，抛出异常
     */
    public static function validateEnumInteger($mixed, $porp, $validValues, $default = null): int
    {
        $value = self::validateInteger($mixed, $porp, null, null, $default);
        if ($default === null && !in_array($value, $validValues)) {
            throw new \Exception("$porp is not valid enum!", 406);
        }

        return $value;
    }

    /**
     * 一个字符串按照指定的方式切割为数组.
     *
     * @param array|string $mixed   源数据
     * @param string       $porp    取值key
     * @param string       $split   分割符，默认“,”
     * @param int          $min     分割后数组最小长度
     * @param int          $max     分割后数组最大长度
     * @param mixed        $default 默认值
     *
     * @throws \Exception
     *
     * @return array
     *
     * 1. 分割一个字符串，按照规则，且分割后数组长度判断
     * ValidatorHelper::validateArray("name,name2,name3");
     *
     * 2. 按照规则分割数组中一个key的值(POST|GET)，且分割后数组长度判断
     * ValidatorHelper::validateArray($_GET, "ids", 3, 4, "id"); 设置默认值
     * ValidatorHelper::validateArray($_GET, "ids", 3, 4);     未设置默认值，验证不通过，抛出异常
     */
    public static function validateArray($mixed, $porp = null, $split = ',', $min = null, $max = null, $default = null)
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        if (null == $value) {
            if ($default === null) {
                return [];
            } else {
                return $default;
            }
        }
        if (!is_array($value)) {
            $value = explode($split, $value);
        }

        $length = count($value);

        if ($min !== null && $length < $min) {
            throw new \Exception("$porp is too short (minimum is $min elements).", 406);
        }

        if ($max !== null && $length > $max) {
            throw new \Exception("$porp is too long (maximum is $max elements).", 406);
        }

        return $value;
    }

    /**
     * 手机号验证
     *
     * @param array  $mixed
     * @param string $porp
     * @param mixed  $default
     *
     * @return int|string
     *
     * @throws \Exception
     */
    public static function validateMobile(array $mixed, string $porp, $default = null)
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        if (!preg_match(self::$mobilePattern, "$value")) {
            if ($default === null) {
                throw new \Exception("$porp must be an mobile number.", 406);
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * 身份证号验证
     *
     * @param array  $mixed
     * @param string $porp
     * @param null   $default
     *
     * @return null|string
     * @throws \Exception
     */
    public static function validateIdcard(array $mixed, string $porp, $default = null)
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        if (!preg_match(self::$idcardPattern, "$value")) {
            if ($default === null) {
                throw new \Exception("$porp must be an idcard number.", 406);
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * 日期格式验证
     *
     * @param array  $mixed
     * @param string $porp
     * @param string $format 允许的日期格式（如：Ymd）
     * @param string $default
     *
     * @throws \Exception
     * @return int|string
     */
    public static function validateDate(array $mixed, string $porp, string $format, string $default = null)
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        if ($value != date($format, strtotime($value))) {
            if ($default === null) {
                throw new \Exception("$porp must be an Date {$value}≠{$format}.", 406);
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * 邮箱验证
     *
     * @param array|string $mixed   源数据
     * @param string       $porp    取值key
     * @param mixed        $default 默认值
     *
     * @return string
     *
     *
     * @throws \Exception
     *
     * 1. 验证数组中一个key的值是否满足字符串长度限制(POST|GET)
     * ValidatorHelper::validateString($_GET, "email", 'test@test.com');    设置默认值
     * ValidatorHelper::validateString($_GET, "email");                     未设置默认值，验证不通过，抛出异常
     */
    public static function validateEmail(array $mixed, string $porp, string $default = null): string
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        if (!preg_match(self::$emailPattern, "$value")) {
            if ($default === null) {
                throw new \Exception("$porp must be an email.", 406);
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * IP验证
     *
     * @param array|string $mixed   源数据
     * @param string       $porp    取值key
     * @param mixed        $default 默认值
     *
     * @return string
     *
     * @throws \Exception
     *
     * 1. 验证数组中一个key的值是否满足字符串长度限制(POST|GET)
     * ValidatorHelper::validateIP($_GET, "ip", '0.0.0.0');    设置默认值
     * ValidatorHelper::validateString($_GET, "email");        未设置默认值，验证不通过，抛出异常
     */
    public static function validateIP(array $mixed, string $porp, string $default = null): string
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        $ipValidator = new IpValidator();
        if (!$ipValidator->validate($value)) {
            throw new \Exception("$porp must be an valid IP.", 406);
        } else {
            return $value;
        }

        return $default;
    }

    /**
     * 图片验证
     *
     * @param array|string $mixed   源数据
     * @param string       $porp    取值key
     * @param mixed        $default 默认值
     *
     * @return string
     *
     * @throws \Exception
     *
     * 1. 验证数组中一个key的值是否满足字符串长度限制(POST|GET)
     * ValidatorHelper::validateIP($_GET, "image", '');    设置默认值
     * ValidatorHelper::validateString($_GET, "image");        未设置默认值，验证不通过，抛出异常
     */
    public static function validateImage(array $mixed, string $porp, string $default = null): string
    {
        $value = self::checkEmpty($mixed, $porp, $default);

        if (!preg_match(self::$imagePattern, "$value")) {
            if ($default === null) {
                throw new \Exception("$porp must be an image.", 406);
            } else {
                return $default;
            }
        }

        return $value;
    }


    /**
     * 验证IP
     *
     * @param        $mixed
     * @param null   $porp
     * @param string $split
     * @param null   $min
     * @param null   $max
     * @param null   $default
     * @param bool   $force
     *
     * @return array
     *
     * ValidatorHelper::validateArrayIP('106.95.255.255,182.148.56.48');    设置默认值
     *
     * @throws \Exception
     */
    public static function validateArrayIP(
        $mixed,
        $porp = null,
        $split = ',',
        $min = null,
        $max = null,
        $default = null,
        bool $force = false
    ) {
        $data          = ValidatorHelper::validateArray($mixed, 'ip');
        $throwExceptin = false;
        foreach ($data as $value) {
            try {
                $retData[] = ValidatorHelper::validateIP(['ip' => $value], 'ip');
            } catch (\Exception $e) {
                $throwExceptin = true;
            }
            if ($force && $throwExceptin) {
                log_exception($e->getMessage());
            }
        }

        return $retData ?? [];
    }


    /**
     * 验证数组值
     *
     * @param        $mixed
     * @param string $porp
     * @param int    $min     分割后数组最小长度
     * @param int    $max     分割后数组最大长度
     * @param mixed  $default 默认值
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function validateArrayValue(array $mixed, string $porp, $min = null, $max = null, $default = null)
    {
        if (!is_array($mixed)) {
            throw new \Exception("$mixed is array!", 406);
        }

        if (!isset($mixed[$porp])) {
            if ($default === null) {
                throw new \Exception("$porp is required!", 406);
            } else {
                return $default;
            }
        }

        if (!is_array($mixed[$porp])) {
            if ($default === null) {
                throw new \Exception("$porp is array!", 406);
            } else {
                return $default;
            }
        }

        $value = count($mixed[$porp]);

        if ($min !== null && $value < $min) {
            if ($default === null) {
                throw new \Exception("$porp is too small (minimum is $min)", 406);
            } else {
                return $default;
            }
        }

        if ($max !== null && $value > $max) {
            if ($default === null) {
                throw new \Exception("$porp is too big (maximum is $max)", 406);
            } else {
                return $default;
            }
        }

        return $mixed[$porp];
    }

    /**
     * 参数是否为整数或浮点数验证，如果不设置默认值，参数验证不通过，抛出异常.
     *
     * @param array|mixed $mixed   源数据
     * @param string      $porp    取值key
     * @param int|float   $min     最小值(不小于)
     * @param int|float   $max     最大值(不大于)
     * @param int|float   $default 默认值
     *
     * @throws \Exception
     *
     * @return int|float
     *
     * 1. 验证一个变量是否为整形或浮点型
     * ValidatorHelper::validateIntegerOrFloat($value);
     *
     * 2. 验证数组中一个key的值是否为整形或浮点型(POST|GET)
     * ValidatorHelper::validateIntegerOrFloat($_GET, "age", 10, 100, 20); 设置默认值
     * ValidatorHelper::validateIntegerOrFloat($_GET, "age", 10, 100);     未设置默认值，验证不通过，抛出异常
     */
    public static function validateIntegerOrFloat($mixed, $porp = null, $min = null, $max = null, $default = null)
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        if (!preg_match(self::$numberPattern, "$value") && !preg_match(self::$floatPattern, "$value")) {
            if ($default === null) {
                throw new \Exception("$porp must be an integer or float.", 406);
            } else {
                return $default;
            }
        }

        if (strpos($value, '.') == false) {
            $value = intval($value);
        } else {
            $value = floatval($value);
        }

        if ($min !== null && $value < $min) {
            if ($default === null) {
                throw new \Exception("$porp is too small (minimum is $min)", 406);
            } else {
                return $default;
            }
        }

        if ($max !== null && $value > $max) {
            if ($default === null) {
                throw new \Exception("$porp is too big (maximum is $max)", 406);
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * 验证字符串是否是JSON，且返回decode数组
     *
     * @param array  $mixed   源数据
     * @param string $porp    取值key
     * @param array  $default 默认值
     *
     * @return array
     */
    public static function validateJson(array $mixed, string $porp, array $default = null): array
    {
        $value = self::checkEmpty($mixed, $porp, $default);
        if ($value == $default) {
            return $default;
        }

        $data          = json_decode($value, true);
        $jsonErrorCode = json_last_error();
        if ($jsonErrorCode == JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        if ($default === null) {
            throw new \Exception("$porp is not json format!", 406);
        } else {
            return $default;
        }
    }


    /**
     * 手机号验证
     *
     * @return bool
     */
    public static function checkMobile(string $mobile): bool
    {
        if (!preg_match(self::$mobilePattern, "$mobile")) {
            return false;
        }

        return true;
    }
}
