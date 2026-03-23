<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models\defacto;

use common\components\BarcodeManager;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
//use common\modules\inbound\models\InboundOrderItemProcess;
use common\modules\stock\models\Stock;
use yii\base\Model;
use Yii;

class InboundExtraPlusForm extends InboundForm {

    /*
     * Validate product_barcode
     * */
    public function validateProductBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if(!BarcodeManager::isDefactoProduct($value)) {
            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Этот не штрих код товара'));
        }

//        $inboundOrderID = $this->order_number;
//        $productBarcode = $this->product_barcode;
//
//        $extraQty = InboundOrderItem::find()->andWhere([
//            'inbound_order_id' => $inboundOrderID,
//            'product_barcode' => $productBarcode,
//        ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();
//
//        if($extraQty) {
//            $this->addError($attribute, '<b>['.$value.']</b> '.Yii::t('inbound/errors','Это лишний товар в накладной'));
//        }
    }


}