<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\outbound\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\employees\models\Employees;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;
use yii\helpers\VarDumper;


class AllocationListForm extends Model {


    public $box_barcode;
    public $employee_barcode;
    public $client_id;

    /*
    * Validate box barcode
    * */
    public function validateBarcode($attribute, $params)
    {
        if($barcode = $this->box_barcode){
            $boxBarcode = ltrim($barcode,'k');
            $boxBarcode = ltrim($boxBarcode,'K');

            $order = InboundOrder::find()->andWhere(['client_box_barcode'=>$boxBarcode,'client_id' => $this->client_id])->one();
            if(!$order){
                $this->addError($attribute, Yii::t('outbound/errors','Вы ввели штрих-код несуществующего короба') );
            }
//            elseif($order->status == Stock::STATUS_INBOUND_SORTED) {
//                $this->addError($attribute, Yii::t('outbound/errors','Вы ввели штрих-код уже отсортированного короба') );
//            }

            // Проверяем есть ли распределение на короб
            $flag = 0;
            if(!empty($order) && $order->status == Stock::STATUS_INBOUND_NEW) {
                $productBarcodes = InboundOrderItem::find()
                    ->select('product_barcode, expected_qty')
                    ->where(['inbound_order_id' => $order->id])
                    ->asArray()
                    ->all();

                // find TIR
                $outboundIds = OutboundOrder::find()
                    ->select('id')
                    ->where([
                        'client_id' => $this->client_id,
                        'parent_order_number' => $order->parent_order_number, //TODO Как это лучше сделать????
                        'status' => [
                            Stock::STATUS_OUTBOUND_NEW,
                            Stock::STATUS_OUTBOUND_SCANNING,
                        ]
                    ])
                    ->orderBy('to_point_id ASC')
                    ->column();

//                VarDumper::dump($outboundIds,10,true);
                foreach($productBarcodes as $item) {
                    $outboundOrderItemExist = OutboundOrderItem::find()
                        ->where(['product_barcode' => $item['product_barcode'],'outbound_order_id'=>$outboundIds]) //
                        ->andWhere('expected_qty != allocated_qty')
                        ->orderBy('expected_qty DESC')
                        ->exists();

//                    VarDumper::dump($outboundOrderItemExist,10,true);
                    if($outboundOrderItemExist) {
                        $flag++;
                    }
                }

                if(!$flag) {
                    // S: TODO Переделать нужно перенести в контроллер
//                    $order->status = Stock::STATUS_INBOUND_SORTING;
//                    $order->detachBehavior('auditBehavior');
//                    $order->save(false);
                    // E: TODO Переделать
                    $this->addError($attribute, Yii::t('outbound/errors','НА ЭТОТ КОРОБ НЕТ РАСПРЕДЕЛЕНИЯ, ОТЛОЖИТЕ ЕГО') );
                }
            }
        }
    }

    /*
     *
     * */
    public function attributeLabels()
    {
        return [
            'box_barcode' => Yii::t('inbound/forms', 'Box barcode'),
        ];
    }

    /*
     *
     *
     * */
    public function rules()
    {
        return [
            [['box_barcode'], 'required'],
            [['box_barcode'], 'validateBarcode'],
            [['box_barcode','employee_barcode'], 'string'],
            [['client_id'], 'integer'],
            [['box_barcode','employee_barcode','client_id'], 'trim'],
        ];
    }
}