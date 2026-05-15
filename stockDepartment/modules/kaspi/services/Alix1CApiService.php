<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\constants\KaspiConstants;
use stockDepartment\modules\kaspi\dto\AlixItemDto;
use stockDepartment\modules\kaspi\exceptions\KaspiApiException;
use Yii;
use yii\base\Component;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Response;
use yii\log\Logger;

/**
 * HTTP-клиент сервиса Alix 1C (мастер-данные номенклатуры).
 *
 * Endpoint: GET {baseUrl}/items
 * Auth: Basic (username / password)
 *
 * Пример:
 *   $items = $alix1c->getItems(); // AlixItemDto[]
 */
class Alix1CApiService extends Component
{
    /** @var string Базовый URL сервиса Alix 1C (с завершающим слэшем). */
    public $baseUrl = KaspiConstants::ALIX_1C_BASE_URL;

    /** @var string Basic Auth — логин. */
    public $username = KaspiConstants::ALIX_1C_DEFAULT_USERNAME;

    /** @var string Basic Auth — пароль. */
    public $password = KaspiConstants::ALIX_1C_DEFAULT_PASSWORD;

    /** @var int Таймаут запроса, сек. */
    public $timeoutSeconds = KaspiConstants::ALIX_1C_DEFAULT_TIMEOUT;

    /**
     * Порог: тело ответа крупнее этого размера дампится только как
     * "[body suppressed: N bytes]", чтобы не засорять лог гигантскими
     * JSON-выгрузками номенклатуры или HTML-страницами 404 от IIS.
     */
    const MAX_RESPONSE_BODY_DUMP_BYTES = 8192;

    /** @var HttpClient|null */
    private $_httpClient;

    public function init()
    {
        parent::init();
        $this->_httpClient = new HttpClient([
            'baseUrl' => rtrim($this->baseUrl, '/'),
        ]);
    }

    /**
     * Получить всю номенклатуру из Alix 1C.
     *
     * @return AlixItemDto[]
     * @throws KaspiApiException
     */
    public function getItems()
    {
        $request = $this->_httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl(KaspiConstants::ALIX_1C_ITEMS_PATH)
            ->setOptions(['timeout' => $this->timeoutSeconds]);

        $request->headers->set(
            'Authorization',
            'Basic ' . base64_encode($this->username . ':' . $this->password)
        );
        $request->headers->set('Accept', 'application/json');

        $startedAt = microtime(true);

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            $this->logHttpExchange(Logger::LEVEL_ERROR, 'getItems transport error', $request, null, $startedAt, $e);
            throw new KaspiApiException('Alix 1C API transport error: ' . $e->getMessage(), 0, $e);
        }

        $level = $response->isOk ? Logger::LEVEL_INFO : Logger::LEVEL_ERROR;
        $label = $response->isOk ? 'getItems OK' : 'getItems HTTP ' . $response->statusCode;
        $this->logHttpExchange($level, $label, $request, $response, $startedAt);

        if (!$response->isOk) {
            $body = substr((string) $response->content, 0, 2000);
            throw new KaspiApiException(
                'Alix 1C API HTTP ' . $response->statusCode,
                (int) $response->statusCode,
                null,
                (int) $response->statusCode,
                $body
            );
        }

        // Alix 1C отдаёт JSON, но без корректного Content-Type, поэтому
        // автоматический парсинг yii httpclient падает — разбираем тело вручную.
        $body = (string) $response->content;
        $data = json_decode($body, true);

        if (!is_array($data)) {
            $jsonErr = function_exists('json_last_error_msg') ? json_last_error_msg() : 'unknown';
            throw new KaspiApiException(
                'Alix 1C API: invalid JSON payload (' . $jsonErr . ')'
            );
        }

        // Некоторые 1С-обмены оборачивают массив в { "items": [...] }
        if (isset($data['items']) && is_array($data['items'])) {
            $data = $data['items'];
        }

