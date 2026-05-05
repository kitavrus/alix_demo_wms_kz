<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\constants\KaspiConstants;
use stockDepartment\modules\kaspi\dto\AlixItemDto;
use stockDepartment\modules\kaspi\exceptions\KaspiApiException;
use Yii;
use yii\base\Component;
use yii\httpclient\Client as HttpClient;
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

    /** @var bool Включить trace-лог HTTP-запроса/ответа. */
    public $httpLogEnabled = false;

    /** @var HttpClient|null */
    private $_httpClient;

    public function init()
    {
        parent::init();
        $this->_httpClient = new HttpClient([
            'baseUrl' => rtrim($this->baseUrl, '/') . '/',
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

        $this->logTrace('Alix 1C items request:', $request->toString());

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw new KaspiApiException('Alix 1C API transport error: ' . $e->getMessage(), 0, $e);
        }

        $this->logTrace('Alix 1C items response:', $response->toString());

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

    private function logTrace($label, $content)
    {
        if (!$this->httpLogEnabled || !Yii::$app->has('log')) {
            return;
        }
        Yii::getLogger()->log(
            $label . "\n" . $content,
            Logger::LEVEL_TRACE,
            KaspiConstants::LOG_CATEGORY_ALIX_1C
        );
    }
}
