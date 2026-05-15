<?php

namespace common\log;

use yii\helpers\VarDumper;
use yii\log\FileTarget;
use yii\log\Logger;

/**
 * FileTarget для логов внешних интеграций (alix.1c, kaspi).
 *
 * Отличия от стандартного yii\log\FileTarget:
 * - не печатает префикс [ip][userId][sessionId] — для cron/HTTP-обмена он шум;
 * - не печатает stack-trace, даже если в компоненте log включён traceLevel.
 *
 * Формат строки: `YYYY-MM-DD HH:MM:SS [level][category] message`.
 */
class IntegrationFileTarget extends FileTarget
{
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $levelName = Logger::getLevelName($level);
        if (!is_string($text)) {
            $text = $text instanceof \Throwable ? (string) $text : VarDumper::export($text);
        }

        // Две пустые строки между записями, чтобы блоки REQUEST/RESPONSE
        // визуально отделялись друг от друга.
        return date('Y-m-d H:i:s', $timestamp) . " [$levelName][$category] $text" . "\n\n";
    }
}
