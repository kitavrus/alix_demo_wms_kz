<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\components\BarcodeManager;
//use common\modules\stock\models\InventoryRows;
//use common\modules\stock\models\Stock;
//use stockDepartment\modules\stock\models\InventoryForm;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Response;

//use common\modules\stock\models\Inventory;

//use common\modules\client\models\Client;
use common\ecommerce\entities\EcommerceInventoryRows;
use common\ecommerce\entities\EcommerceInventory;
//use common\ecommerce\entities\EcommerceInventorySearch;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\defacto\inventory\forms\InventoryForm;

class InventoryProcessController extends Controller
{
    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index',[
            'InventoryForm'=>new InventoryForm(),
        ]);
    }

    /*
* Сканируем ряд и обнуляем адреса коробок
* @return JSON
* */
    public function actionStart()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inventoryForm = new InventoryForm();
        $inventoryForm->scenario = 'SecondaryAddress';
        $restart = Yii::$app->request->get('restart',null);

        $successMessages = [];
        $success = 0;
        if ($inventoryForm->load(Yii::$app->request->post()) && $inventoryForm->validate()) {

            $minMax = EcommerceInventory::getMinMaxSecondaryAddress($inventoryForm->place_address_barcode);
            // обнуляем короба
            $stockAll = EcommerceStock::find()->andWhere(['place_address_barcode'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])->all();

            if(!empty($stockAll)) {
                if (is_null($restart)) {
                    foreach ($stockAll as $stock) {
                        if($stock->box_address_barcode != EcommerceInventory::INVENTORY_BARCODE) {
                            $stock->inventory_box_address_barcode = $stock->box_address_barcode;
                            $stock->inventory_place_address_barcode = $stock->place_address_barcode;
                            $stock->save(false);
                        }
                    }
                }

                $stockRowQty = EcommerceStock::find()->andWhere(['place_address_barcode'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])->count();
                // Выбираем ожидаемое кол-во коробов
//                $stockRowBoxQty = EcommerceStock::find()->andWhere(['place_address_barcode'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])->groupBy('box_address_barcode')->count();

                $column_number = EcommerceInventory::getRowNumber($inventoryForm->place_address_barcode);
                $floor_number = EcommerceInventory::getFloorNumber($inventoryForm->place_address_barcode);
                $level_number = EcommerceInventory::getLevelNumber($inventoryForm->place_address_barcode);

                $conditionWhere = [
                                    'inventory_id'=>$inventoryForm->inventory_id,
                                    'floor_number'=>$floor_number,
                                    'column_number'=>$column_number,
//                                  'level_number'=>$level_number
                ];

                if (!$invRow = EcommerceInventoryRows::find()->andWhere($conditionWhere)->one()) {
                    $invRow = new EcommerceInventoryRows();
                    $invRow->inventory_id = $inventoryForm->inventory_id;
                    $invRow->status = EcommerceInventory::STATUS_IN_PROCESS;
                    $invRow->column_number = $column_number;
                    $invRow->floor_number = $floor_number;
                    $invRow->level_number = $level_number;
                    $invRow->row_number = $inventoryForm->place_address_barcode;
//                    $invRow->expected_places_qty = $stockRowBoxQty;
                }

                $invRow->expected_qty = $stockRowQty;
                $invRow->save(false);

                EcommerceStock::updateAll([
                    'box_address_barcode'=>EcommerceInventory::INVENTORY_BARCODE,
                    'status_inventory'=>EcommerceInventory::STATUS_SCAN_PROCESS
                ],
                    [
                        'place_address_barcode'=>$minMax,
                        'inventory_id'=>$inventoryForm->inventory_id
                    ]
                );
            }

            if(!$inventoryForm->hasErrors()) {
                $success = 1;
                $contentToFile = "Message; Product barcode; Primary address; Special Message; Secondary address"."\n";
                file_put_contents(EcommerceInventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $inventoryForm->getErrors(),
            ];
        }

        return [
            'success'=>$success,
            'errors' => ActiveForm::validate($inventoryForm)
        ];
    }




    /*
     * Сканируем ряд и обнуляем адреса коробок
     * @return JSON
     * */
    public function actionSecondaryAddress()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inventoryForm = new InventoryForm();
        $inventoryForm->scenario = 'SecondaryAddress';

        $successMessages = [];
        $success = 0;
        if ($inventoryForm->load(Yii::$app->request->post()) && $inventoryForm->validate()) {

            $startMessage = "Для НАЧАЛА инвентаризации нажмите <span class='btn btn-warning' id='start-inventory-bt' data-url='".Url::toRoute('start')."'>Начать инвентаризацию</span>";
            $row = EcommerceInventory::getRowNumber($inventoryForm->place_address_barcode);
            $floor = EcommerceInventory::getFloorNumber($inventoryForm->place_address_barcode);
            $level_number = EcommerceInventory::getLevelNumber($inventoryForm->place_address_barcode);

            if(EcommerceInventory::checkStart($row,$floor,$level_number,$inventoryForm->inventory_id)) {
                $startMessage = 'Для ПРОДОЛЖЕНИЯ инвентаризации нажмите '."<span class='btn btn-warning' id='continue-inventory-bt'>Продолжить инвентаризацию</span>";
                $startMessage .= '  или начать инвентаризацию заново нажмите '."<span class='btn btn-danger' id='restart-inventory-bt' data-url='".Url::toRoute(['start','restart'=>'1'])."'>Начать инвентаризацию заново</span>";
            }

            if(!$inventoryForm->hasErrors()) {
                $success = 1;
            }

            return [
                'success'=> $success,
                'startMessage'=> $startMessage,
                'errors' => $inventoryForm->getErrors(),
            ];
        }

        return [
            'success'=>$success,
            'errors' => ActiveForm::validate($inventoryForm)
        ];
    }



    /*
     * Сканируем коробок
     * @return JSON
     * */
    public function actionPrimaryAddress()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inventoryForm = new InventoryForm();
        $inventoryForm->scenario = 'PrimaryAddress';

        $successMessages = [];
        $success = 0;
        $messageToFile = '';
        if ($inventoryForm->load(Yii::$app->request->post()) && $inventoryForm->validate()) {

            $minMax = EcommerceInventory::getMinMaxSecondaryAddress($inventoryForm->place_address_barcode);

            if(!EcommerceStock::find()->andWhere(['inventory_box_address_barcode'=>$inventoryForm->box_address_barcode,'place_address_barcode'=>$minMax,'status_inventory'=>EcommerceInventory::STATUS_SCAN_PROCESS,'inventory_id'=>$inventoryForm->inventory_id])->exists()) {
                $messageToFile = '  Этого короба нет в этом ряду или  Этого короба уже отсканирован'."\n";
                $inventoryForm->addError('inventoryform-box_address_barcode',$messageToFile.' '.'[ '.$inventoryForm->box_address_barcode.' ]');
            }

            if(!$inventoryForm->hasErrors()) {
                $success = 1;
            }

            if(!$success) {
                $specialMessageToFile = '';
                $productBarcodeToFile = $inventoryForm->product_barcode;
                $primaryAddressToFile = $inventoryForm->box_address_barcode;
                $secondaryAddressToFile = $inventoryForm->place_address_barcode;
                $contentToFile = $messageToFile . ";" . $productBarcodeToFile . ";" . $primaryAddressToFile . ";" . $specialMessageToFile . ";" . $secondaryAddressToFile . ";";
                file_put_contents(EcommerceInventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $inventoryForm->getErrors(),
                'countProductInBox' => EcommerceInventory::getCountProductInBox($inventoryForm->box_address_barcode,$minMax,$inventoryForm->inventory_id),
            ];
        }

        return [
            'success'=>$success,
            'errors' => ActiveForm::validate($inventoryForm)
        ];
    }

    /*
     * Сканируем товары
     * @return JSON
     * */
    public function actionProductBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inventoryForm = new InventoryForm();
        $inventoryForm->scenario = 'ProductBarcode';

        $successMessages = [];
        $success = 0;

        $messageToFile = '';
        $productBarcodeToFile = '';
        $primaryAddressToFile = '';
        $secondaryAddressToFile = '';
        $specialMessageToFile = '';
        $contentToFile = '';

        if ($inventoryForm->load(Yii::$app->request->post()) && $inventoryForm->validate()) {

            $minMax = EcommerceInventory::getMinMaxSecondaryAddress($inventoryForm->place_address_barcode);
            $productBarcode = $inventoryForm->product_barcode;
//            if(BarcodeManager::isReturnBoxBarcode($productBarcode) || BarcodeManager::isOneBoxOneProduct($productBarcode)) {
//            if(BarcodeManager::isBoxLotOrReturnBox($productBarcode,$minMax,$inventoryForm->box_address_barcode)) {
//                $productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBoxInventory($productBarcode);
//            }

            if($stock = EcommerceStock::find()->andWhere(['inventory_box_address_barcode'=>$inventoryForm->box_address_barcode,'product_barcode'=>$productBarcode,'place_address_barcode'=>$minMax,'status_inventory'=>EcommerceInventory::STATUS_SCAN_PROCESS,'inventory_id'=>$inventoryForm->inventory_id])->one()) {
                $stock->box_address_barcode = $inventoryForm->box_address_barcode;
                $stock->status_inventory = EcommerceInventory::STATUS_SCAN_YES;
                $stock->save(false);
            } elseif($stock = EcommerceStock::find()->andWhere(['inventory_box_address_barcode'=>$inventoryForm->box_address_barcode,'product_barcode'=>$productBarcode,'place_address_barcode'=>$minMax,'status_inventory'=>EcommerceInventory::STATUS_SCAN_YES,'inventory_id'=>$inventoryForm->inventory_id])->one()) {

                $messageToFile = 'Этот товар лишний';

                $inventoryForm->addError('inventoryform-product_barcode','Этот товар лишний  '.'[ '.$productBarcode.' ] '.' Короб: '.'[ '.$inventoryForm->box_address_barcode.' ]');
            } else {

                $stocks = EcommerceStock::find()->select('place_address_barcode, box_address_barcode, inventory_box_address_barcode')
                                ->andWhere([
                                    'product_barcode'=>$productBarcode,
                                    'status_inventory'=>EcommerceInventory::STATUS_SCAN_NO,
                                    'inventory_id'=>$inventoryForm->inventory_id])
                                ->groupBy('place_address_barcode, box_address_barcode')
                                ->all();
                $secondaryAddressMessage = '';

                if(!empty($stocks) && is_array($stocks)) {
                    $secondaryAddressMessage = "Этот товар найден в следующих адресах: ";
                    foreach($stocks as $stock) {
                        $inventoryAddressBoxBarcode = '';
                        if(!empty($stock->inventory_box_address_barcode)) {
                            $inventoryAddressBoxBarcode = ' [ '.$stock->inventory_box_address_barcode.' ] ';
                        }
                        $secondaryAddressMessage .= $stock->box_address_barcode.' '.$inventoryAddressBoxBarcode.' / '.$stock->place_address_barcode.', ';
                    }
                    $specialMessageToFile = $secondaryAddressMessage;
                }

                $messageToFile = 'Этот товар не из этого ряда';

                $inventoryForm->addError('inventoryform-product_barcode','Этот товар не из этого ряда'.'[ '.$productBarcode.' ] '.' Короб: '.'[ '.$inventoryForm->box_address_barcode.' ] '.$secondaryAddressMessage);
            }

            if(!$inventoryForm->hasErrors()) {
                $success = 1;
            }

            if(!$success) {
                $productBarcodeToFile = $productBarcode;
                $primaryAddressToFile = $inventoryForm->box_address_barcode;
                $secondaryAddressToFile = $inventoryForm->place_address_barcode;
                $contentToFile .= $messageToFile . ";" . $productBarcodeToFile . ";" . $primaryAddressToFile . ";" . $specialMessageToFile . ";" . $secondaryAddressToFile . ";";
                file_put_contents(EcommerceInventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $inventoryForm->getErrors(),
                'countProductInBox' => EcommerceInventory::getCountProductInBox($inventoryForm->box_address_barcode,$minMax,$inventoryForm->inventory_id),
            ];
        }

        return [
            'success'=>$success,
            'errors' => ActiveForm::validate($inventoryForm)
        ];
    }

    /*
    * Clear box
    * @return JSON
    * */
    public function actionClearBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $inventoryForm = new InventoryForm();
        $inventoryForm->scenario = 'ClearBox';

        $successMessages = [];
        $messageToFile = '';
        $success = 0;
        if ($inventoryForm->load(Yii::$app->request->post()) && $inventoryForm->validate()) {

            $minMax = EcommerceInventory::getMinMaxSecondaryAddress($inventoryForm->place_address_barcode);

            if(!EcommerceStock::find()->andWhere(['inventory_box_address_barcode'=>$inventoryForm->box_address_barcode,'place_address_barcode'=>$minMax,'status_inventory'=>[EcommerceInventory::STATUS_SCAN_PROCESS,EcommerceInventory::STATUS_SCAN_YES],'inventory_id'=>$inventoryForm->inventory_id])->exists()) {
                $messageToFile = 'Этого короба нет в этом ряду';
                $inventoryForm->addError('inventoryform-box_address_barcode','Этого короба нет в этом ряду '.'[ '.$inventoryForm->box_address_barcode.' ]');
            }



            if(!$inventoryForm->hasErrors()) {
                $success = 1;
                EcommerceStock::updateAll(['box_address_barcode'=>EcommerceInventory::INVENTORY_BARCODE,'status_inventory' => EcommerceInventory::STATUS_SCAN_PROCESS],['box_address_barcode'=>$inventoryForm->box_address_barcode,'place_address_barcode'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id,'status_inventory' =>EcommerceInventory::STATUS_SCAN_YES]);
            }

            if(!$success) {
                $specialMessageToFile = '';
                $productBarcodeToFile = $inventoryForm->product_barcode;
                $primaryAddressToFile = $inventoryForm->box_address_barcode;
                $secondaryAddressToFile = $inventoryForm->place_address_barcode;
                $contentToFile = $messageToFile . ";" . $productBarcodeToFile . ";" . $primaryAddressToFile . ";" . $specialMessageToFile . ";" . $secondaryAddressToFile . ";";
                file_put_contents(EcommerceInventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $inventoryForm->getErrors(),
                'countProductInBox' => EcommerceInventory::getCountProductInBox($inventoryForm->box_address_barcode,$minMax,$inventoryForm->inventory_id),
            ];
        }

        return [
            'success'=>$success,
            'errors' => ActiveForm::validate($inventoryForm)
        ];
    }

    /*
     *
     * */
    public function actionPrintDiffList()
    {
        $inventoryForm = new InventoryForm();
        $inventoryForm->scenario = 'PrintInventoryDiffList';
        $items = [];

        if ($inventoryForm->load(Yii::$app->request->post()) && $inventoryForm->validate()) {
            $minMax = EcommerceInventory::getMinMaxSecondaryAddress($inventoryForm->place_address_barcode);
            $items = EcommerceStock::find()
                ->select('count(product_barcode) as product_qty, product_barcode, product_model, place_address_barcode, inventory_box_address_barcode')
                ->andWhere(['place_address_barcode'=>$minMax,
                    'inventory_id'=>$inventoryForm->inventory_id,
                    'status_inventory'=>EcommerceInventory::STATUS_SCAN_PROCESS,
                    'status_availability'=>StockAvailability::YES
                ])
//                ->andWhere('status_inventory = :status_inventory',[':status_inventory'=>Inventory::STATUS_SCAN_PROCESS])
                ->groupBy('product_barcode, inventory_box_address_barcode, place_address_barcode')
                ->orderBy([
                    'address_sort_order'=>SORT_DESC,
//                    'place_address_barcode'=>SORT_DESC,
                ])
                ->asArray()
                ->all();
        }

        return $this->render('_print-diff-list-pdf', ['items' => $items]);
    }

    /*
    *
    * */
    public function actionPrintAcceptedList()
    {
        $inventoryForm = new InventoryForm();
        $inventoryForm->scenario = 'PrintInventoryDiffList';
        $items = [];

        if ($inventoryForm->load(Yii::$app->request->post()) && $inventoryForm->validate()) {
            $minMax = EcommerceInventory::getMinMaxSecondaryAddress($inventoryForm->place_address_barcode);
            $items = EcommerceStock::find()
                ->select('count(product_barcode) as product_qty, product_barcode, product_model, place_address_barcode, box_address_barcode')
                ->andWhere(['place_address_barcode'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])
//                ->andWhere('status_inventory = :status_inventory',[':status_inventory'=>Inventory::STATUS_SCAN_YES])
                ->andWhere('status_inventory = :status_inventory AND status_availability = :status_availability',[':status_inventory'=>EcommerceInventory::STATUS_SCAN_YES,':status_availability'=>StockAvailability::YES])
                ->groupBy('product_barcode, box_address_barcode, place_address_barcode')
                ->orderBy([
//                    'place_address_barcode'=>SORT_DESC,
                    'address_sort_order'=>SORT_DESC,
                ])
                ->asArray()
                ->all();
        }

        return $this->render('_print-accepted-list-pdf', ['items' => $items]);
    }
}