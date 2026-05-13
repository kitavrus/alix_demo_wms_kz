<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\returns;

use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;
use common\models\ActiveRecord;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\alix\controllers\ecommerce\returns\domain\ReturnRepository;
use stockDepartment\modules\alix\controllers\ecommerce\returns\domain\ReturnScanningForm;
use stockDepartment\modules\kaspi\models\ProductBarcodesV2;
use stockDepartment\modules\kaspi\models\ProductV2;
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

        $return = EcommerceReturn::findOne($returnId);
        $sourceOutboundId = $return ? (int) $return->outbound_id : 0;
        if ($clientId <= 0 && $return) {
            $clientId = (int) $return->client_id;
        }
        $employeeId = Yii::$app->user && !Yii::$app->user->isGuest ? (int) Yii::$app->user->id : 0;

        // Найти или создать строку EcommerceReturnItem. При создании дописываем
        // product_id из исходной outbound-позиции — иначе ReturnItem остаётся
        // без идентификатора товара и теряет связь с каталогом.
        $rItem = EcommerceReturnItem::findOne([
            'return_id' => $returnId,
            'product_barcode' => $productBarcode,
        ]);
        if (!$rItem) {
            $rItem = new EcommerceReturnItem();
            $rItem->return_id = $returnId;
            $rItem->product_barcode = $productBarcode;
            $rItem->expected_qty = 1;
            $rItem->accepted_qty = 0;
            $rItem->status = ReturnOutboundStatus::SCANNING;
            $rItem->product_id = $this->resolveProductId($sourceOutboundId, $productBarcode);
            $rItem->created_at = $now;
            $rItem->updated_at = $now;
            $rItem->deleted = 0;
            $rItem->save(false);
        }

        // Физика: ищем EcommerceStock-ячейку в три приоритета, чтобы возврат
        // переписывал ту же запись, а не плодил пустые дубли.
        //
        //  1. pre-linked (Kaspi poll уже привязал return_id) — обычное поведение
        //     для Kaspi-возвратов;
        //  2. исходная отгруженная единица — `outbound_id` совпадает с
        //     `return->outbound_id`, return_id ещё не выставлен — это та самая
        //     ячейка, которая физически ушла со склада и сейчас возвращается.
        //     Перезаписываем — product_* остаются нетронутыми;
        //  3. over-scan — реального исходника нет, создаём новую запись, но
        //     обогащаем product_* из последней stock-записи по тому же штрихкоду
        //     или из карточки ProductV2.
        $stock = $this->findPreLinkedStock($returnId, $productBarcode);

        if ($stock === null && $sourceOutboundId > 0) {
            $stock = $this->findSourceOutboundStock($sourceOutboundId, $productBarcode);
        }

        if ($stock !== null) {
            $stock->return_id = $returnId;
            $stock->return_item_id = (int) $rItem->id;
            $stock->box_address_barcode = $boxBarcode;
            $stock->status = EcommerceStock::STATUS_INBOUND_SCANNED;
            $stock->status_availability = EcommerceStock::STATUS_AVAILABILITY_NOT_SET;
            $stock->scan_in_datetime = $now;
            if ($employeeId > 0) {
                $stock->scan_in_employee_id = $employeeId;
            }
            $stock->updated_at = $now;
            $stock->save(false);
        } else {
            $stock = new EcommerceStock();
            $stock->client_id = $clientId;
            $stock->box_address_barcode = $boxBarcode;
            $stock->return_id = $returnId;
            $stock->return_item_id = (int) $rItem->id;
            $stock->product_barcode = $productBarcode;
            $stock->status = EcommerceStock::STATUS_INBOUND_SCANNED;
            $stock->status_availability = EcommerceStock::STATUS_AVAILABILITY_NOT_SET;
            $stock->scan_in_datetime = $now;
            if ($employeeId > 0) {
                $stock->scan_in_employee_id = $employeeId;
            }
            $this->enrichStockFromCatalog($stock, $productBarcode, $clientId);
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

    /**
     * Pre-linked сток: Kaspi poll или ручное связывание уже выставили return_id
     * на физической единице. Берём её первой, чтобы не плодить дубль.
     */
    private function findPreLinkedStock($returnId, $productBarcode)
    {
        return EcommerceStock::find()
            ->andWhere([
                'return_id' => $returnId,
                'product_barcode' => $productBarcode,
            ])
            ->andWhere(['not in', 'status', [
                EcommerceStock::STATUS_INBOUND_SCANNED,
                EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                EcommerceStock::STATUS_INBOUND_CONFIRM,
            ]])
            ->andWhere(['deleted' => 0])
            ->orderBy(['id' => SORT_ASC])
            ->one();
    }

    /**
     * Исходная отгруженная физическая единица. У такой строки уже заполнены
     * product_id / product_sku / product_name / product_model — её и
     * перезаписываем при сканировании возврата.
     */
    private function findSourceOutboundStock($sourceOutboundId, $productBarcode)
    {
        return EcommerceStock::find()
            ->andWhere([
                'outbound_id' => (int) $sourceOutboundId,
                'product_barcode' => $productBarcode,
            ])
            ->andWhere(['or', ['return_id' => 0], ['return_id' => null]])
            ->andWhere(['deleted' => 0])
            ->orderBy(['id' => SORT_ASC])
            ->one();
    }

    /**
     * product_id для EcommerceReturnItem: сначала по позиции исходной отгрузки
     * (если возврат привязан к outbound), иначе резолвим штрихкод через карточку
     * ProductV2 / ProductBarcodesV2.
     */
    private function resolveProductId($sourceOutboundId, $productBarcode)
    {
        if ($sourceOutboundId > 0) {
            $oItem = EcommerceOutboundItem::find()
                ->andWhere(['outbound_id' => (int) $sourceOutboundId])
                ->andWhere(['product_barcode' => $productBarcode])
                ->andWhere(['deleted' => 0])
                ->one();
            if ($oItem && (int) $oItem->product_id > 0) {
                return (int) $oItem->product_id;
            }
        }

        $barcodeRow = ProductBarcodesV2::find()
            ->andWhere(['barcode' => $productBarcode])
            ->one();
        if ($barcodeRow) {
            return (int) $barcodeRow->product_id;
        }

        return 0;
    }

    /**
     * Over-scan: реального исходника не нашли. Заполняем product_* у новой
     * stock-записи из последней живой записи по тому же barcode/client (там
     * product_* уже корректные после inbound), либо как fallback — из карточки
     * ProductV2.
     */
    private function enrichStockFromCatalog(EcommerceStock $stock, $productBarcode, $clientId)
    {
        $reference = EcommerceStock::find()
            ->andWhere(['client_id' => (int) $clientId])
            ->andWhere(['product_barcode' => $productBarcode])
            ->andWhere(['deleted' => 0])
            ->andWhere(['not', ['product_id' => null]])
            ->andWhere(['!=', 'product_id', 0])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($reference !== null) {
            $stock->product_id = (int) $reference->product_id;
            $stock->product_sku = (string) $reference->product_sku;
            $stock->product_name = (string) $reference->product_name;
            $stock->product_model = (string) $reference->product_model;
            $stock->product_price = (string) $reference->product_price;
            return;
        }

        $barcodeRow = ProductBarcodesV2::find()
            ->andWhere(['barcode' => $productBarcode])
            ->one();
        if ($barcodeRow === null) {
            return;
        }
        $product = ProductV2::findOne((int) $barcodeRow->product_id);
        if ($product === null) {
            return;
        }
        $stock->product_id = (int) $product->id;
        $stock->product_sku = (string) $product->guid;
        $stock->product_name = (string) $product->name;
        $stock->product_model = (string) $product->article;
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
