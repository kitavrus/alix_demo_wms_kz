<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\entities;

use Yii;

/**
 * This is the model class for table "ecommerce_inbound".
 *
 * @property int $id
 * @property int $client_id Client id
 * @property string $party_number Party number
 * @property string $order_number Order number
 * @property int $expected_box_qty Expected box qty
 * @property int $accepted_box_qty Accepted box qty
 * @property int $expected_lot_qty Expected lot qty
 * @property int $accepted_lot_qty Accepted lot qty
 * @property int $expected_product_qty Expected product qty
 * @property int $accepted_product_qty Accepted product qty
 * @property int $status Status
 * @property int $begin_datetime Begin scanning datetime
 * @property int $end_datetime End scanning datetime
 * @property int $date_confirm End scanning datetime
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class EcommerceInbound extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ecommerce_inbound';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_confirm','client_id', 'expected_box_qty', 'accepted_box_qty', 'expected_lot_qty', 'accepted_lot_qty', 'expected_product_qty', 'accepted_product_qty', 'status', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['party_number', 'order_number'], 'string', 'max' => 36],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'party_number' => 'Party Number',
            'order_number' => 'Order Number',
            'expected_box_qty' => 'Ожидали кол-во коробов',
            'accepted_box_qty' => 'Приняли кол-во коробов',
            'expected_lot_qty' => 'Expected Lot Qty',
            'accepted_lot_qty' => 'Accepted Lot Qty',
            'expected_product_qty' => 'Ожидали кол-во товаров',
            'accepted_product_qty' => 'Приняли кол-во товаров',
            'status' => 'Status',
            'begin_datetime' => 'Begin Datetime',
            'end_datetime' => 'End Datetime',
            'date_confirm' => 'Date confirm',
            'created_user_id' => 'Created User ID',
            'updated_user_id' => 'Updated User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
        ];
    }
}
