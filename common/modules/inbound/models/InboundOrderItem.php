<?php

namespace common\modules\inbound\models;

use common\modules\stock\models\Stock;
use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "inbound_order_items".
 *
 * @property integer $id
 * @property integer $inbound_order_id
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
 * @property string $product_size
 * @property string $product_brand
 * @property string $box_barcode
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $accepted_number_places_qty
 * @property integer $expected_number_places_qty
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class InboundOrderItem extends ActiveRecord
{
    /*
     * @var integer For scanned qty product in box
     *
     * */
    public $count_accepted_qty;
    public $order_by;

    /*
     * @var integer status
     *
     * */
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
        return 'inbound_order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inbound_order_id'], 'required'],
            [['inbound_order_id', 'product_id', 'status', 'expected_qty', 'accepted_qty', 'allocated_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['accepted_number_places_qty', 'expected_number_places_qty'], 'integer'],
            [['product_price'], 'number'],
            [['product_exporter', 'product_importer', 'product_description', 'product_serialize_data'], 'string'],
            [['product_name', 'product_model', 'product_sku', 'product_madein', 'product_composition'], 'string', 'max' => 128],
            [['product_barcode', 'box_barcode'], 'string', 'max' => 54],
			[['product_size', 'product_brand'], 'string', 'max' => 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('inbound/forms', 'ID'),
            'inbound_order_id' => Yii::t('inbound/forms', 'Inbound Order ID'),
            'product_id' => Yii::t('inbound/forms', 'Product ID'),
            'product_name' => Yii::t('inbound/forms', 'Product Name'),
            'product_barcode' => Yii::t('inbound/forms', 'Product Barcode'),
            'product_price' => Yii::t('inbound/forms', 'Product Price'),
            'product_model' => Yii::t('inbound/forms', 'Product Model'),
            'product_sku' => Yii::t('inbound/forms', 'Product Sku'),
            'product_madein' => Yii::t('inbound/forms', 'Product Madein'),
            'product_composition' => Yii::t('inbound/forms', 'Product Composition'),
            'product_exporter' => Yii::t('inbound/forms', 'Product Exporter'),
            'product_importer' => Yii::t('inbound/forms', 'Product Importer'),
            'product_description' => Yii::t('inbound/forms', 'Product Description'),
            'product_serialize_data' => Yii::t('inbound/forms', 'Product Serialize Data'),
            'box_barcode' => Yii::t('inbound/forms', 'Box Barcode'),
            'status' => Yii::t('inbound/forms', 'Status'),
            'expected_qty' => Yii::t('inbound/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('inbound/forms', 'Accepted Qty'),
            'allocated_qty' => Yii::t('inbound/forms', 'Allocated Qty'),
            'accepted_number_places_qty' => Yii::t('inbound/forms', 'Accepted Number Places Qty'),
            'expected_number_places_qty' => Yii::t('inbound/forms', 'Expected Number Places Qty'),
            'begin_datetime' => Yii::t('inbound/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('inbound/forms', 'End Datetime'),
            'created_user_id' => Yii::t('inbound/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('inbound/forms', 'Updated User ID'),
            'created_at' => Yii::t('inbound/forms', 'Created At'),
            'updated_at' => Yii::t('inbound/forms', 'Updated At'),
        ];
    }

    /*
     * Get count product in box
     * @param string $boxBarcode
     * @return integer
     *
     * */
    public static function getScannedProductInBox($boxBarcode,$inbound_order_id)
    {
        return (int)Stock::find()->where([
                                    'inbound_order_id'=>$inbound_order_id,
                                    'primary_address' => $boxBarcode,
                                    'status'=>
                                        [
                                            Stock::STATUS_INBOUND_SCANNED,
                                            Stock::STATUS_INBOUND_OVER_SCANNED,
                                        ]])->count();

    }

    /*
     * Clear box
     * @param string $boxBarcode
     * @return integer
     * */
//    public static function clearBox($boxBarcode,$inboundOrderId)
//    {
//        return InboundOrderItemProcess::updateAll([
//                'primary_address'=>'',
//            ],
//            [
//                'primary_address' => $boxBarcode,
//                'inbound_order_id' => $inboundOrderId,
//            ]
//        );
//        return InboundOrderItemProcess::deleteAll(
//            [
//                'box_barcode' => $boxBarcode,
//                'inbound_order_id' => $inboundOrderId,
//            ]
//        );
//    }
}
