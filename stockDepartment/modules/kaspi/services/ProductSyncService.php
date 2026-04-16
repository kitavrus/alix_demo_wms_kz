<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\constants\KaspiConstants;
use stockDepartment\modules\kaspi\dto\AlixItemDto;
use stockDepartment\modules\kaspi\exceptions\KaspiApiException;
use stockDepartment\modules\kaspi\models\ProductBarcodesV2;
use stockDepartment\modules\kaspi\models\ProductV2;
use Yii;
use yii\base\Component;
use yii\log\Logger;

/**
 * Синхронизация номенклатуры Alix 1C → product_v2 / product_barcodes_v2.
 *
 * Идентификатор в источнике — `guid`. При каждом запуске cron:
 *   - для каждого item из Alix 1C ищем ProductV2 по `guid`;
 *   - если нет — создаём, если есть — обновляем поля;
 *   - основной barcode из JSON добавляем в `product_barcodes_v2`
 *     (UNIQUE по (product_id, barcode) защищает от дублей).
 */
class ProductSyncService extends Component
{
    /** @var Alix1CApiService */
    public $api;

    public function init()
    {
        parent::init();
        if ($this->api === null) {
            $module = Yii::$app->getModule('kaspi');
            if ($module !== null) {
                $this->api = $module->get('alix1cApiService');
            }
        }
        if (!$this->api instanceof Alix1CApiService) {
            $this->api = new Alix1CApiService();
        }
    }

    /**
     * Полный цикл: забрать из Alix 1C и записать в БД.
     *
     * @return array{fetched:int, created:int, updated:int, barcodes_added:int, errors:int, status:string, message?:string}
     */
    public function syncFromApi()
    {
        try {
            $items = $this->api->getItems();
        } catch (KaspiApiException $e) {
            $this->log(Logger::LEVEL_ERROR, 'Alix 1C fetch failed: ' . $e->getMessage());

            return [
                'fetched'        => 0,
                'created'        => 0,
                'updated'        => 0,
                'barcodes_added' => 0,
                'errors'         => 1,
                'status'         => 'ERROR',
                'message'        => $e->getMessage(),
            ];
        }

        $result = $this->syncFromItems($items);
        $result['status'] = $result['errors'] === 0 ? 'OK' : 'PARTIAL';

        return $result;
    }

    /**
     * Записать массив DTO в БД.
     *
     * @param AlixItemDto[] $items
     * @return array{fetched:int, created:int, updated:int, barcodes_added:int, errors:int}
     */
    public function syncFromItems(array $items)
    {
        $stats = [
            'fetched'        => count($items),
            'created'        => 0,
            'updated'        => 0,
            'barcodes_added' => 0,
            'errors'         => 0,
        ];

        foreach ($items as $dto) {
            if (!$dto instanceof AlixItemDto || !$dto->isValid()) {
                $stats['errors']++;
                continue;
            }

            $tx = Yii::$app->db->beginTransaction();
            try {
                $this->upsertProduct($dto, $stats);
                $this->upsertBarcode($dto, $stats);
                $tx->commit();
            } catch (\Exception $e) {
                $tx->rollBack();
                $stats['errors']++;
                $this->log(
                    Logger::LEVEL_ERROR,
                    'Alix 1C sync failed for guid=' . $dto->guid . ': ' . $e->getMessage()
                );
            }
        }

        return $stats;
    }

    /**
     * @param AlixItemDto $dto
     * @param array       $stats
     * @throws \Exception
     */
    private function upsertProduct(AlixItemDto $dto, array &$stats)
    {
        $product = ProductV2::findByGuid($dto->guid);
        $isNew = false;

        if ($product === null) {
            $product = new ProductV2();
            $product->guid = $dto->guid;
            $isNew = true;
        }

        $product->barcode           = $this->nullableString($dto->barcode);
        $product->article           = $this->nullableString($dto->article);
        $product->category          = $this->nullableString($dto->category);
        $product->name              = $this->nullableString($dto->name);
        $product->name_kaz          = $this->nullableString($dto->name_kaz);
        $product->brand             = $this->nullableString($dto->brand);
        $product->VAT_rate          = $dto->VAT_rate !== null && $dto->VAT_rate !== '' ? (string) $dto->VAT_rate : null;
        $product->country_of_origin = $this->nullableString($dto->country_of_origin);
        $product->description       = $dto->description !== null ? (string) $dto->description : null;
        $product->color_code        = $this->nullableString($dto->color_code);
        $product->color_name        = $this->nullableString($dto->color_name);
        $product->filling           = $this->nullableString($dto->filling);
        $product->code_tnved        = $this->nullableString($dto->code_tnved);
        $product->code_nkt          = $this->nullableString($dto->code_nkt);

        if (!$product->save()) {
            throw new \RuntimeException(
                'save ProductV2 failed: ' . json_encode($product->getErrors(), JSON_UNESCAPED_UNICODE)
            );
        }

        if ($isNew) {
            $stats['created']++;
        } else {
            $stats['updated']++;
        }
    }

    /**
     * @param AlixItemDto $dto
     * @param array       $stats
     * @throws \Exception
     */
    private function upsertBarcode(AlixItemDto $dto, array &$stats)
    {
        $barcode = $this->nullableString($dto->barcode);
        if ($barcode === null) {
            return;
        }

        $product = ProductV2::findByGuid($dto->guid);
        if ($product === null) {
            return;
        }

        if (ProductBarcodesV2::existsFor($product->id, $barcode)) {
            return;
        }

        $row = new ProductBarcodesV2();
        $row->product_id = (int) $product->id;
        $row->barcode    = $barcode;

        if (!$row->save()) {
            throw new \RuntimeException(
                'save ProductBarcodesV2 failed: ' . json_encode($row->getErrors(), JSON_UNESCAPED_UNICODE)
            );
        }

        $stats['barcodes_added']++;
    }

    /**
     * Пустая строка → null. Иначе — строка без пробелов по краям.
     */
    private function nullableString($value)
    {
        if ($value === null) {
            return null;
        }
        $v = trim((string) $value);

        return $v === '' ? null : $v;
    }

    private function log($level, $message)
    {
        if (!Yii::$app->has('log')) {
            return;
        }
        Yii::getLogger()->log($message, $level, KaspiConstants::LOG_CATEGORY_ALIX_1C);
    }
}
