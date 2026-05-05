<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\returns;

use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;
use common\models\ActiveRecord;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\alix\controllers\ecommerce\returns\domain\ReturnRepository;
use stockDepartment\modules\alix\controllers\ecommerce\returns\domain\ReturnScanningForm;
use stockDepartment\modules\kaspi\services\OrderReturnService;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Сканирование возвратов для alix-ecommerce на базе EcommerceReturn.
 * URL: /alix/ecommerce/returns/scanning/*
 *
 * Все возвраты (Kaspi/Alix/DeFacto) идут через EcommerceReturn / EcommerceReturnItem.
 * Физические сканы пишутся в ecommerce_stock (joined by return_id / return_item_id).
 */
class ScanningController extends Controller
{
    public function actionIndex()
    {
        $form = new ReturnScanningForm();
        $clientId = Client::CLIENT_ALIXAVIEN;
        $form->client_id = $clientId;

        $partyNumberArray = ArrayHelper::map(
            EcommerceReturn::find()
                ->select('id, order_number')
                ->andWhere(['client_id' => $clientId])
                ->andWhere(['!=', 'status', ReturnOutboundStatus::DONE])
                ->andWhere(['deleted' => 0])
                ->orderBy(['created_at' => SORT_DESC])
                ->asArray()
                ->all(),
            'id',
            'order_number'
        );

        if (!empty($partyNumberArray)) {
            reset($partyNumberArray);
            $form->order_number = key($partyNumberArray);
        }

        $items = [];
        $expectedQty = 0;
        $acceptedQty = 0;
        if ($return = EcommerceReturn::findOne(['id' => $form->order_number, 'client_id' => $clientId])) {
            $items = EcommerceReturnItem::find()
                ->andWhere(['return_id' => $return->id])
                ->orderBy(['accepted_qty' => SORT_ASC])
                ->asArray()
                ->all();
            $expectedQty = (int) $return->expected_qty;
            $acceptedQty = (int) $return->accepted_qty;
        }

        return $this->render('index', [
            'inboundForm' => $form,
            'partyNumberArray' => $partyNumberArray,
            'items' => $this->renderPartial('_order_items', ['items' => $items]),
            'expected_qty' => $expectedQty,
            'accepted_qty' => $acceptedQty,
        ]);
    }

    public function actionScanningReturns()
    {
        return $this->render('scanning-returns', ['inboundForm' => new ReturnScanningForm()]);
    }

    public function actionCreateNewOrder()
    {
        $form = new ReturnScanningForm();
        $form->setScenario(ReturnScanningForm::SCENARIO_ORDER_NUMBER);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $dtoRepo = new \stdClass();
            $dtoRepo->clientId = Client::CLIENT_ALIXAVIEN;

            $repository = new ReturnRepository($dtoRepo);
            if ($repository->isOrderExist($form->order_number)) {
                Yii::$app->session->setFlash(
                    'error',
                    "Возврат с таким номером '{$form->order_number}' уже существует!"
                );
                return $this->render('scanning-returns', ['inboundForm' => $form]);
            }

            $dto = new \stdClass();
            $dto->orderNumber = trim($form->order_number);
            $dto->expectedTotalProductQty = 0;
            $dto->expectedTotalPlaceQty = 0;
            $dto->items = [];

            $repository->create($dto);
            return $this->redirect(['index']);
        }

