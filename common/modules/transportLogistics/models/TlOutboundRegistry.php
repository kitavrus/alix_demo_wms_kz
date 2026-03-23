<?php

namespace common\modules\transportLogistics\models;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlCars;
use Yii;

/**
 * This is the model class for table "tl_outbound_registry".
 *
 * @property integer $id
 * @property integer $agent_id
 * @property integer $car_id
 * @property string $driver_name
 * @property string $driver_phone
 * @property string $driver_auto_number
 * @property string $weight
 * @property string $volume
 * @property integer $places
 * @property string $extra_fields
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlOutboundRegistry extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_outbound_registry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agent_id', 'car_id', 'places', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['weight', 'volume', 'price_invoice', 'price_invoice_with_vat'], 'number'],
            [['extra_fields'], 'string'],
            [['driver_name', 'driver_phone', 'driver_auto_number'], 'string', 'max' => 255],
            [['agent_id', 'car_id', ], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'agent_id' => Yii::t('transportLogistics/forms', 'Agent ID'),
            'car_id' => Yii::t('transportLogistics/forms', 'Car ID'),
            'driver_name' => Yii::t('transportLogistics/forms', 'Driver Name'),
            'driver_phone' => Yii::t('transportLogistics/forms', 'Driver Phone'),
            'driver_auto_number' => Yii::t('transportLogistics/forms', 'Driver Auto Number'),
            'weight' => Yii::t('transportLogistics/forms', 'Weight'),
            'volume' => Yii::t('transportLogistics/forms', 'Volume'),
            'places' => Yii::t('transportLogistics/forms', 'Places'),
            'price_invoice' => Yii::t('transportLogistics/forms', 'Price Invoice'),
            'price_invoice_with_vat' => Yii::t('transportLogistics/forms', 'Price Invoice With Vat'),
            'extra_fields' => Yii::t('transportLogistics/forms', 'Extra Fields'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
            'deleted' => Yii::t('transportLogistics/forms', 'Deleted'),
        ];
    }

    /*
   * Relation has many with Items
   * */
    public function getRegistryItems()
    {
        return $this->hasMany(TlOutboundRegistryItems::className(), ['tl_outbound_registry_id' => 'id']);
    }

    /*
     * Relation has One with Car
     **/
    public function getCar()
    {
        return $this->hasOne(TlCars::className(), ['id' => 'car_id']);
    }

    /*
     * Relation has One with Agent
     **/
    public function getAgent()
    {
        return $this->hasOne(TlAgents::className(), ['id' => 'agent_id']);
    }

    /*
     * Relation has One with Agent
     **/
    public function canPrintTtn()
    {
        $flag = true;
        if($items = $this->registryItems){
            foreach($items as $item){
                if($dp = $item->proposal){
                    if($dp->status == TlDeliveryProposal::STATUS_DELIVERED ||
                        $dp->status == TlDeliveryProposal::STATUS_DONE ||
                        $dp->status == TlDeliveryProposal::STATUS_ON_ROUTE
                    ){
                        $flag=false;
                    }
                }
            }
        }
        return $flag;
    }
}
