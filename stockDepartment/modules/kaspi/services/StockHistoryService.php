<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\dto\StockUpdateRequestDto;
use stockDepartment\modules\kaspi\models\KaspiStockHistory;
use Yii;
use yii\base\Component;

/**
 * Сервис управления остатками товаров на Kaspi.
 *
 * Kaspi не имеет REST API для остатков — значения подаются через xlsx.
 * Механизм:
 * 1. Принимаем массив новых остатков через наш API → сохраняем в kaspi_stock_history.
 * 2. Если хотя бы одна запись уже активна — перегенерируем Excel один раз.
 * 3. Если все даты в будущем — записи остаются PENDING; cron активирует их позже.
 */
class StockHistoryService extends Component
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
     * Сохранить массив новых остатков и, если есть уже наступившие даты, перегенерировать Excel.
     *
     * @param StockUpdateRequestDto[] $dtos
     * @return array
     */
    public function applyBatchStockUpdate(array $dtos)
    {
        $savedIds    = [];
        $scheduled   = [];
        $immediate   = [];
        $errors      = [];
        $now         = time();
        $userId      = Yii::$app->has('user') && !Yii::$app->user->isGuest
            ? (int) Yii::$app->user->id
            : null;

        foreach ($dtos as $dto) {
            $record = new KaspiStockHistory();
            $record->product_guid    = $dto->product_guid;
            $record->qty             = (int) $dto->qty;
            $record->note            = $dto->note;
            $record->effective_from  = $dto->getEffectiveFromTimestamp();
            $record->push_status     = KaspiStockHistory::PUSH_STATUS_PENDING;
            $record->created_at      = $now;
            $record->created_user_id = $userId;

            if (!$record->save(false)) {
                Yii::error('Failed to save KaspiStockHistory for ' . $dto->product_guid, 'kaspi.stock');
                $errors[] = ['product_guid' => $dto->product_guid, 'error' => 'DB save failed'];
                continue;
            }

            $savedIds[] = $record->id;

            if ($dto->isImmediatelyEffective()) {
                $immediate[] = $record;
            } else {
                $scheduled[] = [
                    'stock_history_id' => $record->id,
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

        if (empty($immediate)) {
            $response = [
                'status' => 'scheduled',
                'stocks' => count($savedIds),
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

        foreach ($immediate as $record) {
            $record->push_status = KaspiStockHistory::PUSH_STATUS_SENT;
            $record->push_at     = $now;
            $record->save(false);
        }

        try {
            $this->priceListService->generate();

            $response = [
                'status' => 'generated',
                'stocks' => count($savedIds),
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
        } catch (\Exception $e) {
            Yii::error('Kaspi price list generation failed: ' . $e->getMessage(), 'kaspi.stock');

            foreach ($immediate as $record) {
                $record->push_status   = KaspiStockHistory::PUSH_STATUS_ERROR;
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
     * Активировать PENDING-записи с наступившей датой, перегенерировать xlsx.
     * Вызывается из будущего CronController::actionKaspiActivatePendingStocks.
     */
    public function activatePendingStocks()
    {
        $records = KaspiStockHistory::find()
            ->andWhere(['push_status' => KaspiStockHistory::PUSH_STATUS_PENDING])
            ->andWhere(['<=', 'effective_from', time()])
            ->all();

        if (empty($records)) {
            return ['activated' => 0, 'status' => 'nothing_to_activate'];
        }

        $now        = time();
        $saveErrors = 0;
        foreach ($records as $record) {
            $record->push_status = KaspiStockHistory::PUSH_STATUS_SENT;
            $record->push_at     = $now;
            if (!$record->save(false)) {
                Yii::error('Failed to activate KaspiStockHistory #' . $record->id, 'kaspi.stock');
                $saveErrors++;
            }
        }

        $total = count($records);

        try {
            $this->priceListService->generate();
        } catch (\Exception $e) {
            Yii::error('Kaspi price list generation failed: ' . $e->getMessage(), 'kaspi.stock');
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
