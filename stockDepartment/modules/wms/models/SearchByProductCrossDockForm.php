<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\wms\models;

use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use yii\base\Model;
use Yii;
use yii\helpers\VarDumper;


class SearchByProductCrossDockForm extends Model
{
    public $internal_barcode; // номер родительского заказа. сканируем из листа сборки
//    public $to_point; // 4-х значный код магазина
    public $product_barcode; // шк короба дефакто
    public $scanned_product_barcodes;
//    public $validate_type; //
//    public $client_id = 2; // id клиента дефакто (2)

    /*
    *
    *
    * */
    public function rules()
    {
        return [
            [['internal_barcode', 'product_barcode','scanned_product_barcodes'], 'string'],
            [['internal_barcode', 'product_barcode','scanned_product_barcodes'], 'trim'],

            [['internal_barcode'], 'validateInternalBarcode', 'on' => 'validateInternalBarcode'],
            [['internal_barcode'], 'required', 'on' => 'validateInternalBarcode'],

            [['product_barcode'], 'validateProductBarcode', 'on' => 'validateProductBarcode'],
            [['product_barcode'], 'required', 'on' => 'validateProductBarcode'],
        ];
    }

    /*
    * Validate cross dock internal barcode
    * */
    public function validateInternalBarcode($attribute, $params)
    {
        $barcode = $this->internal_barcode;
        if ($barcode) {
            $dcExist = CrossDock::find()->andWhere(['internal_barcode' => $barcode, 'status' => [
                Stock::STATUS_CROSS_DOCK_PRINTED_PICKING_LIST,
                Stock::STATUS_CROSS_DOCK_SCANNING,
                Stock::STATUS_CROSS_DOCK_SCANNED,
            ]])->exists();
            if (!$dcExist) {
                $this->addError('searchbyproductcrossdockform-internal_barcode', Yii::t('outbound/errors', 'Вы ввели штрих-код еще не существующего или не распечатанного сборочного листа'));
            }
        }
    }

    /*
    * Validate cross dock product barcode
    * */
    public function validateProductBarcode($attribute, $params)
    {
        $barcode = $this->internal_barcode;
        $product_barcode = $this->product_barcode;

/*        if ($storeCode && $barcode && $box_barcode) {
            $storeId = Store::find()->select('id')->andWhere(['client_id' => $this->client_id, 'type_use' => Store::TYPE_USE_STORE, 'shop_code' => $storeCode])->scalar();
            if (!empty($storeId)) {
                if ($cdId = CrossDock::find()->select('id')->andWhere(['internal_barcode' => $barcode, 'to_point_id' => $storeId])->scalar()) {
                    if ($cdItem = CrossDockItems::find()->andWhere(['cross_dock_id' => $cdId, 'box_barcode' => $box_barcode])->andWhere('status != :status', [':status' => Stock::STATUS_CROSS_DOCK_SCANNED])->one()) {
                    } else {
                        $this->addError('outboundcrossdockform-box_barcode', Yii::t('outbound/errors', 'Этого этого короба нет в магазине или он лишний') . ' [' . $box_barcode . ']');
                    }
                } else {
                }
            } else {
            }
        }*/
    }

    /*
     *
     * */
    public function attributeLabels()
    {
        return [
            'internal_barcode' => Yii::t('outbound/forms', 'Шк лист сборки'),
//            'to_point' => Yii::t('outbound/forms', 'Номер магазина'),
//            'box_barcode' => Yii::t('outbound/forms', 'Шк короба'),
            'product_barcode' => Yii::t('outbound/forms', 'Шк товара'),
        ];
    }
}