<?php
namespace common\modules\returnOrder\models;

use Yii;

/**
 * This is the model class for table "return_tmp_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $from_point_id
 * @property string $from_point_client_id
 * @property integer $to_point_id
 * @property string $to_point_client_id
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property string $ttn
 * @property string $party_number
 * @property string $order_number
 * @property string $our_box_inbound_barcode
 * @property string $our_box_to_stock_barcode
 * @property string $client_box_barcode
 * @property string $primary_address
 * @property string $secondary_address
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ReturnTmpOrders extends \common\models\ActiveRecord
{
    public $qty;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_tmp_orders';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
//    public static function getDb()
//    {
//        return Yii::$app->get('dbDefactoSpecial');
//    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'from_point_id', 'to_point_id', 'status', 'expected_qty', 'accepted_qty', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['from_point_client_id', 'to_point_client_id', 'ttn', 'party_number', 'order_number'], 'string', 'max' => 128],
            [['our_box_inbound_barcode', 'our_box_to_stock_barcode', 'client_box_barcode'], 'string', 'max' => 16],
            [['primary_address', 'secondary_address'], 'string', 'max' => 28],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client'),
            'from_point_id' => Yii::t('app', 'Internal from point id '),
            'from_point_client_id' => Yii::t('app', 'Client from point id '),
            'to_point_id' => Yii::t('app', 'Internal from point id '),
            'to_point_client_id' => Yii::t('app', 'Client from point id '),
            'status' => Yii::t('app', 'Status'),
            'expected_qty' => Yii::t('app', 'Expected qty'),
            'accepted_qty' => Yii::t('app', 'Accepted qty'),
            'ttn' => Yii::t('app', 'Ttn number'),
            'party_number' => Yii::t('app', 'Party number'),
            'order_number' => Yii::t('app', 'Order number'),
            'our_box_inbound_barcode' => Yii::t('app', 'Our box inbound barcode'),
            'our_box_to_stock_barcode' => Yii::t('app', 'Our box to stock barcode'),
            'client_box_barcode' => Yii::t('app', 'Client box barcode'),
            'primary_address' => Yii::t('app', 'Primary address'),
            'secondary_address' => Yii::t('app', 'Secondary address'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @inheritdoc
     * @return ReturQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReturQuery(get_called_class());
    }
}
