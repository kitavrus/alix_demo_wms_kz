<?php

namespace common\log;

use Yii;
use yii\base\ActionFilter;
use yii\log\Logger;
use yii\web\Response;

/**
 * Поведение-фильтр для REST-контроллеров: пишет каждый входящий запрос
 * в категорию alix.1c (маршрутизируется в @runtime/logs/alix/1c.log).
 *
 * Подключение:
 *   $behaviors['apiLogger'] = ['class' => \common\log\IncomingApiLogger::class];
 *
 * В лог попадает: метод, URL, IP клиента, заголовки запроса
 * (Authorization маскируется), тело запроса, статус ответа, заголовки и
 * тело ответа, длительность. Тело больше maxBodyDumpBytes подменяется
 * на "[body suppressed: N bytes]"; HTML-ответы — на "[HTML body suppressed]".
 */
class IncomingApiLogger extends ActionFilter
{
    public $category = 'alix.1c';
    public $maxBodyDumpBytes = 8192;

    private $startedAt;

    public function beforeAction($action)
    {
        $this->startedAt = microtime(true);

        $response = Yii::$app->getResponse();
        $response->on(Response::EVENT_AFTER_PREPARE, [$this, 'logExchange'], ['action' => $action]);

        return parent::beforeAction($action);
    }

    public function logExchange(\yii\base\Event $event)
    {
        try {
            $action = $event->data['action'];
            $request = Yii::$app->getRequest();
            $response = $event->sender;

            $durationMs = (int) round((microtime(true) - $this->startedAt) * 1000);
            $statusCode = (int) $response->getStatusCode();
            $level = $statusCode >= 400 ? Logger::LEVEL_ERROR : Logger::LEVEL_INFO;

            $label = sprintf(
                'Alix incoming %s %s → %d',
                $request->getMethod(),
                $action->getUniqueId(),
                $statusCode
            );

            $parts = [
                $label . ' (' . $durationMs . ' ms)',
                'From: ' . $request->getUserIP(),
                '=== REQUEST ===',
                $this->dumpRequest($request),
                '=== RESPONSE ===',
                $this->dumpResponse($response),
            ];

            Yii::getLogger()->log(implode("\n", $parts), $level, $this->category);
        } catch (\Throwable $t) {
            Yii::error('IncomingApiLogger failed: ' . $t->getMessage(), $this->category);
        }
    }

    private function dumpRequest($request)
    {
        $lines = [$request->getMethod() . ' ' . $request->getAbsoluteUrl()];
        foreach ($request->getHeaders() as $name => $values) {
            $value = strcasecmp($name, 'Authorization') === 0 ? '***' : implode(', ', $values);
            $lines[] = $name . ': ' . $value;
        }
        $lines[] = '';

        $body = (string) $request->getRawBody();
        $lines[] = $this->renderBody($body, false);

        return implode("\n", $lines);
    }

    private function dumpResponse($response)
    {
        $lines = ['HTTP ' . $response->getStatusCode() . ' ' . $response->statusText];
        foreach ($response->getHeaders() as $name => $values) {
            $lines[] = $name . ': ' . implode(', ', $values);
        }
        $lines[] = '';

        $body = (string) $response->content;
        $contentType = (string) $response->getHeaders()->get('Content-Type', '');
        $isHtml = stripos($contentType, 'text/html') !== false
            || stripos(ltrim(substr($body, 0, 200)), '<!DOCTYPE') === 0
            || stripos(ltrim(substr($body, 0, 200)), '<html') === 0;

        $lines[] = $this->renderBody($body, $isHtml);

        return implode("\n", $lines);
    }

    private function renderBody($body, $isHtml)
    {
        $bytes = strlen($body);
        if ($bytes === 0) {
            return '[empty body]';
        }
        if ($isHtml) {
            return '[HTML body suppressed: ' . $bytes . ' bytes]';
        }
        if ($bytes > $this->maxBodyDumpBytes) {
            return '[body suppressed: ' . $bytes . ' bytes]';
        }
        return $body;
    }
}
