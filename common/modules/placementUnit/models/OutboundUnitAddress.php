<?php

namespace common\modules\placementUnit\models;

use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "outbound_unit_address".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $warehouse_id
 * @property integer $zone_id
 * @property integer $outbound_order_id
 * @property integer $code_book_id
 * @property string $from_rack_address
 * @property string $from_pallet_address
 * @property string $from_box_address
 * @property string $our_barcode
 * @property string $client_barcode
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class OutboundUnitAddress extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_unit_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'warehouse_id', 'zone_id', 'outbound_order_id', 'code_book_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['from_rack_address', 'from_pallet_address', 'from_box_address', 'our_barcode', 'client_barcode'], 'string', 'max' => 23],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client id'),
            'warehouse_id' => Yii::t('app', 'Warehouse id'),
            'zone_id' => Yii::t('app', 'Zone id'),
            'outbound_order_id' => Yii::t('app', 'Outbound order id'),
            'code_book_id' => Yii::t('app', 'Code book id'),
            'from_rack_address' => Yii::t('app', 'From rack address barcode'),
            'from_pallet_address' => Yii::t('app', 'From pallet address barcode'),
            'from_box_address' => Yii::t('app', 'From box address barcode'),
            'our_barcode' => Yii::t('app', 'Our Unit barcode'),
            'client_barcode' => Yii::t('app', 'Client Unit barcode'),
            'status' => Yii::t('app', 'Status:'),
            'created_user_id' => Yii::t('app', 'Created user id'),
            'updated_user_id' => Yii::t('app', 'Updated user id'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}