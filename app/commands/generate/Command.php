<?php

namespace app\commands\generate;

use yii\helpers\Console;

/**
 * 命令
 *
 * @uses     Command
 * @version  2018年07月24日
 * @author   lilin <lilin@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Command extends \yii\console\Controller
{
    /**
     * You can define action alias.
     * @return array
     */
    public function commandAliases(): array
    {
        return [
            // 'alias' => 'real action name'
        ];
    }

    public function runAction($id, $params = []): int
    {
        if ($aliases = $this->commandAliases()) {
            $id = $aliases[$id] ?? $id;
        }

        return (int)parent::runAction($id, $params);
    }

    protected function prettyJSON($data, string $title): void
    {
        $string = \json_encode($data, \JSON_UNESCAPED_SLASHES|\JSON_PRETTY_PRINT|\JSON_UNESCAPED_UNICODE);

        if ($title) {
            echo $title, \PHP_EOL;
        }

        echo $string, \PHP_EOL;
    }

    /**
     * @param string $format
     * @param mixed ...$args
     * @return int
     */
    protected function infoMessage(string $format, ...$args): int
    {
        $this->formatMessage([
            'type' => 'info',
            // 'msgColors' => [Console::FG_GREEN],
            'titleColors' => [Console::FG_GREEN],
        ], $format, ...$args);
        return 0;
    }

    /**
     * @param string $format
     * @param mixed ...$args
     * @return int
     */
    protected function warnMessage(string $format, ...$args): int
    {
        $this->formatMessage([
            'type' => 'warning',
            // 'msgColors' => [Console::FG_GREEN],
            'titleColors' => [Console::FG_YELLOW],
        ], $format, ...$args);
        return 0;
    }

    /**
     * @param string $format
     * @param mixed ...$args
     * @return int
     */
    protected function successMessage(string $format, ...$args): int
    {
        $this->formatMessage([
            'type' => 'success',
            'msgColors' => [Console::FG_GREEN],
            'titleColors' => [Console::FG_GREEN, Console::BOLD],
        ], $format, ...$args);
        return 0;
    }

    /**
     * @param string $format
     * @param mixed ...$args
     * @return int
     */
    protected function errorMessage(string $format, ...$args): int
    {
        $this->formatMessage([
            'type' => 'error',
            'msgColors' => [Console::FG_RED],
            'titleColors' => [Console::FG_RED, Console::BOLD],
        ], $format, ...$args);

        return 2;
    }

    /**
     * @param array $opts
     * - type eg error, info ...
     * - title colors [Console::FG_RED, Console::BOLD]
     * - message colors [Console::FG_RED]
     * - return(bool) return formatted
     * @param string $format
     * @param mixed ...$args
     * @return string|mixed
     */
    protected function formatMessage(array $opts, string $format, ...$args)
    {
        $opts = \array_merge([
            'type' => 'info',
            'return' => false,
            'msgColors' => [],
            'titleColors' => [Console::FG_CYAN],
        ], $opts);

        $type = \strtoupper($opts['type']);
        $msgColors = (array)$opts['msgColors'];
        $titleColors = (array)$opts['titleColors'];

        $message = $this->ansiFormat($type . ': ', ...$titleColors);
        $message .= $this->ansiFormat(\sprintf($format, ...$args), ...$msgColors);

        if ($opts['return']) {
            return $message;
        }

        echo $message . \PHP_EOL;
        return null;
    }
}