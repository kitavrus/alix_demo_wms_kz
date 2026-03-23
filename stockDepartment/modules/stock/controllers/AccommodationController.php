<?php

namespace app\modules\stock\controllers;

use common\components\BarcodeManager;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\Stock;
//use common\modules\codebook\models\Codebook;
use Yii;
use stockDepartment\components\Controller;
use stockDepartment\modules\stock\models\AccommodationForm;
use yii\base\DynamicModel;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Response;
use app\modules\stock\models\StockSearch;
use common\modules\stock\service\ChangeAddressPlaceService;

class AccommodationController extends Controller
{
    /*
     *
     * */
    public function actionIndex()
    {
        return $this->render('index',[
            'af'=>new AccommodationForm(),
        ]);
    }

    /*
     * Move from to
     * @return JSON
     *
     * */
    public function actionMoveFromTo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new AccommodationForm();
        $successMessages = [];
        $success = 0;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->from = trim($model->from,'#');
            $model->to = trim($model->to,'#');

            file_put_contents('accommodation-all.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
            //S: Start
            switch($model->type) {
                case '1': // Короб на Полку
                    file_put_contents('accommodation-box-on.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
                    //From - Проверяем ШК что это короб
                    //To - Проверяем ШК что это полка
                    // TODO Попробовать сделать через DynamicModel

                    if( !BarcodeManager::isBox($model->from) && !empty($model->from) ) {
						$message = Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']';
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
                        $model->addError('accommodationform-from',$message);
                        //$model->addError('accommodationform-from',Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }
                    //Если в коробе нет товаров
                    if(BarcodeManager::isBox($model->from)) {
                        if (BarcodeManager::isEmptyBox($model->from)) {
							$message = Yii::t('stock/errors', 'Этот короб пуст') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']';
							ChangeAddressPlaceService::add($model->from, $model->to, $message);
							$model->addError('accommodationform-from', $message);
                            //$model->addError('accommodationform-from', Yii::t('stock/errors', 'Этот короб пуст') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']');
                        }
                    }

                    if( !BarcodeManager::isRegiment($model->to) && !empty($model->to)) {
						
						$message = Yii::t('stock/errors','This is not shelf').' ['.$model->to.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']';
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
                        $model->addError('accommodationform-to',$message);
						
                        //$model->addError('accommodationform-to',Yii::t('stock/errors','This is not shelf').' ['.$model->to.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    // Primary address  - Это Короб или палета
                    // Secondary address - Это полка или стелаж
                    if(!$model->hasErrors() && !empty($model->to) && !empty($model->from)) {
                        $address_sort_order = '';
                        if($address = RackAddress::find()->where(['address'=>$model->to])->one()) {
                            $address_sort_order = $address->sort_order;
                        }
                        Stock::updateAll(['secondary_address'=>$model->to,'address_sort_order'=>$address_sort_order],'primary_address = :pa',[':pa'=>$model->from]);
						
						$message = Yii::t('stock/messages', 'Successfully moved the box {from} shelf {to}',['from'=>$model->from,'to'=>$model->to]);
                        $successMessages[] = $message;
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
						
                        //$successMessages[] = Yii::t('stock/messages', 'Successfully moved the box {from} shelf {to}',['from'=>$model->from,'to'=>$model->to]);
                    }

                    break;

                case '2': // Из Короба в Короб
                    file_put_contents('accommodation-from-box-in-box.log',$model->type.";".$model->from.";".$model->to.";".(new \DateTime('now'))->format('Y-m-d H:i:s')."\n",FILE_APPEND);
                    // 2220003693377 700000056424
                    if( !BarcodeManager::isBox($model->from) && !empty($model->from) ) {
						
						$message = Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']';
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
                        $model->addError('accommodationform-from',$message);
						
                        //$model->addError('accommodationform-from',Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }
                    //Если в коробе нет товаров
                    if(BarcodeManager::isBox($model->from)) {
                        if (BarcodeManager::isEmptyBox($model->from)) {
							$message = Yii::t('stock/errors', 'Этот короб пуст') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']';
							ChangeAddressPlaceService::add($model->from, $model->to, $message);
							$model->addError('accommodationform-from', $message);
                            //$model->addError('accommodationform-from', Yii::t('stock/errors', 'Этот короб пуст') . ' [' . $model->from . '] ' . ' Перемещение из/в ' . ' [' . $model->from . ']  [' . $model->to . ']');
                        }
                    }

                    if( !BarcodeManager::isBox($model->to) && !empty($model->to) ) {
                        $message = Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']';
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
						$model->addError('accommodationform-to',$message);
						
						//$model->addError('accommodationform-to',Yii::t('stock/errors','This is not box').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    // Primary address  - Это Короб или палета
                    // Secondary address - Это полка или стелаж
                    if(!$model->hasErrors() && !empty($model->to) && !empty($model->from)) {
/*                        $address_sort_order = '';
                        if($address = RackAddress::find()->where(['address'=>$model->to])->one()) {
                            $address_sort_order = $address->sort_order;
                        }*/
//                        Stock::updateAll(['primary_address'=>$model->to],'primary_address = :sa',[':sa'=>$model->from]);

                        Stock::updateAll(['primary_address'=>$model->to],
                            [
                                'primary_address'=>$model->from,
                                'status'=>[
									Stock::STATUS_NOT_SET,
                                    Stock::STATUS_INBOUND_NEW,
                                    Stock::STATUS_INBOUND_CONFIRM,
                                    Stock::STATUS_OUTBOUND_NEW,
                                    Stock::STATUS_OUTBOUND_FULL_RESERVED,
                                    Stock::STATUS_OUTBOUND_RESERVING,
                                    Stock::STATUS_OUTBOUND_PART_RESERVED,

                                ]
                            ]);
//                        Stock::updateAll(['primary_address'=>$model->to,'address_sort_order'=>$address_sort_order],'primary_address = :sa',[':sa'=>$model->from]);

						$message = Yii::t('stock/messages', 'Успешно переместили из короба {from} в короб {to}',['from'=>$model->from,'to'=>$model->to]);
                        $successMessages[] = $message;
						ChangeAddressPlaceService::add($model->from, $model->to, $message);
                       // $successMessages[] = Yii::t('stock/messages', 'Успешно переместили из короба {from} в короб {to}',['from'=>$model->from,'to'=>$model->to]);
                    }

                    break;
/*
                case '3': // Палету на Стеллаж
                    if( !BarcodeManager::isPallet($model->from) && !empty($model->from)) {
                        $model->addError('accommodationform-from',Yii::t('stock/errors','This is not pallet').' ['.$model->from.'] '.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    if( !BarcodeManager::isRack($model->to) && !empty($model->to)) {
                        $model->addError('accommodationform-to',Yii::t('stock/errors','This is not rack').' ['.$model->to.']'.' Перемещение из/в '.' ['.$model->from.']  ['.$model->to.']');
                    }

                    // Primary address  - Это Короб или палета
                    // Secondary address - Это полка или стелаж
                    if(!$model->hasErrors() && !empty($model->to) && !empty($model->from)) {
                        $address_sort_order = '';
                        if($address = RackAddress::find()->where(['address'=>$model->to])->one()) {
                            $address_sort_order = $address->sort_order;
                        }
                        Stock::updateAll(['secondary_address'=>$model->to,'address_sort_order'=>$address_sort_order],'primary_address = :pa',[':pa'=>$model->from]);
                        $successMessages[] = Yii::t('stock/messages', 'Successfully moved the pallet {from} rack {to}',['from'=>$model->from,'to'=>$model->to]);
                    }

                    break;
                case '4': // Yii::t('stock/form','С палеты/короба содержимое на полку')
                    break;*/
            }
            //E: End
                if(!$model->hasErrors()) {
                    $success = 1;
                }

                return [
                    'success'=> $success,
                    'successMessages'=> $successMessages,
                    'errors' => $model->getErrors(),
                ];

        } else {
            return [
                'success'=>0,
                'errors' => ActiveForm::validate($model)
            ];
        }

        //из 6-06-00
        // в 2-9-15-2
    }

    public function actionUnallocatedBox()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere('secondary_address = "" OR secondary_address IS NULL')
                            ->andWhere('NOT primary_address=""')
                            ->groupBy('primary_address');

        return $this->render('index-unallocated', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
     * Print pick list
     *
     **/
    public function actionPrintUnallocatedList()
    {

        $idsData = Yii::$app->request->get('ids');
        $queryParams = Yii::$app->request->queryParams;


        $ids = [];
        if (!empty($idsData)) {
            $ids = explode(',', $idsData);
        } else if(!empty ($queryParams)){
            $searchModel = new StockSearch();
            $dataProvider = $searchModel->search($queryParams);
            $dataProvider->query->andWhere('secondary_address = "" OR secondary_address IS NULL')
                ->andWhere('NOT primary_address=""')
                ->groupBy('primary_address');
            $ids = ArrayHelper::map($dataProvider->query->asArray()->all(), 'id', 'id');
        }


        return $this->render('_print-unalloc-list-pdf', ['ids' => $ids]);
    }
}