        $dtos = [];
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }
            $dto = AlixItemDto::fromArray($row);
            if ($dto->isValid()) {
                $dtos[] = $dto;
            }
        }

        return $dtos;
    }

    /**
     * Отправить выполненную продажу в 1С.
     *
     * ЗАГЛУШКА: реальная спецификация endpoint 1С ещё не получена. Сейчас метод
     * только логирует payload и возвращает псевдо-ответ. Когда 1С отдаст
     * спецификацию (URL, auth, формат) — подменим тело на реальный HTTP-вызов
     * без изменения контракта возвращаемого значения.
     *
     * @param array $payload payload продажи: { order_number, kaspi_order_id, customer, items[], totals }
     * @return array ['status' => 'OK'|'ERROR', 'message' => ?, 'one_c_ref' => ?]
     */
    public function postSale(array $payload)
    {
        // TODO: - Вот тут добавить настоящий endpoint
        Yii::info(
            'Alix 1C postSale (stub): ' . json_encode($payload, JSON_UNESCAPED_UNICODE),
            KaspiConstants::LOG_CATEGORY_ALIX_1C
        );

        return [
            'status'    => 'OK',
            'message'   => 'stub: 1C endpoint not yet configured',
            'one_c_ref' => null,
        ];
    }

    /**
     * Полный дамп HTTP-обмена в категорию alix.1c
     * (маршрутизируется в @runtime/logs/alix/1c.log).
     *
     * В сообщение попадает: method+URL+query, заголовки и тело запроса
     * (Authorization маскируется), статус+заголовки+тело ответа, длительность.
     * Тело ответа подменяется на "[body suppressed: N bytes]", если оно
     * HTML или крупнее MAX_RESPONSE_BODY_DUMP_BYTES.
     */
    private function logHttpExchange($level, $label, $request, $response, $startedAt, \Exception $exception = null)
    {
        if (!Yii::$app->has('log')) {
            return;
        }

        $parts = [
            'Alix 1C ' . $label . ' (' . $this->elapsedMs($startedAt) . ' ms)',
        ];
        $caller = $this->resolveCaller();
        if ($caller !== null) {
            $parts[] = 'Called from: ' . $caller;
        }
        $parts[] = '=== REQUEST ===';
        $parts[] = $this->maskSecrets($this->safeDump($request));

        if ($exception !== null) {
            $parts[] = '=== TRANSPORT ERROR ===';
            $parts[] = get_class($exception) . ': ' . $exception->getMessage();
        }

        if ($response !== null) {
            $parts[] = '=== RESPONSE ===';
            $parts[] = $this->safeDump($response);
        }

        Yii::getLogger()->log(
            implode("\n", $parts),
            $level,
            KaspiConstants::LOG_CATEGORY_ALIX_1C
        );
    }

    private function safeDump($message)
    {
        // Для большого/HTML-ответа не вызываем toString() и не применяем regex
        // к телу — на старом PCRE pattern с backtracking даёт SIGSEGV
        // (наблюдается на PHP 5.6 + ответе > ~200KB).
        if ($message instanceof Response) {
            $bytes = strlen((string) $message->content);
            $suppress = $this->isHtmlResponse($message) || $bytes > self::MAX_RESPONSE_BODY_DUMP_BYTES;
            if ($suppress) {
                return $this->renderResponseHeadersOnly($message, $bytes);
            }
        }

        try {
            return (string) $message->toString();
        } catch (\Throwable $t) {
            return '[failed to dump: ' . $t->getMessage() . ']';
        }
    }

    private function renderResponseHeadersOnly(Response $response, $bytes)
    {
        $lines = ['HTTP/1.1 ' . $response->statusCode];
        try {
            foreach ($response->headers as $name => $values) {
                $values = is_array($values) ? $values : [$values];
                $lines[] = $name . ': ' . implode(', ', $values);
            }
        } catch (\Throwable $t) {
            $lines[] = '[failed to dump headers: ' . $t->getMessage() . ']';
        }
        $lines[] = '';
        $lines[] = '[body suppressed: ' . $bytes . ' bytes]';
        return implode("\n", $lines);
    }

    private function isHtmlResponse(Response $response)
    {
        $contentType = (string) $response->headers->get('Content-Type', '');
        if (stripos($contentType, 'text/html') !== false) {
            return true;
        }
        // Fallback: IIS иногда отдаёт HTML без корректного Content-Type.
        $head = ltrim(substr((string) $response->content, 0, 200));
        return stripos($head, '<!DOCTYPE') === 0 || stripos($head, '<html') === 0;
    }

    private function maskSecrets($raw)
    {
        return preg_replace('/^(Authorization:\s*).+$/mi', '$1***', $raw);
    }

    /**
     * Возвращает первый кадр стека за пределами Alix1CApiService — это и есть
     * место в прикладном коде, откуда был дёрнут integration-метод.
     */
    private function resolveCaller()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 12);
        foreach ($trace as $frame) {
            if (!isset($frame['file'], $frame['line'])) {
                continue;
            }
            if (strpos($frame['file'], __FILE__) === 0) {
                continue;
            }
            return $frame['file'] . ':' . $frame['line'];
        }
        return null;
    }

    private function elapsedMs($startedAt)
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }
}
