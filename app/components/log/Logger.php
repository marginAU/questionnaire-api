<?php

namespace app\components\log;

/**
 * 重写Logger
 *
 * @uses     Logger
 * @version  2018年05月29日
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Logger extends \yii\log\Logger
{
    // 默认的分类
    const DEFAULT_CATEGORY = 'application';

    // notice 日志级别
    const LEVEL_NOTICE = 0x80;

    /**
     * 请求日志全局唯一ID
     *
     * @var string
     */
    public $traceid = '';

    /**
     * 当前系统日志ID
     *
     * @var string
     */
    public $spanid = '';

    /**
     * 父请求日志ID(spanid)
     *
     * @var string
     */
    public $parentid = '';

    /**
     * 请求uri
     *
     * @var string
     */
    public $uri = '';

    /**
     * 性能日志
     *
     * @var array
     */
    public $profiles = [];

    /**
     * 计算日志
     *
     * @var array
     */
    public $countings = [];

    /**
     * 标记日志
     *
     * @var array
     */
    public $pushlogs = [];

    /**
     * 标记栈
     *
     * @var array
     */
    public $profileStacks = [];

    /**
     * 重写新增请求参数
     */
    public function init()
    {
        $this->spanid   = get_spanid();
        $this->traceid  = get_traceid();
        $this->parentid = get_parentid();

        if (isset($_SERVER['REQUEST_URI'])) {
            $arrUrl    = parse_url('http://www.example.com' . strval($_SERVER['REQUEST_URI']));
            $this->uri = strval($arrUrl['path']);
        }

        if (\Yii::$app instanceof \yii\console\Application && isset($_SERVER['argv'])) {
            $this->uri = 'php' . ' ' . implode(' ', $_SERVER['argv']);
        }

        parent::init();
    }

    /**
     * 重写新增日志参数
     *
     * @param array|string $message
     * @param int          $level
     * @param string       $category
     */
    public function log($message, $level, $category = 'application'): void
    {
        if ($category == self::DEFAULT_CATEGORY && defined('SYSTEM_NAME')) {
            $category = SYSTEM_NAME;
        }
        $prefix  = sprintf('[traceid:%s] [spanid:%s] [parentid:%s] ', $this->traceid, $this->spanid, $this->parentid);
        $message = $prefix . $message;

        $time   = microtime(true);
        $traces = [];
        if ($this->traceLevel > 0) {
            $count = 0;
            $ts    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_pop($ts); // remove the last trace since it would be the entry script, not very useful
            foreach ($ts as $trace) {
                if (isset($trace['file'], $trace['line']) && strpos($trace['file'], YII2_PATH) !== 0) {
                    unset($trace['object'], $trace['args']);
                    $traces[] = $trace;
                    if (++$count >= $this->traceLevel) {
                        break;
                    }
                }
            }
        }
        $this->messages[] = [$message, $level, $category, $time, $traces];
        if ($this->flushInterval > 0 && count($this->messages) >= $this->flushInterval) {
            $this->flush();
        }
    }

    /***
     * 重写追加notice日志
     *
     * @param bool $final
     */
    public function flush($final = false): void
    {
        // 所有日志后面追加一条notice日志
        if ($final) {
            $this->apendNoticeLog();
        }

        parent::flush($final);
    }

    /**
     * 重写新增notice日志
     *
     * @param int $level
     *
     * @return string
     */
    public static function getLevelName($level): string
    {
        static $levels = [
            self::LEVEL_ERROR         => 'error',
            self::LEVEL_WARNING       => 'warning',
            self::LEVEL_INFO          => 'info',
            self::LEVEL_TRACE         => 'trace',
            self::LEVEL_PROFILE_BEGIN => 'profile begin',
            self::LEVEL_PROFILE_END   => 'profile end',
            self::LEVEL_NOTICE        => 'notice',
        ];

        return isset($levels[$level]) ? $levels[$level] : 'unknown';
    }

    /**
     * 追加一条notice日志
     */
    public function apendNoticeLog(): void
    {
        // php耗时单位ms毫秒
        $timeUsed = sprintf("%.0f", (microtime(true) - YII_BEGIN_TIME) * 1000);
        // php运行内存大小单位M
        $memUsed = sprintf("%.0f", memory_get_peak_usage() / (1024 * 1024));

        $profileInfo  = $this->getProfilesInfos();
        $countingInfo = $this->getCountingInfo();

        $messageAry = array(
            "[$timeUsed(ms)]",
            "[$memUsed(MB)]",
            "[{$this->uri}]",
            "[" . implode(" ", $this->pushlogs) . "]",
            "profile[" . $profileInfo . "]",
            "counting[" . $countingInfo . "]",
        );
        $category   = defined('MODULE_NAME') ? MODULE_NAME : 'application';
        $message    = implode(" ", $messageAry);

        $this->profiles      = [];
        $this->countings     = [];
        $this->pushlogs      = [];
        $this->profileStacks = [];
        $this->log($message, self::LEVEL_NOTICE, $category);
    }

    /**
     * pushlog日志
     *
     * @param string $key
     * @param mixed  $val
     */
    public function pushLog(string $key, $val): void
    {
        if (!(is_string($key) || is_numeric($key))) {
            return;
        }
        $key = urlencode($key);
        if (is_array($val)) {
            $this->pushlogs[] = "$key=" . json_encode($val);
        } elseif (is_bool($val)) {
            $this->pushlogs[] = "$key=" . var_export($val, true);
        } elseif (is_string($val) || is_numeric($val)) {
            $this->pushlogs[] = "$key=" . urlencode($val);
        } elseif (is_null($val)) {
            $this->pushlogs[] = "$key=";
        }
    }

    /**
     * 标记时间记录
     *
     * @param string $name
     * @param float  $value
     */
    public function profile(string $name, $value): void
    {
        $this->profiles[$name]['cost']  = $value;
        $this->profiles[$name]['total'] = 1;
    }

    /**
     * 标记开始
     *
     * @param string $name
     */
    public function profileStart(string $name): void
    {
        if (is_string($name) == false || empty($name)) {
            return;
        }
        $this->profileStacks[$name]['start'] = microtime(true);
    }

    /**
     * 标记开始
     *
     * @param string $name
     */
    public function profileEnd(string $name): void
    {
        if (is_string($name) == false || empty($name)) {
            return;
        }

        if (!isset($this->profiles[$name])) {
            $this->profiles[$name] = [
                'cost'  => 0,
                'total' => 0,
            ];
        }

        $this->profiles[$name]['cost']  += microtime(true) - $this->profileStacks[$name]['start'];
        $this->profiles[$name]['total'] = $this->profiles[$name]['total'] + 1;
    }

    /**
     * 组装profiles
     */
    public function getProfilesInfos(): string
    {
        $profileAry = [];
        foreach ($this->profiles as $key => $profile) {
            if (!isset($profile['cost']) || !isset($profile['total'])) {
                continue;
            }
            $cost         = sprintf("%.2f", $profile['cost'] * 1000);
            $profileAry[] = "$key=" . $cost . '(ms)/' . $profile['total'];
        }

        return implode(",", $profileAry);
    }

    /**
     * 缓存命中率计算
     *
     * @param string $name
     * @param int    $hit
     * @param int    $total
     */
    public function counting(string $name, int $hit, int $total = null): void
    {
        if (!is_string($name) || empty($name)) {
            return;
        }
        if (!isset($this->countings[$name])) {
            $this->countings[$name] = ['hit' => 0, 'total' => 0];
        }
        $this->countings[$name]['hit'] += intval($hit);
        if ($total !== null) {
            $this->countings[$name]['total'] += intval($total);
        }
    }

    /**
     * 组装字符串
     */
    public function getCountingInfo(): string
    {
        if (empty($this->countings)) {
            return "";
        }

        $countAry = [];
        foreach ($this->countings as $name => $counter) {
            if (isset($counter['hit'], $counter['total']) && $counter['total'] != 0) {
                $countAry[] = "$name=" . $counter['hit'] . "/" . $counter['total'];
            } elseif (isset($counter['hit'])) {
                $countAry[] = "$name=" . $counter['hit'];
            }
        }

        return implode(',', $countAry);
    }
}