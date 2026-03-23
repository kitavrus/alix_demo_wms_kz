<?php

namespace common\modules\returnOrder\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\client\models\Client;

/**
 * This is the model class for table "return_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $warehouse_id
 * @property string $order_number
 * @property integer $status
 * @property integer $expected_qty
 * @property integer $accepted_qty
 * @property integer $begin_datetime
 * @property integer $end_datetime
 * @property string $extra_fields
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ReturnOrder extends \common\models\ActiveRecord
{

    const STATUS_NEW = 1; // Новый
    const STATUS_IN_PROCESS = 2; // В процессе
    const STATUS_COMPLETE = 3;   // Выполнен
    const STATUS_SCANNED = 4; // Короб отсканирован
    const STATUS_SCANNED_OVER = 5; // Лишний короб

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'warehouse_id', 'status', 'expected_qty', 'accepted_qty', 'begin_datetime', 'end_datetime', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['extra_fields','order_number'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('return/forms', 'ID'),
            'client_id' => Yii::t('return/forms', 'Client ID'),
            'warehouse_id' => Yii::t('return/forms', 'Warehouse ID'),
            'order_number' => Yii::t('return/forms', 'Order Number'),
            'status' => Yii::t('return/forms', 'Status'),
            'expected_qty' => Yii::t('return/forms', 'Expected Qty'),
            'accepted_qty' => Yii::t('return/forms', 'Accepted Qty'),
            'begin_datetime' => Yii::t('return/forms', 'Begin Datetime'),
            'end_datetime' => Yii::t('return/forms', 'End Datetime'),
            'created_user_id' => Yii::t('return/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('return/forms', 'Updated User ID'),
            'created_at' => Yii::t('return/forms', 'Created At'),
            'updated_at' => Yii::t('return/forms', 'Updated At'),
            'deleted' => Yii::t('return/forms', 'Deleted'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return [
            self::STATUS_NEW => Yii::t('return/titles', 'New'),
            self::STATUS_IN_PROCESS=> Yii::t('return/titles', 'In process'),
            self::STATUS_COMPLETE => Yii::t('return/titles', 'Complete'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(), $status);
    }

    /*
   * Relation has many with ReturnOrderItem
   * */
    public function getOrderItems()
    {
        return $this->hasMany(ReturnOrderItems::className(), ['return_order_id' => 'id']);
    }

    /*
  * Relation has one with Client
  * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
}
