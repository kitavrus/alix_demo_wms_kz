<?php

namespace common\modules\transportLogistics\models;

use Yii;
use app\modules\transportLogistics\transportLogistics;
use common\models\ActiveRecord;

/**
 * This is the model class for table "tl_order_items".
 *
 * @property integer $id
 * @property integer $tl_order_id
 * @property string $box_barcode
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class TlOrderItems extends ActiveRecord
{
    /*
  * @var integer status
  * */
    const STATUS_ACTIVE = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /*
     * @var integer status
     *
     * */
    const STATUS_PRINTED_BOX_LABELS = 1; // распечатаны этикетки для коробов
    const STATUS_SCANNED = 2; // товар отсканировали в короб
    const STATUS_SHIPPED_COURIER = 3; // короба с товаром упакованы, этикетки на короба наклеены, и отгружены в курьерскую службу
    const STATUS_SHIPPED = 4; // Заказ доставлен в точку назначение и оператор на складе устанавливает дату доставку




    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_order_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['box_barcode'], 'string', 'max' => 54]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'tl_order_id' => Yii::t('transportLogistics/forms', 'Internal transport logistic order id'),
            'box_barcode' => Yii::t('transportLogistics/forms', 'Scanned box barcode'),
            'status' => Yii::t('transportLogistics/forms', 'Status new, scanned'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
        ];
    }


    /*
     * Find box By barcode
     * @param integer $order_id TlOrder
     * @param string $barcode
     * @return mix | Return model or null
     * */
    public static function findBoxByBarcode($order_id,$barcode)
    {
        return self::find()->where('tl_order_id = :tl_order_id AND box_barcode = :barcode',[':tl_order_id'=>$order_id,':barcode'=>$barcode])->one();
    }
}
