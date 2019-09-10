<?php

namespace app\components\log;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * 重写日志输出器
 *
 * @uses     FileTarget
 * @version  2018年05月28日
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class FileTarget extends Target
{
    /**
     * @var string log file path or [path alias](guide:concept-aliases). If not set, it will use the "@runtime/logs/app.log" file.
     * The directory containing the log files will be automatically created if not existing.
     */
    public $logFile;
    /**
     * @var bool whether log files should be rotated when they reach a certain [[maxFileSize|maximum size]].
     * Log rotation is enabled by default. This property allows you to disable it, when you have configured
     * an external tools for log rotation on your server.
     * @since 2.0.3
     */
    public $enableRotation = true;
    /**
     * @var int maximum log file size, in kilo-bytes. Defaults to 10240, meaning 10MB.
     */
    public $maxFileSize = 10240; // in KB
    /**
     * @var int number of log files used for rotation. Defaults to 5.
     */
    public $maxLogFiles = 5;
    /**
     * @var int the permission to be set for newly created log files.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * If not set, the permission will be determined by the current environment.
     */
    public $fileMode;
    /**
     * @var int the permission to be set for newly created directories.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * Defaults to 0775, meaning the directory is read-writable by owner and group,
     * but read-only for other users.
     */
    public $dirMode = 0775;
    /**
     * @var bool Whether to rotate log files by copy and truncate in contrast to rotation by
     * renaming files. Defaults to `true` to be more compatible with log tailers and is windows
     * systems which do not play well with rename on open files. Rotation by renaming however is
     * a bit faster.
     *
     * The problem with windows systems where the [rename()](http://www.php.net/manual/en/function.rename.php)
     * function does not work with files that are opened by some process is described in a
     * [comment by Martin Pelletier](http://www.php.net/manual/en/function.rename.php#102274) in
     * the PHP documentation. By setting rotateByCopy to `true` you can work
     * around this problem.
     */
    public $rotateByCopy = true;

    /**
     * @var bool
     */
    public $json = false;


    /**
     * Initializes the route.
     * This method is invoked after the route is created by the route manager.
     */
    public function init()
    {
        parent::init();
        if ($this->logFile === null) {
            $this->logFile = Yii::$app->getRuntimePath() . '/logs/app.log';
        } else {
            $this->logFile = Yii::getAlias($this->logFile);
        }
        if ($this->maxLogFiles < 1) {
            $this->maxLogFiles = 1;
        }
        if ($this->maxFileSize < 1) {
            $this->maxFileSize = 1;
        }
    }

    /**
     * Writes log messages to a file.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     *
     * @throws InvalidConfigException if unable to open the log file for writing
     * @throws LogRuntimeException if unable to write complete log to file
     */
    public function export()
    {
        $logPath = dirname($this->logFile);
        FileHelper::createDirectory($logPath, $this->dirMode, true);

        $text = $this->getTextMessage();
        if (($fp = @fopen($this->logFile, 'a')) === false) {
            throw new InvalidConfigException("Unable to append to log file: {$this->logFile}");
        }
        @flock($fp, LOCK_EX);
        if ($this->enableRotation) {
            // clear stat cache to ensure getting the real current file size and not a cached one
            // this may result in rotating twice when cached file size is used on subsequent calls
            clearstatcache();
        }
        if ($this->enableRotation && @filesize($this->logFile) > $this->maxFileSize * 1024) {
            $this->rotateFiles();
            @flock($fp, LOCK_UN);
            @fclose($fp);
            $writeResult = @file_put_contents($this->logFile, $text, FILE_APPEND | LOCK_EX);
            if ($writeResult === false) {
                $error = error_get_last();
                throw new LogRuntimeException("Unable to export log through file!: {$error['message']}");
            }
            $textSize = strlen($text);
            if ($writeResult < $textSize) {
                throw new LogRuntimeException("Unable to export whole log through file! Wrote $writeResult out of $textSize bytes.");
            }
        } else {
            $writeResult = @fwrite($fp, $text);
            if ($writeResult === false) {
                $error = error_get_last();
                throw new LogRuntimeException("Unable to export log through file!: {$error['message']}");
            }
            $textSize = strlen($text);
            if ($writeResult < $textSize) {
                throw new LogRuntimeException("Unable to export whole log through file! Wrote $writeResult out of $textSize bytes.");
            }
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        if ($this->fileMode !== null) {
            @chmod($this->logFile, $this->fileMode);
        }
    }

    /**
     * Rotates log files.
     */
    protected function rotateFiles()
    {
        $file = $this->logFile;
        for ($i = $this->maxLogFiles; $i >= 0; --$i) {
            // $i == 0 is the original log file
            $rotateFile = $file . ($i === 0 ? '' : '.' . $i);
            if (is_file($rotateFile)) {
                // suppress errors because it's possible multiple processes enter into this section
                if ($i === $this->maxLogFiles) {
                    @unlink($rotateFile);
                    continue;
                }
                $newFile = $this->logFile . '.' . ($i + 1);
                $this->rotateByCopy ? $this->rotateByCopy($rotateFile, $newFile) : $this->rotateByRename($rotateFile,
                    $newFile);
                if ($i === 0) {
                    $this->clearLogFile($rotateFile);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getTextMessage(): string
    {
        if ($this->json) {
            $text = implode("\n", array_map([$this, 'formatJsonMessage'], $this->messages)) . "\n";
        } else {
            $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";
        }
        return $text;
    }

    /**
     * @return string
     */
    private function formatJsonMessage(array $message): string
    {
        list($text, $level, $category, $timestamp) = $message;
        if ($level == Logger::LEVEL_NOTICE) {
            return $this->formatJsonNoticeMessage($text, $level, $category, $timestamp);
        }

        return $this->formatJsonNormalMessage($text, $level, $category, $timestamp);;
    }

    /**
     * @param $text
     * @param $category
     * @param $timestamp
     *
     * @return string
     */
    private function formatJsonNormalMessage($text, $level, $category, $timestamp): string
    {
        $level = Logger::getLevelName($level);

        $data['datetime']    = date('Y/m/d H:i:s', $timestamp);
        $data['level']       = $level;
        $data['application'] = $category;

        // 解析日志
        $intMatch = preg_match('#\[traceid:([^\]]+)\] \[spanid:([^\]]+)\] \[parentid:([^\]]+)\] trace\[([^\]]*)\]([\s\S]*)#',
            $text, $arrMatch);
        if (!$intMatch) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        list(, $data['traceid'], $data['spanid'], $data['parentid'], $data['trace'], $data['message']) = $arrMatch;

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $text
     * @param $category
     * @param $timestamp
     *
     * @return string
     */
    private function formatJsonNoticeMessage($text, $level, $category, $timestamp): string
    {
        $level = Logger::getLevelName($level);

        $data['datetime']    = date('Y/m/d H:i:s', $timestamp);
        $data['level']       = $level;
        $data['application'] = $category;

        // 解析日志
        $intMatch = preg_match('#\[traceid:([^\]]+)\] \[spanid:([^\]]+)\] \[parentid:([^\]]+)\] \[(\d+)\(ms\)\] \[(\d+)\(MB\)\] \[([^\]]+)\] \[(.*)(?=\]\sprofile\[)\] profile\[([^\]]*)\] counting\[([^\]]*)\](.*)#',
            $text, $arrMatch);
        if (!$intMatch) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        list(, $data['traceid'], $data['spanid'], $data['parentid'], $data['cost(ms)'], $data['memory(MB)'], $data['uri'], $data['params'], $data['profile'], $data['counting']) = $arrMatch;
        $paramsAry = explode(' ', $data['params']);

        foreach ($paramsAry as $paramItem) {
            $itemAry = explode('=', $paramItem);
            if (count($itemAry) != 2) {
                continue;
            }
            list($key, $value) = $itemAry;
            if ($key == 'status') {
                $data['status'] = (int)$value;
            }
        }

        $data['memory(MB)'] = (int)$data['memory(MB)'];
        $data['cost(ms)']   = (int)$data['cost(ms)'];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /***
     * Clear log file without closing any other process open handles
     *
     * @param string $rotateFile
     */
    private function clearLogFile($rotateFile)
    {
        if ($filePointer = @fopen($rotateFile, 'a')) {
            @ftruncate($filePointer, 0);
            @fclose($filePointer);
        }
    }

    /***
     * Copy rotated file into new file
     *
     * @param string $rotateFile
     * @param string $newFile
     */
    private function rotateByCopy($rotateFile, $newFile)
    {
        @copy($rotateFile, $newFile);
        if ($this->fileMode !== null) {
            @chmod($newFile, $this->fileMode);
        }
    }

    /**
     * Renames rotated file into new file
     *
     * @param string $rotateFile
     * @param string $newFile
     */
    private function rotateByRename($rotateFile, $newFile)
    {
        @rename($rotateFile, $newFile);
    }
}