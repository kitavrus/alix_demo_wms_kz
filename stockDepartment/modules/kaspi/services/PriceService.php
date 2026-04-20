<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\models\KaspiPriceHistory;
use stockDepartment\modules\kaspi\models\ProductV2;
use stockDepartment\modules\kaspi\dto\PriceUpdateRequestDto;
use Yii;
use yii\base\Component;

/**
 * Сервис управления ценами товаров на Kaspi.
 *
 * Kaspi не имеет REST API для обновления цен. Механизм:
 * 1. Принимаем массив новых цен через наш API → сохраняем все в kaspi_price_history.
 * 2. Если хотя бы одна запись с датой активации уже наступившей — генерируем Excel один раз.
 *    Файл содержит ВСЕ доступные товары с актуальными ценами.
 *    Его нужно вручную загрузить в кабинет Kaspi: Товары → Загрузить прайс-лист.
 * 3. Если все даты в будущем — записи остаются PENDING;
 *    cron активирует их, когда дата наступит (actionKaspiActivatePendingPrices).
 */
class PriceService extends Component
{
    /** @var PriceListService */
    public $priceListService;

    public function init()
    {
        parent::init();
        if ($this->priceListService === null) {
            $module = Yii::$app->getModule('kaspi');
            if ($module !== null) {
                $this->priceListService = $module->get('priceListService');
            }
        }
        if (!$this->priceListService instanceof PriceListService) {
            $this->priceListService = new PriceListService();
        }
    }

