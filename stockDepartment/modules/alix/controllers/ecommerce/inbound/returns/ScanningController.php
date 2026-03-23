<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\inbound\returns;

use Yii;
use yii\web\Response;
use yii\helpers\VarDumper;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\ActiveRecord;
use common\modules\stock\models\Stock;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\inbound\models\ConsignmentInboundOrders;
use stockDepartment\modules\intermode\controllers\ecommerce\inbound\domain\InboundRepository;
use stockDepartment\modules\intermode\controllers\ecommerce\inbound\domain\InboundForm;

class ScanningController extends Controller
{
    public function actionIndex()
    {
        $inboundForm = new InboundForm();
        $client_id = Client::CLIENT_ERENRETAIL;
        $inboundForm->client_id = $client_id;

        $partyNumberArray = ArrayHelper::map(
            InboundOrder::find()
                ->select('id, order_number')
                ->andWhere(
                    [
                        'order_type' => InboundOrder::ORDER_TYPE_ECOMM_RETURN,
                        'client_id' => $client_id
                    ]
                )
				->andWhere("status != 9")
                ->orderBy(['created_at' => SORT_DESC])
                ->asArray()->all(),
            'id',
            function ($data, $defaultValue) {
                return $data['order_number'];
            }
        );

        $inboundForm->client_id = $client_id;

        // Подставляем первый элемент массива как выбранный
		$id = 0;
        if (!empty($partyNumberArray)) {
            reset($partyNumberArray);
            $inboundForm->order_number = key($partyNumberArray);
        }

		$items = [];
		$expected_qty = 0;
		$accepted_qty = 0;
		if ($io = InboundOrder::find()->andWhere(["id"=> $inboundForm->order_number])->one()) {
			$items = $io->getOrderItems()->orderBy([
				'accepted_qty' => SORT_ASC
			])->asArray()
			->all();
			
			$expected_qty = $io->expected_qty;
			$accepted_qty = $io->accepted_qty ;
		}

        return $this->render(
            'index',
            [
                'inboundForm' => $inboundForm,
                'partyNumberArray' => $partyNumberArray,
				'items' => $this->renderPartial('_order_items', ['items' => $items]),
				'expected_qty' => $expected_qty,
				'accepted_qty' => $accepted_qty,
            ]
        );
    }

    public function actionScanningReturns()
    {
        $inboundForm = new InboundForm();

        return $this->render(
            'scanning-returns',
            [
                'inboundForm' => $inboundForm,
            ]
        );
    }

    public function actionGetScannedProductById()
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $countScannedProductInOrder = InboundOrder::getCountItemByID($id);
        $items = [];
        if ($io = InboundOrder::findOne($id)) {
            $items = $io
                ->getOrderItems()
                ->orderBy([
                    'accepted_qty' => SORT_ASC
                ])
                ->asArray()
                ->all();
        }

