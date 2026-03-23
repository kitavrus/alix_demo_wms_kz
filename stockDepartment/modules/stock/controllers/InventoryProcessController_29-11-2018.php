<?php

namespace app\modules\stock\controllers;

use common\components\BarcodeManager;
use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\InventoryRows;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\Stock;
//use common\modules\codebook\models\Codebook;
use stockDepartment\modules\stock\models\InventoryForm;
use Yii;
use stockDepartment\components\Controller;
use stockDepartment\modules\stock\models\AccommodationForm;
use yii\base\DynamicModel;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Response;
use app\modules\stock\models\StockSearch;

use common\modules\stock\models\Inventory;
use app\modules\stock\models\InventorySearch;
//use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

            $minMax = Inventory::getMinMaxSecondaryAddress($inventoryForm->secondary_address);
            // обнуляем короба
            $stockAll = Stock::find()->andWhere(['secondary_address'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])->all();

            if(!empty($stockAll)) {
                if (is_null($restart)) {
                    foreach ($stockAll as $stock) {
                        if($stock->primary_address != Inventory::INVENTORY_BARCODE) {
                            $stock->inventory_primary_address = $stock->primary_address;
                            $stock->inventory_secondary_address = $stock->secondary_address;
                            $stock->save(false);
                        }
                    }
                }

                $stockRowQty = Stock::find()->andWhere(['secondary_address'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])->count();
                // Выбираем ожидаемое кол-во коробов
//                $stockRowBoxQty = Stock::find()->andWhere(['secondary_address'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])->groupBy('primary_address')->count();

                $column_number = Inventory::getRowNumber($inventoryForm->secondary_address);
                $floor_number = Inventory::getFloorNumber($inventoryForm->secondary_address);
                $level_number = Inventory::getLevelNumber($inventoryForm->secondary_address);

                $conditionWhere = [
                                    'inventory_id'=>$inventoryForm->inventory_id,
                                    'floor_number'=>$floor_number,
                                    'column_number'=>$column_number,
//                                    'level_number'=>$level_number
                ];

                if (!$invRow = InventoryRows::find()->andWhere($conditionWhere)->one()) {
                    $invRow = new InventoryRows();
                    $invRow->inventory_id = $inventoryForm->inventory_id;
                    $invRow->status = Inventory::STATUS_IN_PROCESS;
                    $invRow->column_number = $column_number;
                    $invRow->floor_number = $floor_number;
                    $invRow->level_number = $level_number;
                    $invRow->row_number = $inventoryForm->secondary_address;
//                    $invRow->expected_places_qty = $stockRowBoxQty;
                }

                $invRow->expected_qty = $stockRowQty;
                $invRow->save(false);

                Stock::updateAll([
                    'primary_address'=>Inventory::INVENTORY_BARCODE,
                    'status_inventory'=>Inventory::STATUS_SCAN_PROCESS
                ],
                    [
                        'secondary_address'=>$minMax,
                        'inventory_id'=>$inventoryForm->inventory_id
                    ]
                );
            }

            if(!$inventoryForm->hasErrors()) {
                $success = 1;
                $contentToFile = "Message; Product barcode; Primary address; Special Message; Secondary address"."\n";
                file_put_contents(Inventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
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
            $row = Inventory::getRowNumber($inventoryForm->secondary_address);
            $floor = Inventory::getFloorNumber($inventoryForm->secondary_address);
            $level_number = Inventory::getLevelNumber($inventoryForm->secondary_address);

            if(Inventory::checkStart($row,$floor,$level_number,$inventoryForm->inventory_id)) {
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

            $minMax = Inventory::getMinMaxSecondaryAddress($inventoryForm->secondary_address);

            if(!Stock::find()->andWhere(['inventory_primary_address'=>$inventoryForm->primary_address,'secondary_address'=>$minMax,'status_inventory'=>Inventory::STATUS_SCAN_PROCESS,'inventory_id'=>$inventoryForm->inventory_id])->exists()) {
                $messageToFile = '  Этого короба нет в этом ряду или  Этого короба уже отсканирован'."\n";
                $inventoryForm->addError('inventoryform-primary_address',$messageToFile.' '.'[ '.$inventoryForm->primary_address.' ]');
            }

            if(!$inventoryForm->hasErrors()) {
                $success = 1;
            }

            if(!$success) {
                $specialMessageToFile = '';
                $productBarcodeToFile = $inventoryForm->product_barcode;
                $primaryAddressToFile = $inventoryForm->primary_address;
                $secondaryAddressToFile = $inventoryForm->secondary_address;
                $contentToFile = $messageToFile . ";" . $productBarcodeToFile . ";" . $primaryAddressToFile . ";" . $specialMessageToFile . ";" . $secondaryAddressToFile . ";";
                file_put_contents(Inventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $inventoryForm->getErrors(),
                'countProductInBox' => Inventory::getCountProductInBox($inventoryForm->primary_address,$minMax,$inventoryForm->inventory_id),
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

            $minMax = Inventory::getMinMaxSecondaryAddress($inventoryForm->secondary_address);
            $productBarcode = $inventoryForm->product_barcode;
//            if(BarcodeManager::isReturnBoxBarcode($productBarcode) || BarcodeManager::isOneBoxOneProduct($productBarcode)) {
            if(BarcodeManager::isBoxLorOrReturnBox($productBarcode)) {
                $productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBoxInventory($productBarcode);
            }

            if($stock = Stock::find()->andWhere(['inventory_primary_address'=>$inventoryForm->primary_address,'product_barcode'=>$productBarcode,'secondary_address'=>$minMax,'status_inventory'=>Inventory::STATUS_SCAN_PROCESS,'inventory_id'=>$inventoryForm->inventory_id])->one()) {
                $stock->primary_address = $inventoryForm->primary_address;
                $stock->status_inventory = Inventory::STATUS_SCAN_YES;
                $stock->save(false);
            } elseif($stock = Stock::find()->andWhere(['inventory_primary_address'=>$inventoryForm->primary_address,'product_barcode'=>$productBarcode,'secondary_address'=>$minMax,'status_inventory'=>Inventory::STATUS_SCAN_YES,'inventory_id'=>$inventoryForm->inventory_id])->one()) {

                $messageToFile = 'Этот товар лишний';

                $inventoryForm->addError('inventoryform-product_barcode','Этот товар лишний  '.'[ '.$productBarcode.' ] '.' Короб: '.'[ '.$inventoryForm->primary_address.' ]');
            } else {

                $stocks = Stock::find()->select('secondary_address, primary_address, inventory_primary_address')
                                ->andWhere([
                                    'product_barcode'=>$productBarcode,
                                    'status_inventory'=>Inventory::STATUS_SCAN_NO,
                                    'inventory_id'=>$inventoryForm->inventory_id])
                                ->groupBy('secondary_address, primary_address')
                                ->all();
                $secondaryAddressMessage = '';

                if(!empty($stocks) && is_array($stocks)) {
                    $secondaryAddressMessage = "Этот товар найден в следующих адресах: ";
                    foreach($stocks as $stock) {
                        $inventoryAddressBoxBarcode = '';
                        if(!empty($stock->inventory_primary_address)) {
                            $inventoryAddressBoxBarcode = ' [ '.$stock->inventory_primary_address.' ] ';
                        }
                        $secondaryAddressMessage .= $stock->primary_address.' '.$inventoryAddressBoxBarcode.' / '.$stock->secondary_address.', ';
                    }
                    $specialMessageToFile = $secondaryAddressMessage;
                }

                $messageToFile = 'Этот товар не из этого ряда';

                $inventoryForm->addError('inventoryform-product_barcode','Этот товар не из этого ряда'.'[ '.$productBarcode.' ] '.' Короб: '.'[ '.$inventoryForm->primary_address.' ] '.$secondaryAddressMessage);
            }

            if(!$inventoryForm->hasErrors()) {
                $success = 1;
            }

            if(!$success) {
                $productBarcodeToFile = $productBarcode;
                $primaryAddressToFile = $inventoryForm->primary_address;
                $secondaryAddressToFile = $inventoryForm->secondary_address;
                $contentToFile .= $messageToFile . ";" . $productBarcodeToFile . ";" . $primaryAddressToFile . ";" . $specialMessageToFile . ";" . $secondaryAddressToFile . ";";
                file_put_contents(Inventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $inventoryForm->getErrors(),
                'countProductInBox' => Inventory::getCountProductInBox($inventoryForm->primary_address,$minMax,$inventoryForm->inventory_id),
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

            $minMax = Inventory::getMinMaxSecondaryAddress($inventoryForm->secondary_address);

            if(!Stock::find()->andWhere(['inventory_primary_address'=>$inventoryForm->primary_address,'secondary_address'=>$minMax,'status_inventory'=>[Inventory::STATUS_SCAN_PROCESS,Inventory::STATUS_SCAN_YES],'inventory_id'=>$inventoryForm->inventory_id])->exists()) {
                $messageToFile = 'Этого короба нет в этом ряду';
                $inventoryForm->addError('inventoryform-primary_address','Этого короба нет в этом ряду '.'[ '.$inventoryForm->primary_address.' ]');
            }



            if(!$inventoryForm->hasErrors()) {
                $success = 1;
                Stock::updateAll(['primary_address'=>Inventory::INVENTORY_BARCODE,'status_inventory' => Inventory::STATUS_SCAN_PROCESS],['primary_address'=>$inventoryForm->primary_address,'secondary_address'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id,'status_inventory' =>Inventory::STATUS_SCAN_YES]);
            }

            if(!$success) {
                $specialMessageToFile = '';
                $productBarcodeToFile = $inventoryForm->product_barcode;
                $primaryAddressToFile = $inventoryForm->primary_address;
                $secondaryAddressToFile = $inventoryForm->secondary_address;
                $contentToFile = $messageToFile . ";" . $productBarcodeToFile . ";" . $primaryAddressToFile . ";" . $specialMessageToFile . ";" . $secondaryAddressToFile . ";";
                file_put_contents(Inventory::INVENTORY_FILE_NAME_ERROR, $contentToFile."\n", FILE_APPEND);
            }

            return [
                'success'=> $success,
                'successMessages'=> $successMessages,
                'errors' => $inventoryForm->getErrors(),
                'countProductInBox' => Inventory::getCountProductInBox($inventoryForm->primary_address,$minMax,$inventoryForm->inventory_id),
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
            $minMax = Inventory::getMinMaxSecondaryAddress($inventoryForm->secondary_address);
            $items = Stock::find()
                ->select('count(product_barcode) as product_qty, product_barcode, product_model, secondary_address, inventory_primary_address')
                ->andWhere(['secondary_address'=>$minMax,
                    'inventory_id'=>$inventoryForm->inventory_id,
                    'status_inventory'=>Inventory::STATUS_SCAN_PROCESS,
                    'status_availability'=>Stock::STATUS_AVAILABILITY_YES
                ])
//                ->andWhere('status_inventory = :status_inventory',[':status_inventory'=>Inventory::STATUS_SCAN_PROCESS])
                ->groupBy('product_barcode, inventory_primary_address, secondary_address')
                ->orderBy([
                    'address_sort_order'=>SORT_DESC,
//                    'secondary_address'=>SORT_DESC,
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
            $minMax = Inventory::getMinMaxSecondaryAddress($inventoryForm->secondary_address);
            $items = Stock::find()
                ->select('count(product_barcode) as product_qty, product_barcode, product_model, secondary_address, primary_address')
                ->andWhere(['secondary_address'=>$minMax,'inventory_id'=>$inventoryForm->inventory_id])
//                ->andWhere('status_inventory = :status_inventory',[':status_inventory'=>Inventory::STATUS_SCAN_YES])
                ->andWhere('status_inventory = :status_inventory AND status_availability = :status_availability',[':status_inventory'=>Inventory::STATUS_SCAN_YES,':status_availability'=>Stock::STATUS_AVAILABILITY_YES])
                ->groupBy('product_barcode, primary_address, secondary_address')
                ->orderBy([
//                    'secondary_address'=>SORT_DESC,
                    'address_sort_order'=>SORT_DESC,
                ])
                ->asArray()
                ->all();
        }

        return $this->render('_print-accepted-list-pdf', ['items' => $items]);
    }

}