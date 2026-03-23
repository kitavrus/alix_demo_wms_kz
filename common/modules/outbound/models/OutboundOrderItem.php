<?php

namespace common\modules\outbound\models;

use common\modules\stock\models\Stock;
use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "outbound_order_items".
 *
 * @property integer $id
 * @property integer $outbound_order_id
 * @property integer $product_id
 * @property string $product_name
 * @property string $product_barcode
 * @property string $product_price
 * @property string $product_model
 * @property string $product_sku
 * @property string $product_madein
 * @property string $product_composition
 * @property string $product_exporter
 * @property string $product_importer
 * @property string $product_description
 * @property string $product_serialize_data
 * @property string $box_barcode
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $allocated_qty
 * @property integer $expected_number_places_qty
 * @property integer $accepted_number_places_qty
 * @property integer $allocated_number_places_qty
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class OutboundOrderItem extends ActiveRecord
{
    public $order_by;
//    const STATUS_NEW = 1; // новый товар еще не отсканирован в короб
//    const STATUS_SCANNED = 2; // товар отсканировали в короб
//    const STATUS_PRINTED_PRODUCT_LABELS = 3; // для отсканированных товаров в короб распечатаны ценники
//    const STATUS_PRINTED_BOX_LABELS = 4; // распечатаны этикетки для коробов
//    const STATUS_PACKED = 5; // после того, как этикетки на короба распечатаны, товары переходят в статус упакован
//    const STATUS_SHIPPED_COURIER = 6; // короба с товаром упакованы, этикетки на короба наклеены, и отгружены в курьерскую службу

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outbound_order_id'], 'required'],
            [['allocated_number_places_qty','accepted_number_places_qty','expected_number_places_qty','allocated_qty','outbound_order_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['product_price'], 'number'],
            [['product_exporter', 'product_importer', 'product_description', 'product_serialize_data'], 'string'],
            [['product_name', 'product_model', 'product_sku', 'product_madein', 'product_composition'], 'string', 'max' => 128],
            [['product_barcode', 'box_barcode'], 'string', 'max' => 54]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        // 'allocate_number_places_qty','accepted_number_places_qty','expected_number_places_qty','allocate_qty'
        return [
            'id' => Yii::t('outbound/forms', 'ID'),
            'outbound_order_id' => Yii::t('outbound/forms', 'Outbound Order ID'),
            'product_id' => Yii::t('outbound/forms', 'Product ID'),
            'product_name' => Yii::t('outbound/forms', 'Product Name'),
            'product_barcode' => Yii::t('outbound/forms', 'Product Barcode'),
            'product_price' => Yii::t('outbound/forms', 'Product Price'),
            'product_model' => Yii::t('outbound/forms', 'Product Model'),
            'product_sku' => Yii::t('outbound/forms', 'Product Sku'),
            'product_madein' => Yii::t('outbound/forms', 'Product Madein'),
            'product_composition' => Yii::t('outbound/forms', 'Product Composition'),
            'product_exporter' => Yii::t('outbound/forms', 'Product Exporter'),
            'product_importer' => Yii::t('outbound/forms', 'Product Importer'),
            'product_description' => Yii::t('outbound/forms', 'Product Description'),
            'product_serialize_data' => Yii::t('outbound/forms', 'Product Serialize Data'),
            'box_barcode' => Yii::t('outbound/forms', 'Box Barcode'),
            'status' => Yii::t('outbound/forms', 'Status'),
            'expected_qty' => Yii::t('outbound/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('outbound/forms', 'Accepted Qty'),
            'allocated_qty' => Yii::t('outbound/forms', 'Allocated Qty'),
            'expected_number_places_qty' => Yii::t('outbound/forms', 'Expected number places Qty'),
            'accepted_number_places_qty' => Yii::t('outbound/forms', 'Accepted number places Qty'),
            'allocated_number_places_qty' => Yii::t('outbound/forms', 'Allocated number places Qty'),
            'begin_datetime' => Yii::t('outbound/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('outbound/forms', 'End Datetime'),
            'created_user_id' => Yii::t('outbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('outbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('outbound/forms', 'Created At'),
            'updated_at' => Yii::t('outbound/forms', 'Updated At'),
            'deleted' => Yii::t('outbound/forms', 'Deleted'),
        ];
    }

    /*
       * Relation has One with outbound Order
       *
       * */
    public function getOutboundOrder()
    {
        return $this->hasOne(OutboundOrder::className(), ['id' => 'outbound_order_id']);
    }

    /*
    * Get grid row color, depend on
    * record status
    * @return string
    **/
    public function getGridColor(){

        switch($this->status) {
            case Stock::STATUS_OUTBOUND_FULL_RESERVED: //#FFA54F
                $class = 'color-tan';
                break;
            case Stock::STATUS_OUTBOUND_PART_RESERVED: //#FFA500
                $class = 'color-orange';
                break;
            case Stock::STATUS_OUTBOUND_PICKING: //#FFF68F
                $class = 'color-khaki';
                break;
            case Stock::STATUS_OUTBOUND_PICKED: //#CAFF70
                $class = 'color-dark-olive-green';
                break;
            case Stock::STATUS_OUTBOUND_SCANNING: //#87CEFA
                $class = 'color-light-sky-blue';
                break;
            case Stock::STATUS_OUTBOUND_SCANNED: //#1E90FF
                $class = 'color-dodger-blue';
                break;
            case Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST: //#FFFFE0
                $class = 'color-light-yellow';
                break;
            case Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API: //#EE82EE
                $class = 'color-violet ';
                break;
            case Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL: //#FF6A6A
            case Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL: //#FF6A6A
                $class = 'color-indian-red';
                break;
            case Stock::STATUS_OUTBOUND_COMPLETE: //#C6E2FF
                $class = 'color-slate-gray';
                break;
            default:
                $class = '';
                break;

        }
        return $class;
    }
}