        return [
            'message' => 'Success',
            'countScannedProductInOrder' => $countScannedProductInOrder,
            'expected_qty' => $io->expected_qty,
            'items' => $this->renderPartial('_order_items', ['items' => $items]),
        ];
    }

    public function actionGetInProcessInboundOrdersByClientId()
    {
        $clientID = Yii::$app->request->post('client_id');
        $data = ['' => ''];
		$data += InboundOrder::getNewAndInProcessItemByClientID($clientID);
		$type = 'inbound';

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Success',
            'type' => $type,
            'dataOptions' => $data,
        ];
    }

    /*
     * Validate scanned box
     * @return JSON true or errors array
     * */
    public function actionValidateScannedBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new InboundForm();
		$model->setScenario(InboundForm::SCENARIO_BOX_BARCODE);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            return [
                'success' => '1',
                'countProductInBox' => InboundOrderItem::getScannedProductInBox(
                    $model->box_barcode,
                    $model->order_number
                ),
            ];
        } else {
            $errors = ActiveForm::validate($model);
            return [
                'success' => (empty($errors) ? '1' : '0'),
                'errors' => $errors
            ];
        }
    }

    /**
     * Clear all product in box
     * @param string $box_barcode Box barcode
     * */
    public function actionClearBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $dataScannedProductByBarcode = [];
		$items = [];
        $expected_qty = 0;
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;

        $model = new InboundForm();
        $model->scenario = 'ClearBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$productsInBox = Stock::find()
								  ->select(
									  'count(product_barcode) as product_barcode_count, product_barcode'
								  )
								  ->andWhere(
									  [
										  'primary_address' => $model->box_barcode,
										  'inbound_order_id' => $model->order_number,
										  'status' => [
											  Stock::STATUS_INBOUND_SCANNED,
											  Stock::STATUS_INBOUND_OVER_SCANNED
										  ]
									  ]
								  )
								  ->groupBy('product_barcode')
								  ->all();

            if ($productsInBox) {
                foreach ($productsInBox as $item) {
					$ioi = InboundOrderItem::findOne(
						[
							'product_barcode' => $item->product_barcode,
							'inbound_order_id' => $model->order_number,
						]
					);

                    if ($ioi) {

                        Stock::deleteAll(
                            [
                                'primary_address' => $model->box_barcode,
                                'inbound_order_id' => $model->order_number,
                                'product_barcode' => $item->product_barcode,
                                'status' => [
                                    Stock::STATUS_INBOUND_SCANNED,
                                    Stock::STATUS_INBOUND_OVER_SCANNED
                                ]
                            ]
                        );

                        $countStockForItem = Stock::find()
                            ->andWhere(
                                [
                                    'inbound_order_id' => $model->order_number,
                                    'product_barcode' => $item->product_barcode,
                                    'status' => Stock::STATUS_INBOUND_SCANNED,
                                ]
                            )
                            ->count();

                        $ioi->expected_qty = $countStockForItem;
                        $ioi->accepted_qty = $countStockForItem;
                        $ioi->save(false);
                    }
                }

                $countStockForOrder = Stock::find()
                    ->andWhere(
                        [
                            'inbound_order_id' => $model->order_number,
                            'status' => Stock::STATUS_INBOUND_SCANNED,
                        ]
                    )
                    ->count();


                if ($inbound = InboundOrder::findOne($model->order_number)) {
                    $inbound->status = Stock::STATUS_INBOUND_SCANNING;
                    $inbound->expected_qty = $countStockForOrder;
                    $inbound->accepted_qty = $countStockForOrder;
                    $inbound->save(false);
                    $expected_qty = $inbound->expected_qty;
					$items = $inbound
						->getOrderItems()
						->orderBy([
							'accepted_qty' => SORT_ASC
						])
						->asArray()
						->all();
                }
            }
        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countScannedProductInOrder' => InboundOrder::getCountItemByID($model->order_number),
            'expected_qty' => $expected_qty,
            'dataScannedProductByBarcode' => $dataScannedProductByBarcode,
            'expectedQtyParty' => $expectedQtyParty,
            'acceptedQtyParty' => $acceptedQtyParty,
			'items' => $this->renderPartial('_order_items', ['items' => $items]),
        ];
    }

    /**
     * Scanned product in box
     * @return JSON true or errors array
     * */
    public function actionScanProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new InboundForm();
        $model->setScenario(InboundForm::SCENARIO_PRODUCT_BARCODE);
        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return [
                'success' => '0',
                'errors' => ActiveForm::validate($model)
            ];
        }

        $orderNumber = $model->order_number;
        $productBarcode = $model->product_barcode;
        $boxBarcode = $model->box_barcode;
        $clientId = $model->client_id;

        // Найдём или создадим строку InboundOrderItem
        $ioi = InboundOrderItem::findOne([
            'inbound_order_id' => $orderNumber,
            'product_barcode' => $productBarcode,
        ]);

        if (!$ioi) {
            $ioi = new InboundOrderItem();
            $ioi->inbound_order_id = $orderNumber;
            $ioi->product_barcode = $productBarcode;
            $ioi->expected_qty = 1;
            $ioi->accepted_qty = 1;
        } else {
            $ioi->expected_qty += 1;
            $ioi->accepted_qty += 1;
        }
        $ioi->save(false);

        $io = InboundOrder::findOne($orderNumber);
        $io->expected_qty += 1;
        $io->accepted_qty += 1;
        $io->status = Stock::STATUS_INBOUND_SCANNING;
        $io->save(false);

        $stock = new Stock();
        $stock->client_id = $clientId;
        $stock->primary_address = $boxBarcode;
        $stock->inbound_order_id = $orderNumber;
        $stock->inbound_order_item_id = $ioi->id;
        $stock->product_barcode = $productBarcode;
        $stock->status = Stock::STATUS_INBOUND_SCANNED;
        $stock->save(false);

		$items = $io->getOrderItems()->orderBy([
			'accepted_qty'=>SORT_ASC
		])->asArray()
		  ->all();

        return [
            'success' => '1',
            'countProductInBox' => (new InboundRepository())->getScannedProductInBox(
                $model->box_barcode,
                $model->order_number
            ),
            'countScannedProductInOrder' => InboundOrder::getCountItemByID($model->order_number),
			'items' =>$this->renderPartial('_order_items',['items'=>$items]),
            'dataScannedProductByBarcode' => [
                'countValue' => $ioi->expected_qty,
            ],
            'expected_qty' => $io->expected_qty,
            'accepted_qty' => $io->accepted_qty,
        ];
    }

    /*
     * Delete product by barcode  in box
     * @return JSON true or errors array
     * */
    public function actionClearProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];
        $countProductInBox = 0;
        $countValue = 0;
        $colorRowClass = '';
        $rowId = '';
        $expected_qty = 0;
        $expectedQtyParty = 0;
        $acceptedQtyParty = 0;

        $model = new InboundForm();
        $model->scenario = 'ClearProductInBox';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Stock::deleteAll(
                [
                    'primary_address' => $model->box_barcode,
                    'product_barcode' => $model->product_barcode,
                    'inbound_order_id' => $model->order_number,
                    'status' => [
                        Stock::STATUS_INBOUND_SCANNED,
                        Stock::STATUS_INBOUND_OVER_SCANNED
                    ]
                ]
            );

            $countStockForItem = Stock::find()
                ->andWhere(
                    [
                        'inbound_order_id' => $model->order_number,
                        'product_barcode' => $model->product_barcode,
                        'status' => Stock::STATUS_INBOUND_SCANNED,
                    ]
                )
                ->count();

            if (
                $ioi = InboundOrderItem::findOne(
                    [
                        'product_barcode' => $model->product_barcode,
                        'inbound_order_id' => $model->order_number
                    ]
                )
            ) {
                $ioi->accepted_qty = $countStockForItem;
                $ioi->status = Stock::STATUS_INBOUND_SCANNING;
                $ioi->save(false);

                $colorRowClass = 'alert-danger';
                if ($ioi->accepted_qty == $ioi->expected_qty) {
                    $colorRowClass = 'alert-success';
                } elseif ($ioi->accepted_qty > 0) {
                    $colorRowClass = 'alert-warning';
                }

                $countValue = $ioi->accepted_qty;
                $rowId = $ioi->id . '-' . $model->product_barcode;
            }
            ;

            $countStockForOrder = Stock::find()
                ->andWhere(
                    [
                        'inbound_order_id' => $model->order_number,
                        'status' => Stock::STATUS_INBOUND_SCANNED,
                    ]
                )
                ->count();

            if ($inbound = InboundOrder::findOne($model->order_number)) {
                $inbound->status = Stock::STATUS_INBOUND_SCANNING;
                $inbound->accepted_qty = $countStockForOrder;
                $inbound->save(false);

                $expected_qty = $inbound->expected_qty;
                $expectedQtyParty = $inbound->expected_qty;
                $acceptedQtyParty = $inbound->accepted_qty;
            }

        } else {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors,
            'messages' => $messages,
            'countProductInBox' => InboundOrderItem::getScannedProductInBox(
                $model->box_barcode,
                $model->order_number
            ),
            'countScannedProductInOrder' => InboundOrder::getCountItemByID($model->order_number),
            'expectedQtyParty' => $expectedQtyParty,
            'acceptedQtyParty' => $acceptedQtyParty,
            'expected_qty' => $expected_qty,
            'dataScannedProductByBarcode' => [
                'rowId' => $rowId,
                'countValue' => $countValue,
                'colorRowClass' => $colorRowClass
            ],
        ];
    }

    /*
     * Print the list of differences
     * */
    public function actionPrintListDifferences()
    {
        $id = Yii::$app->request->get('inbound_id');

        $items = [];
        if ($io = InboundOrder::findOne($id)) {
            $items = $io->getOrderItems()
                ->orderBy([
                    'accepted_qty' => SORT_ASC
                ])
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
        if ($io = InboundOrder::findOne($id)) {
            $items = Stock::find()
                ->select('primary_address, secondary_address')
                ->andWhere([
                    'inbound_order_id' => $io->id,
                    'secondary_address' => '',
                ])
                ->andWhere([
                    'not',
                    ['primary_address' => '']
                ])
                ->groupBy('primary_address')
                ->orderBy([
                    'secondary_address' => SORT_DESC,
                    'primary_address' => SORT_DESC,
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

    /*
    * Confirm inbound order data
    * @return JSON true or errors array
    * */
    public function actionConfirmOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = [];

        $model = new InboundForm();
        $model->scenario = 'ConfirmOrder';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($io = InboundOrder::findOne($model->order_number)) {
                if ($io->status == Stock::STATUS_INBOUND_CONFIRM) {
                    $messages[] = Yii::t(
                        'inbound/errors',
                        'Накладная с номером ' . $io->order_number . ' уже принята'
                    );
                } else {
                    $io->status = Stock::STATUS_INBOUND_CONFIRM;
                    $io->save(false);

                    Stock::updateAll(
                        [
                            'status' => Stock::STATUS_INBOUND_CONFIRM,
                            'status_availability' => Stock::STATUS_AVAILABILITY_YES,
                        ],
                        [
                            'inbound_order_id' => $io->id,
                            'status' => [
                                Stock::STATUS_INBOUND_SCANNED,
                                Stock::STATUS_INBOUND_OVER_SCANNED,
                            ]
                        ]
                    );

                    Stock::deleteAll('inbound_order_id = :inbound_order_id AND status != :status', [':inbound_order_id' => $io->id, ':status' => Stock::STATUS_INBOUND_CONFIRM]);

                    $messages[] = Yii::t(
                        'inbound/errors',
                        'Накладная с номером ' . $io->order_number . ' успешно принята'
                    );
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

    public function actionCreateNewOrder()
    {
        $form = new InboundForm();
		$form->setScenario(InboundForm::SCENARIO_ORDER_NUMBER);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $dtoRepository = new \stdClass();
            $dtoRepository->clientId = 103;
            $dtoRepository->orderType = InboundOrder::ORDER_TYPE_ECOMM_RETURN;

            $repository = new InboundRepository($dtoRepository);
            if ($repository->isOrderExist($form->order_number)) {
                Yii::$app->session->setFlash(
                    'error',
                    "Заказ с таким номером '{$form->order_number}' уже существует!"
                );
                return $this->render(
                    'scanning-returns',
                    [
                        'inboundForm' => $form,
                    ]
                );
            }

            $repository->create(
                $this->makeDTOForCreateInboundOrder(
                    $form->order_number
                )
            );

            return $this->redirect(['index']);
        }

        return $this->render(
            'scanning-returns',
            [
                'inboundForm' => $form,
            ]
        );
    }

    private function makeDTOForCreateInboundOrder($orderNumber)
    {
        $dtoForCreateInboundOrder = new \stdClass();
        $dtoForCreateInboundOrder->orderNumber = trim($orderNumber);
        $dtoForCreateInboundOrder->supplierId = 1;
        $dtoForCreateInboundOrder->expectedTotalProductQty = 0;
        $dtoForCreateInboundOrder->expectedTotalPlaceQty = 0;
        $dtoForCreateInboundOrder->totalQtyRows = 0;
        $dtoForCreateInboundOrder->comment = '';
        $dtoForCreateInboundOrder->items = [];

        return $dtoForCreateInboundOrder;
    }
}