        return $this->render('scanning-returns', ['inboundForm' => $form]);
    }

    public function actionGetScannedProductById()
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $items = [];
        $expected = 0;
        $accepted = 0;

        if ($return = EcommerceReturn::findOne($id)) {
            $items = EcommerceReturnItem::find()
                ->andWhere(['return_id' => $return->id])
                ->orderBy(['accepted_qty' => SORT_ASC])
                ->asArray()
                ->all();
            $expected = (int) $return->expected_qty;
            $accepted = (int) $return->accepted_qty;
        }

        return [
            'message' => 'Success',
            'countScannedProductInOrder' => $accepted,
            'expected_qty' => $expected,
            'items' => $this->renderPartial('_order_items', ['items' => $items]),
        ];
    }

    public function actionGetInProcessInboundOrdersByClientId()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $clientId = Yii::$app->request->post('client_id', Client::CLIENT_ALIXAVIEN);

        $data = ['' => ''] + ArrayHelper::map(
            EcommerceReturn::find()
                ->select('id, order_number')
                ->andWhere(['client_id' => $clientId])
                ->andWhere(['!=', 'status', ReturnOutboundStatus::DONE])
                ->andWhere(['deleted' => 0])
                ->asArray()
                ->all(),
            'id',
            'order_number'
        );

        return [
            'message' => 'Success',
            'type' => 'return',
            'dataOptions' => $data,
        ];
    }

    public function actionValidateScannedBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReturnScanningForm();
        $model->setScenario(ReturnScanningForm::SCENARIO_BOX_BARCODE);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $repo = new ReturnRepository();
            return [
                'success' => '1',
                'countProductInBox' => $repo->getScannedProductInBox($model->box_barcode, $model->order_number),
            ];
        }

        $errors = ActiveForm::validate($model);
        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }

    public function actionScanProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReturnScanningForm();
        $model->setScenario(ReturnScanningForm::SCENARIO_PRODUCT_BARCODE);
        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return [
                'success' => '0',
                'errors' => ActiveForm::validate($model),
            ];
        }

        $returnId = (int) $model->order_number;
        $productBarcode = (string) $model->product_barcode;
        $boxBarcode = (string) $model->box_barcode;
        $clientId = (int) $model->client_id;
        $now = time();

        // Найти или создать строку EcommerceReturnItem
        $rItem = EcommerceReturnItem::findOne([
            'return_id' => $returnId,
            'product_barcode' => $productBarcode,
        ]);
        if (!$rItem) {
            // Ручной возврат без pre-linked item — создаём.
            $rItem = new EcommerceReturnItem();
            $rItem->return_id = $returnId;
            $rItem->product_barcode = $productBarcode;
            $rItem->expected_qty = 1;
            $rItem->accepted_qty = 0;
            $rItem->status = ReturnOutboundStatus::SCANNING;
            $rItem->created_at = $now;
            $rItem->updated_at = $now;
            $rItem->deleted = 0;
            $rItem->save(false);
        }

        // Физика: если возврат связан с Kaspi-заказом, poll заранее пометил
        // stock-строку return_id + product_barcode. Обновляем её (одна физическая
        // единица — одна запись). Если pre-linked строки не осталось (все уже
        // отсканированы или это ручной возврат) — создаём новую.
        $stock = EcommerceStock::find()
            ->andWhere([
                'return_id' => $returnId,
                'product_barcode' => $productBarcode,
            ])
            ->andWhere(['not in', 'status', [
                EcommerceStock::STATUS_INBOUND_SCANNED,
                EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                EcommerceStock::STATUS_INBOUND_CONFIRM,
            ]])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if ($stock !== null) {
            $stock->box_address_barcode = $boxBarcode;
            $stock->status = EcommerceStock::STATUS_INBOUND_SCANNED;
            $stock->return_item_id = (int) $rItem->id;
            $stock->scan_in_datetime = $now;
            $stock->updated_at = $now;
            $stock->save(false);
        } else {
            $stock = new EcommerceStock();
            $stock->client_id = $clientId ?: (int) (EcommerceReturn::findOne($returnId)->client_id ?: 0);
            $stock->box_address_barcode = $boxBarcode;
            $stock->return_id = $returnId;
            $stock->return_item_id = (int) $rItem->id;
            $stock->product_barcode = $productBarcode;
            $stock->status = EcommerceStock::STATUS_INBOUND_SCANNED;
            $stock->status_availability = EcommerceStock::STATUS_AVAILABILITY_NOT_SET;
            $stock->scan_in_datetime = $now;
            $stock->save(false);
        }

        // Счётчики — всегда пересчитываем от фактических SCANNED/OVER_SCANNED строк,
        // чтобы не рассинхронизироваться при повторных сканах/очистках.
        $acceptedForItem = (int) EcommerceStock::find()
            ->andWhere([
                'return_id' => $returnId,
                'product_barcode' => $productBarcode,
                'status' => [
                    EcommerceStock::STATUS_INBOUND_SCANNED,
                    EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                ],
            ])->count();
        $rItem->accepted_qty = $acceptedForItem;
        if ($rItem->accepted_qty > $rItem->expected_qty) {
            $rItem->expected_qty = $rItem->accepted_qty; // перескан → расширяем план
        }
        $rItem->status = ReturnOutboundStatus::SCANNING;
        $rItem->updated_at = $now;
        $rItem->save(false);

        $return = EcommerceReturn::findOne($returnId);
        if ($return) {
            if (empty($return->begin_datetime)) {
                $return->begin_datetime = $now;
            }
            $return->accepted_qty = (int) EcommerceStock::find()
                ->andWhere([
                    'return_id' => $returnId,
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_SCANNED,
                        EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                    ],
                ])->count();
            if ($return->accepted_qty > $return->expected_qty) {
                $return->expected_qty = $return->accepted_qty;
            }
            $return->status = ReturnOutboundStatus::SCANNING;
            $return->updated_at = $now;
            $return->save(false);
        }

        $items = EcommerceReturnItem::find()
            ->andWhere(['return_id' => $returnId])
            ->orderBy(['accepted_qty' => SORT_ASC])
            ->asArray()
            ->all();

        $repo = new ReturnRepository();

        return [
            'success' => '1',
            'countProductInBox' => $repo->getScannedProductInBox($boxBarcode, $returnId),
            'countScannedProductInOrder' => $return ? (int) $return->accepted_qty : 0,
            'items' => $this->renderPartial('_order_items', ['items' => $items]),
            'dataScannedProductByBarcode' => ['countValue' => $rItem->expected_qty],
            'expected_qty' => $return ? (int) $return->expected_qty : 0,
            'accepted_qty' => $return ? (int) $return->accepted_qty : 0,
        ];
    }

    public function actionClearBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReturnScanningForm();
        $model->scenario = 'ClearBox';

        $items = [];
        $expectedQty = 0;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $returnId = (int) $model->order_number;
            $boxBarcode = (string) $model->box_barcode;

            $productsInBox = EcommerceStock::find()
                ->select('count(product_barcode) AS product_barcode_count, product_barcode')
                ->andWhere([
                    'box_address_barcode' => $boxBarcode,
                    'return_id' => $returnId,
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_SCANNED,
                        EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                    ],
                ])
                ->groupBy('product_barcode')
                ->all();

            foreach ($productsInBox as $row) {
                EcommerceStock::deleteAll([
                    'box_address_barcode' => $boxBarcode,
                    'return_id' => $returnId,
                    'product_barcode' => $row->product_barcode,
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_SCANNED,
                        EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                    ],
                ]);

                $countLeft = EcommerceStock::find()->andWhere([
                    'return_id' => $returnId,
                    'product_barcode' => $row->product_barcode,
                    'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
                ])->count();

                if ($rItem = EcommerceReturnItem::findOne(['return_id' => $returnId, 'product_barcode' => $row->product_barcode])) {
                    $rItem->accepted_qty = (int) $countLeft;
                    $rItem->expected_qty = max((int) $rItem->expected_qty, (int) $countLeft);
                    $rItem->save(false);
                }
            }

            if ($return = EcommerceReturn::findOne($returnId)) {
                $totalLeft = EcommerceStock::find()->andWhere([
                    'return_id' => $returnId,
                    'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
                ])->count();

                $return->status = ReturnOutboundStatus::SCANNING;
                $return->accepted_qty = (int) $totalLeft;
                if ($return->expected_qty < $return->accepted_qty) {
                    $return->expected_qty = $return->accepted_qty;
                }
                $return->save(false);
                $expectedQty = (int) $return->expected_qty;

                $items = EcommerceReturnItem::find()
                    ->andWhere(['return_id' => $returnId])
                    ->orderBy(['accepted_qty' => SORT_ASC])
                    ->asArray()
                    ->all();
            }
        }

        return [
            'success' => '1',
            'countScannedProductInOrder' => $model->order_number ? (int) (EcommerceReturn::findOne($model->order_number)->accepted_qty ?: 0) : 0,
            'expected_qty' => $expectedQty,
            'items' => $this->renderPartial('_order_items', ['items' => $items]),
        ];
    }

    public function actionClearProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReturnScanningForm();
        $model->scenario = 'ClearProductInBox';

        $countValue = 0;
        $colorRowClass = '';
        $rowId = '';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $returnId = (int) $model->order_number;

            EcommerceStock::deleteAll([
                'box_address_barcode' => $model->box_barcode,
                'product_barcode' => $model->product_barcode,
                'return_id' => $returnId,
                'status' => [
                    EcommerceStock::STATUS_INBOUND_SCANNED,
                    EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                ],
            ]);

            $countLeft = EcommerceStock::find()->andWhere([
                'return_id' => $returnId,
                'product_barcode' => $model->product_barcode,
                'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
            ])->count();

            if ($rItem = EcommerceReturnItem::findOne(['return_id' => $returnId, 'product_barcode' => $model->product_barcode])) {
                $rItem->accepted_qty = (int) $countLeft;
                $rItem->status = ReturnOutboundStatus::SCANNING;
                $rItem->save(false);

                $colorRowClass = 'alert-danger';
                if ($rItem->accepted_qty == $rItem->expected_qty) {
                    $colorRowClass = 'alert-success';
                } elseif ($rItem->accepted_qty > 0) {
                    $colorRowClass = 'alert-warning';
                }
                $countValue = (int) $rItem->accepted_qty;
                $rowId = $rItem->id . '-' . $model->product_barcode;
            }

            if ($return = EcommerceReturn::findOne($returnId)) {
                $totalLeft = EcommerceStock::find()->andWhere([
                    'return_id' => $returnId,
                    'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
                ])->count();
                $return->status = ReturnOutboundStatus::SCANNING;
                $return->accepted_qty = (int) $totalLeft;
                $return->save(false);
            }
        }

        $repo = new ReturnRepository();

        return [
            'success' => '1',
            'countProductInBox' => $repo->getScannedProductInBox($model->box_barcode, $model->order_number),
            'dataScannedProductByBarcode' => [
                'rowId' => $rowId,
                'countValue' => $countValue,
                'colorRowClass' => $colorRowClass,
            ],
        ];
    }

    public function actionConfirmOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $messages = [];
        $errors = [];

        $model = new ReturnScanningForm();
        $model->scenario = 'ConfirmOrder';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($return = EcommerceReturn::findOne($model->order_number)) {
                if ((int) $return->status === ReturnOutboundStatus::DONE) {
                    $messages[] = "Возврат с номером {$return->order_number} уже закрыт";
                } else {
                    $return->status = ReturnOutboundStatus::DONE;
                    $return->date_confirm = time();
                    $return->save(false);

                    EcommerceStock::updateAll(
                        [
                            'status' => EcommerceStock::STATUS_INBOUND_CONFIRM,
                            'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                        ],
                        [
                            'return_id' => $return->id,
                            'status' => [
                                EcommerceStock::STATUS_INBOUND_SCANNED,
                                EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                            ],
                        ]
                    );

                    $messages[] = "Возврат с номером {$return->order_number} успешно закрыт";

                    // Оповещение Kaspi: переводим заказ в RETURNED (только для возвратов из Kaspi-поллинга).
                    // Ошибка API не должна ломать подтверждение — пишем в лог и в сообщения UI.
                    if (!empty($return->source_kaspi_order_id)) {
                        try {
                            $service = new OrderReturnService();
                            $service->init();
                            $service->confirmReturnCompleted($return->source_kaspi_order_id);
                            // Фиксируем факт подтверждения со стороны Kaspi —
                            // отдельно от локального date_confirm.
                            $return->kaspi_returned_at = time();
                            $return->save(false);
                            $messages[] = "Kaspi уведомлён (order {$return->source_kaspi_order_id} → RETURNED)";
                        } catch (\Throwable $e) {
                            Yii::error(
                                'Kaspi confirmReturnCompleted failed for order '
                                . $return->source_kaspi_order_id . ': ' . $e->getMessage(),
                                'kaspi.return'
                            );
                            $messages[] = "Kaspi не уведомлён (API недоступен), возврат закрыт локально";
                        }
                    }
                }
            }
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => 'OK',
            'errors' => $errors,
            'messages' => $messages,
        ];
    }

    public function actionPrintListDifferences()
    {
        $id = Yii::$app->request->get('inbound_id');
        $items = [];
        if (EcommerceReturn::findOne($id)) {
            $items = EcommerceReturnItem::find()
                ->andWhere(['return_id' => $id])
                ->orderBy(['accepted_qty' => SORT_ASC])
                ->asArray()
                ->all();
        }

        if ($this->printType == 'html') {
            Yii::$app->layout = 'print-html';
            return $this->render('print/list-differences-html', ['items' => $items]);
        }
        return $this->render('print/list-differences-pdf', ['items' => $items]);
    }

    public function actionPrintUnallocatedList()
    {
        $id = Yii::$app->request->get('inbound_id');
        $items = [];
        if ($return = EcommerceReturn::findOne($id)) {
            $items = EcommerceStock::find()
                ->select('box_address_barcode AS primary_address, place_address_barcode AS secondary_address')
                ->andWhere([
                    'return_id' => $return->id,
                    'place_address_barcode' => '',
                ])
                ->andWhere(['not', ['box_address_barcode' => '']])
                ->groupBy('box_address_barcode')
                ->orderBy([
                    'place_address_barcode' => SORT_DESC,
                    'box_address_barcode' => SORT_DESC,
                ])
                ->asArray()
                ->all();
        }
        if ($this->printType == 'html') {
            Yii::$app->layout = 'print-html';
            return $this->render('print/print-unallocated-box-html', ['items' => $items]);
        }
        return $this->render('print/print-unallocated-box-pdf', ['items' => $items]);
    }
}