    /**
     * Сохранить массив новых цен и, если есть уже наступившие даты, сгенерировать Excel.
     *
     * Excel генерируется ровно один раз для всего батча — не на каждую запись отдельно.
     *
     * @param PriceUpdateRequestDto[] $dtos
     * @return array Результат операции
     */
    public function applyBatchPriceUpdate(array $dtos)
    {
        $savedIds    = [];
        $scheduled   = [];
        $immediate   = [];
        $now         = time();
        $userId      = Yii::$app->has('user') && !Yii::$app->user->isGuest
            ? (int) Yii::$app->user->id
            : null;

        // Собираем все идентификаторы из батча — могут быть как GUID, так и артикулы
        $allIds = array_map(function ($dto) { return $dto->product_guid; }, $dtos);

        // Ищем по guid
        $byGuid = ProductV2::find()
            ->select(['guid', 'article'])
            ->andWhere(['in', 'guid', $allIds])
            ->asArray()
            ->all();
        $guidToArticle = [];
        foreach ($byGuid as $row) {
            $guidToArticle[$row['guid']] = $row['article'];
        }

        // Для тех, что не нашлись по guid — ищем по article
        $notFoundByGuid = array_diff($allIds, array_keys($guidToArticle));
        $articleToGuid = [];
        if (!empty($notFoundByGuid)) {
            $byArticle = ProductV2::find()
                ->select(['guid', 'article'])
                ->andWhere(['in', 'article', $notFoundByGuid])
                ->asArray()
                ->all();
            foreach ($byArticle as $row) {
                $articleToGuid[$row['article']] = $row['guid'];
            }
        }

        foreach ($dtos as $dto) {
            $effectiveFrom = $dto->getEffectiveFromTimestamp();

            // Резолвим: если пришёл GUID — берём артикул; если пришёл артикул — берём GUID
            if (isset($guidToArticle[$dto->product_guid])) {
                $guid    = $dto->product_guid;
                $article = $guidToArticle[$dto->product_guid];
            } elseif (isset($articleToGuid[$dto->product_guid])) {
                $guid    = $articleToGuid[$dto->product_guid];
                $article = $dto->product_guid;
            } else {
                $guid    = $dto->product_guid;
                $article = null;
            }

            $record = new KaspiPriceHistory();
            $record->product_guid    = $guid;
            $record->article         = $article;
            $record->price           = (float) $dto->price;
            $record->price_type      = $dto->price_type;
            $record->note            = $dto->note;
            $record->effective_from  = $effectiveFrom;
            $record->push_status     = KaspiPriceHistory::PUSH_STATUS_PENDING;
            $record->created_at      = $now;
            $record->created_user_id = $userId;

            if (!$record->save(false)) {
                Yii::error('Failed to save KaspiPriceHistory for ' . $dto->product_guid, 'kaspi.price');
                $errors[] = ['product_guid' => $dto->product_guid, 'error' => 'DB save failed'];
                continue;
            }

            $savedIds[] = $record->id;

            if ($dto->isImmediatelyEffective()) {
                $immediate[] = $record;
            } else {
                $scheduled[] = [
                    'price_history_id' => $record->id,
                    'product_guid'     => $dto->product_guid,
                    'effective_from'   => $dto->effective_from,
                ];
            }
        }

        if (!empty($errors)) {
            return [
                'status' => 'save_error',
                'errors' => $errors,
            ];
        }

        // Нет ни одной немедленной — всё в будущем
        if (empty($immediate)) {
            $response = [
                'status' => 'scheduled',
                'prices' => count($savedIds),
            ];
            if (!empty($scheduled)) {
                $response['scheduled'] = array_map(function ($s) {
                    return [
                        'product_guid'   => $s['product_guid'],
                        'effective_from' => $s['effective_from'],
                    ];
                }, $scheduled);
            }
            return $response;
        }

        // Помечаем немедленные записи как SENT, затем генерируем Excel один раз
        foreach ($immediate as $record) {
            $record->push_status = KaspiPriceHistory::PUSH_STATUS_SENT;
            $record->push_at     = $now;
            $record->save(false);
        }

        try {
            $priceList = $this->priceListService->buildCurrentPriceList();
            $this->priceListService->generateFromRows($priceList);

            // Маппинг sku → qty из получившегося прайса,
            // чтобы отличить applied-со-стоком (qty>0) от preload (qty=0).
            $excelQty = [];
            foreach ($priceList as $row) {
                $excelQty[$row['sku']] = (int) $row['qty'];
            }

            $applied    = [];
            $preloaded  = [];
            $notApplied = [];
            foreach ($immediate as $record) {
                $sku = !empty($record->article) ? $record->article : $record->product_guid;
                if (!array_key_exists($sku, $excelQty)) {
                    // Не попал в прайс — скорее всего нет карточки в product_v2
                    $notApplied[] = $sku;
                    continue;
                }
                $applied[] = $sku;
                if ($excelQty[$sku] === 0) {
                    $preloaded[] = $sku;
                }
            }

            $response = [
                'status'        => 'generated',
                'prices_saved'  => count($savedIds),
                'in_price_list' => count($applied),
                'applied'       => $applied,
                'download_url_xlsx' => '/kaspi/api/v1/price-list-download',
                'download_url_xml'  => '/kaspi/api/v1/price-list-download-xml',
                'public_xml_url'    => '/kaspi-price-list.xml',
            ];
            if (!empty($preloaded)) {
                $response['preloaded'] = $preloaded;
            }
            if (!empty($notApplied)) {
                $response['not_applied'] = $notApplied;
            }
            if (!empty($scheduled)) {
                $response['scheduled'] = array_map(function ($s) {
                    return [
                        'product_guid'   => $s['product_guid'],
                        'effective_from' => $s['effective_from'],
                    ];
                }, $scheduled);
            }
            return $response;
        } catch (\Exception $e) {
            Yii::error('Kaspi price list generation failed: ' . $e->getMessage(), 'kaspi.price');

            // Откатываем статус немедленных записей обратно в ERROR
            foreach ($immediate as $record) {
                $record->push_status   = KaspiPriceHistory::PUSH_STATUS_ERROR;
                $record->push_response = $e->getMessage();
                $record->save(false);
            }

            return [
                'status'  => 'error',
                'saved'   => count($savedIds),
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Активировать все PENDING-записи, у которых effective_from уже наступил,
     * и перегенерировать Excel-прайс одним файлом.
     *
     * Вызывается из CronController::actionKaspiActivatePendingPrices.
     */
    public function activatePendingPrices()
    {
        $records = KaspiPriceHistory::find()
            ->andWhere(['push_status' => KaspiPriceHistory::PUSH_STATUS_PENDING])
            ->andWhere(['<=', 'effective_from', time()])
            ->all();

        if (empty($records)) {
            return ['activated' => 0, 'excel_file' => null, 'status' => 'nothing_to_activate'];
        }

        $now        = time();
        $saveErrors = 0;
        foreach ($records as $record) {
            $record->push_status = KaspiPriceHistory::PUSH_STATUS_SENT;
            $record->push_at     = $now;
            if (!$record->save(false)) {
                Yii::error('Failed to activate KaspiPriceHistory #' . $record->id, 'kaspi.price');
                $saveErrors++;
            }
        }

        $total = count($records);

        try {
            $this->priceListService->generate();
        } catch (\Exception $e) {
            Yii::error('Kaspi price list generation failed: ' . $e->getMessage(), 'kaspi.price');
            return [
                'status' => 'error',
                'sent'   => 0,
                'errors' => $total,
                'total'  => $total,
            ];
        }

        return [
            'status' => 'activated',
            'sent'   => $total - $saveErrors,
            'errors' => $saveErrors,
            'total'  => $total,
        ];
    }
}
