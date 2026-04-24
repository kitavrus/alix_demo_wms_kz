<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\inbound;

use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItem;
use common\ecommerce\entities\EcommerceStock;
use common\models\ActiveRecord;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use common\overloads\ArrayHelper;
use stockDepartment\components\Controller;
use stockDepartment\modules\alix\controllers\ecommerce\inbound\domain\InboundForm;
use stockDepartment\modules\alix\controllers\ecommerce\inbound\domain\InboundScanningService;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class ScanningController extends Controller
{
    public function actionIndex()
    {
        $inboundForm = new InboundForm();
        $clientsArray = Client::getActiveWMSItems();
        $client_id = Client::CLIENT_ERENRETAIL;

        $partyNumberArray = ArrayHelper::map(
            EcommerceInbound::find()
                ->select('id, order_number, from_point_id')
                ->where([
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_NEW,
                        EcommerceStock::STATUS_INBOUND_SCANNING,
                        EcommerceStock::STATUS_INBOUND_SCANNED,
                    ],
                    'client_id' => $client_id,
                ])
                ->andWhere(['deleted' => ActiveRecord::NOT_SHOW_DELETED])
                ->asArray()
                ->all(),
            'id',
            function ($data, $defaultValue) {
                $title = $data['order_number'];
                if (!empty($data['from_point_id'])) {
                    $store = Store::findOne($data['from_point_id']);
                    if ($store) {
                        $title .= " / " . $store->getPointTitleByPattern();
                    }
                }
                return $title;
            }
        );

        $inboundForm->client_id = $client_id;
        return $this->render('index', [
            'inboundForm' => $inboundForm,
            'clientsArray' => $clientsArray,
            'partyNumberArray' => $partyNumberArray,
        ]);
    }

    public function actionGetInProcessInboundOrdersByClientId()
    {
        $clientID = 103;
        $data = self::mapNewAndInProcessByClientId($clientID);
        $type = 'inbound';

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Success',
            'type' => $type,
            'dataOptions' => $data,
        ];
    }

    public function actionGetInProcessInboundOrdersByPartyId()
    {
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;

        $party_id = Yii::$app->request->post('party_id');

        $data = ['' => ''];
        $data += \yii\helpers\ArrayHelper::map(
            EcommerceInbound::find()
                ->select('id, order_number')
                ->where([
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_NEW,
                        EcommerceStock::STATUS_INBOUND_SCANNING,
                        EcommerceStock::STATUS_INBOUND_SCANNED,
                    ],
                    'id' => $party_id,
                ])
                ->andWhere(['deleted' => ActiveRecord::NOT_SHOW_DELETED])
                ->asArray()
                ->all(),
            'id',
            'order_number'
        );

        if ($cio = EcommerceInbound::findOne($party_id)) {
            $expectedQtyParty = intval($cio->expected_product_qty);
            $acceptedQtyParty = intval($cio->accepted_product_qty);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Success',
            'dataOptions' => $data,
            'expectedQtyParty' => $expectedQtyParty,
            'acceptedQtyParty' => $acceptedQtyParty,
        ];
    }

    public function actionGetCompleteInboundOrdersByClientId()
    {
        $clientID = Yii::$app->request->post('client_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = ['' => ''];
        $data += \yii\helpers\ArrayHelper::map(
            EcommerceInbound::find()
                ->select('id, order_number')
                ->where([
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_CONFIRM,
                        EcommerceStock::STATUS_INBOUND_PREPARED_DATA_FOR_API,
                    ],
                    'client_id' => $clientID,
                ])
                ->andWhere(['deleted' => ActiveRecord::NOT_SHOW_DELETED])
                ->asArray()
                ->all(),
            'id',
            'order_number'
        );
        return [
            'message' => 'Success',
            'dataOptions' => $data,
        ];
    }

    public function actionGetScannedProductById()
    {
        $id = Yii::$app->request->post('inbound_id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $countScannedProductInOrder = EcommerceInbound::getCountItemByID($id);
        $items = [];
        $expected_qty = 0;
        if ($io = EcommerceInbound::findOne($id)) {
            $items = $io->getOrderItems()
                ->orderBy(['product_accepted_qty' => SORT_ASC])
                ->asArray()
                ->all();
            $expected_qty = $io->expected_product_qty;
        }

        return [
            'message' => 'Success',
            'countScannedProductInOrder' => $countScannedProductInOrder,
            'expected_qty' => $expected_qty,
            'items' => $this->renderPartial('_order_items', ['items' => $items]),
        ];
    }

    public function actionValidateScannedBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new InboundForm();
        $model->scenario = 'ScannedBox';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return [
                'success' => '1',
                'countProductInBox' => EcommerceInboundItem::getScannedProductInBox($model->box_barcode, $model->order_number),
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
        $expected_qty = 0;
        $model = new InboundForm();
        $model->scenario = 'ScannedProduct';
        $post = Yii::$app->request->post();
        $stockId = -1;

        if ($model->load($post) && $model->validate()) {
            $stock = $model->setScannedStatus();
            try {
                (new InboundScanningService())->sendStatusInWork($model->order_number);
            } catch (\Throwable $e) {
                Yii::error('sendStatusInWork failed: ' . $e->getMessage(), __METHOD__);
            }

            $ioi = EcommerceInboundItem::find()
                ->andWhere([
                    'inbound_id' => $model->order_number,
                    'product_barcode' => $model->product_barcode,
                ])
                ->one();

            $stockId = $stock->id;

            $countStockForItem = EcommerceStock::find()->andWhere([
                'inbound_id' => $model->order_number,
                'product_barcode' => $model->product_barcode,
                'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
                'client_id' => $model->client_id,
            ])->count();

            if ($ioi) {
                if (intval($ioi->product_accepted_qty) < 1) {
                    $ioi->begin_datetime = time();
                    $ioi->status = EcommerceStock::STATUS_INBOUND_SCANNING;
                }

                $ioi->product_accepted_qty = $countStockForItem;

                if ($ioi->product_accepted_qty == $ioi->product_expected_qty) {
                    $ioi->status = EcommerceStock::STATUS_INBOUND_SCANNED;
                }

                $ioi->end_datetime = time();
                $ioi->save(false);
            }

            $countStockForOrder = EcommerceStock::find()->where([
                'inbound_id' => $model->order_number,
                'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
                'client_id' => $model->client_id,
            ])->count();

            if ($inboundModel = EcommerceInbound::findOne($model->order_number)) {
                if (intval($inboundModel->accepted_product_qty) < 1) {
                    $inboundModel->begin_datetime = time();
                    $inboundModel->status = EcommerceStock::STATUS_INBOUND_SCANNING;
                }

                $inboundModel->accepted_product_qty = $countStockForOrder;

                if ($inboundModel->accepted_product_qty == $inboundModel->expected_product_qty) {
                    $inboundModel->status = EcommerceStock::STATUS_INBOUND_SCANNED;
                }

                $inboundModel->end_datetime = time();
                $inboundModel->save(false);

                $expected_qty = $inboundModel->expected_product_qty;
            }

            $expectedQtyParty = 0;
            $acceptedQtyParty = 0;

            $colorRowClass = 'alert-danger';
            if ($ioi->product_accepted_qty == $ioi->product_expected_qty) {
                $colorRowClass = 'alert-success';
            } elseif ($ioi->product_accepted_qty > 0) {
                $colorRowClass = 'alert-warning';
            }

            return [
                'success' => '1',
                'countProductInBox' => EcommerceInboundItem::getScannedProductInBox($model->box_barcode, $model->order_number),
                'countScannedProductInOrder' => EcommerceInbound::getCountItemByID($model->order_number),
                'expectedQtyParty' => $expectedQtyParty,
                'acceptedQtyParty' => $acceptedQtyParty,
                'expected_qty' => $expected_qty,
                'stockId' => $stockId,
                'dataScannedProductByBarcode' => [
                    'rowId' => $ioi->id . '-' . $model->product_barcode,
                    'expected_qty' => $ioi->product_expected_qty,
                    'countValue' => $ioi->product_accepted_qty,
                    'colorRowClass' => $colorRowClass,
                ],
            ];
        }

        $errors = ActiveForm::validate($model);
        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }

    public function actionPrintListDifferences()
    {
        $id = Yii::$app->request->get('inbound_id');
        $items = [];
        $orderNumber = '';
        $fromPointId = '';
        $expectedQtyCount = 0;
        $acceptedQtyCount = 0;
        if ($io = EcommerceInbound::findOne($id)) {
            $orderNumber = $io->order_number;
            $fromPointId = $io->from_point_id;
            $expectedQtyCount += $io->expected_product_qty;
            $acceptedQtyCount += $io->accepted_product_qty;

            $items = $io->getOrderItems()
                ->orderBy(['product_accepted_qty' => SORT_ASC])
                ->asArray()
                ->all();
        }

        $store = Store::findOne($fromPointId);

        if ($this->printType == 'html') {
            Yii::$app->layout = 'print-html';
            return $this->render('print/list-differences-html', ['items' => $items]);
        }
        return $this->render('print/list-differences-pdf', [
            'orderNumber' => $orderNumber,
            'items' => $items,
            'store' => $store,
            'expectedQtyCount' => $expectedQtyCount,
            'acceptedQtyCount' => $acceptedQtyCount,
        ]);
    }

    public function actionConfirmOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];

        $model = new InboundForm();
        $model->scenario = 'ConfirmOrder';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($io = EcommerceInbound::findOne($model->order_number)) {
                if ($io->status == EcommerceStock::STATUS_INBOUND_CONFIRM) {
                    $messages[] = Yii::t('inbound/errors', 'Накладная с номером ' . $io->order_number . ' уже принята');
                } else {
                    $io->status = EcommerceStock::STATUS_INBOUND_CONFIRM;
                    $io->date_confirm = time();
                    $io->save(false);

                    EcommerceStock::updateAll(
                        [
                            'status' => EcommerceStock::STATUS_INBOUND_CONFIRM,
                            'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                        ],
                        [
                            'inbound_id' => $io->id,
                            'status' => [
                                EcommerceStock::STATUS_INBOUND_SCANNED,
                                EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                            ],
                        ]
                    );

                    EcommerceStock::deleteAll(
                        'inbound_id = :inbound_id AND status != :status',
                        [':inbound_id' => $io->id, ':status' => EcommerceStock::STATUS_INBOUND_CONFIRM]
                    );

                    $messages[] = Yii::t('inbound/errors', 'Накладная с номером ' . $io->order_number . ' успешно принята');
                    try {
                        (new InboundScanningService())->sendStatusCompleted($io->id);
                    } catch (\Throwable $e) {
                        Yii::error('sendStatusCompleted failed: ' . $e->getMessage(), __METHOD__);
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

    public function actionClearProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $countValue = 0;
        $colorRowClass = '';
        $rowId = '';
        $expected_qty = 0;
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;

        $model = new InboundForm();
        $model->scenario = 'ClearProductInBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            EcommerceStock::deleteAll([
                'box_address_barcode' => $model->box_barcode,
                'product_barcode' => $model->product_barcode,
                'inbound_id' => $model->order_number,
                'status' => [
                    EcommerceStock::STATUS_INBOUND_SCANNED,
                    EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                ],
            ]);

            $countStockForItem = EcommerceStock::find()->where([
                'inbound_id' => $model->order_number,
                'product_barcode' => $model->product_barcode,
                'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
            ])->count();

            if ($ioi = EcommerceInboundItem::findOne(['product_barcode' => $model->product_barcode, 'inbound_id' => $model->order_number])) {
                $ioi->product_accepted_qty = $countStockForItem;
                $ioi->status = EcommerceStock::STATUS_INBOUND_SCANNING;
                $ioi->save(false);

                $colorRowClass = 'alert-danger';
                if ($ioi->product_accepted_qty == $ioi->product_expected_qty) {
                    $colorRowClass = 'alert-success';
                } elseif ($ioi->product_accepted_qty > 0) {
                    $colorRowClass = 'alert-warning';
                }

                $countValue = $ioi->product_accepted_qty;
                $rowId = $ioi->id . '-' . $model->product_barcode;
            }

            $countStockForOrder = EcommerceStock::find()->where([
                'inbound_id' => $model->order_number,
                'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
            ])->count();

            if ($inbound = EcommerceInbound::findOne($model->order_number)) {
                $inbound->status = EcommerceStock::STATUS_INBOUND_SCANNING;
                $inbound->accepted_product_qty = $countStockForOrder;
                $inbound->save(false);

                $expected_qty = $inbound->expected_product_qty;
                $expectedQtyParty = $inbound->expected_product_qty;
                $acceptedQtyParty = $inbound->accepted_product_qty;
            }
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countProductInBox' => EcommerceInboundItem::getScannedProductInBox($model->box_barcode, $model->order_number),
            'countScannedProductInOrder' => EcommerceInbound::getCountItemByID($model->order_number),
            'expectedQtyParty' => $expectedQtyParty,
            'acceptedQtyParty' => $acceptedQtyParty,
            'expected_qty' => $expected_qty,
            'dataScannedProductByBarcode' => [
                'rowId' => $rowId,
                'countValue' => $countValue,
                'colorRowClass' => $colorRowClass,
            ],
        ];
    }

    public function actionClearBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $dataScannedProductByBarcode = [];
        $expected_qty = 0;
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;

        $model = new InboundForm();
        $model->scenario = 'ClearBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $productsInBox = EcommerceStock::find()
                ->select('count(product_barcode) as product_barcode_count, product_barcode')
                ->where([
                    'box_address_barcode' => $model->box_barcode,
                    'inbound_id' => $model->order_number,
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_SCANNED,
                        EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                    ],
                ])
                ->groupBy('product_barcode')
                ->all();

            if ($productsInBox) {
                foreach ($productsInBox as $item) {
                    if ($ioi = EcommerceInboundItem::findOne([
                        'product_barcode' => $item->product_barcode,
                        'inbound_id' => $model->order_number,
                    ])) {
                        EcommerceStock::deleteAll([
                            'box_address_barcode' => $model->box_barcode,
                            'inbound_id' => $model->order_number,
                            'product_barcode' => $item->product_barcode,
                            'status' => [
                                EcommerceStock::STATUS_INBOUND_SCANNED,
                                EcommerceStock::STATUS_INBOUND_OVER_SCANNED,
                            ],
                        ]);

                        $countStockForItem = EcommerceStock::find()->where([
                            'inbound_id' => $model->order_number,
                            'product_barcode' => $item->product_barcode,
                            'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
                        ])->count();

                        $ioi->product_accepted_qty = $countStockForItem;
                        $ioi->save(false);

                        $colorRowClass = 'alert-danger';
                        if ($ioi->product_accepted_qty == $ioi->product_expected_qty) {
                            $colorRowClass = 'alert-success';
                        } elseif ($ioi->product_accepted_qty > 0) {
                            $colorRowClass = 'alert-warning';
                        }

                        $dataScannedProductByBarcode[] = [
                            'rowId' => $ioi->id . '-' . $item->product_barcode,
                            'countValue' => $ioi->product_accepted_qty,
                            'colorRowClass' => $colorRowClass,
                        ];
                    }
                }

                $countStockForOrder = EcommerceStock::find()->where([
                    'inbound_id' => $model->order_number,
                    'status' => EcommerceStock::STATUS_INBOUND_SCANNED,
                ])->count();

                if ($inbound = EcommerceInbound::findOne($model->order_number)) {
                    $inbound->status = EcommerceStock::STATUS_INBOUND_SCANNING;
                    $inbound->accepted_product_qty = $countStockForOrder;
                    $inbound->save(false);

                    $expected_qty = $inbound->expected_product_qty;
                }
            }
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countScannedProductInOrder' => EcommerceInbound::getCountItemByID($model->order_number),
            'expected_qty' => $expected_qty,
            'dataScannedProductByBarcode' => $dataScannedProductByBarcode,
            'expectedQtyParty' => $expectedQtyParty,
            'acceptedQtyParty' => $acceptedQtyParty,
        ];
    }

    public function actionPrintUnallocatedList()
    {
        $id = Yii::$app->request->get('inbound_id');

        $items = [];
        if ($io = EcommerceInbound::findOne($id)) {
            $items = EcommerceStock::find()
                ->select('box_address_barcode AS primary_address, place_address_barcode AS secondary_address')
                ->where([
                    'inbound_id' => $io->id,
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

    public function actionScanDatamatrix()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inboundForm = new InboundForm();
        $inboundForm->setScenario(InboundForm::SCENARIO_SCAN_DATAMATRIX);

        if ($inboundForm->load(Yii::$app->request->post()) && $inboundForm->validate()) {
            $inboundOrderService = new InboundScanningService($inboundForm->getDTO());
            $inboundOrderService->scanDataMatrix();

            return [
                'success' => '1',
            ];
        }
        $errors = ActiveForm::validate($inboundForm);
        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
        ];
    }

    private static function mapNewAndInProcessByClientId($clientID)
    {
        return \yii\helpers\ArrayHelper::map(
            EcommerceInbound::find()
                ->select('id, order_number')
                ->where([
                    'status' => [
                        EcommerceStock::STATUS_INBOUND_NEW,
                        EcommerceStock::STATUS_INBOUND_SCANNING,
                        EcommerceStock::STATUS_INBOUND_SCANNED,
                    ],
                    'client_id' => $clientID,
                ])
                ->andWhere(['deleted' => ActiveRecord::NOT_SHOW_DELETED])
                ->asArray()
                ->all(),
            'id',
            'order_number'
        );
    }
}
