<?php

namespace common\modules\transportLogistics\models;
use common\modules\store\models\Store;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "tl_outbound_registry_items".
 *
 * @property integer $id
 * @property integer $tl_outbound_registry_id
 * @property integer $tl_delivery_proposal_id
 * @property string $weight
 * @property integer $route_from
 * @property integer $route_to
 * @property string $volume
 * @property integer $places
 * @property string $extra_fields
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlOutboundRegistryItems extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_outbound_registry_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_outbound_registry_id', 'tl_delivery_proposal_id', 'route_from', 'route_to', 'places', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['weight', 'volume'], 'number'],
            [['extra_fields'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'tl_outbound_registry_id' => Yii::t('transportLogistics/forms', 'Tl Outbound Registry ID'),
            'tl_delivery_proposal_id' => Yii::t('transportLogistics/forms', 'Tl Delivery Proposal ID'),
            'route_from' => Yii::t('transportLogistics/forms', 'Route From'),
            'route_to' => Yii::t('transportLogistics/forms', 'Route To'),
            'weight' => Yii::t('transportLogistics/forms', 'Weight'),
            'volume' => Yii::t('transportLogistics/forms', 'Volume'),
            'places' => Yii::t('transportLogistics/forms', 'Places'),
            'extra_fields' => Yii::t('transportLogistics/forms', 'Extra Fields'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
            'deleted' => Yii::t('transportLogistics/forms', 'Deleted'),
        ];
    }

    /*
     * Relation has One with TlOutboundTRegistry
     **/
    public function getRegistry()
    {
        return $this->hasOne(TlOutboundRegistry::className(), ['id' => 'tl_outbound_registry_id']);
    }

    /*
     * Relation has One with TlDeliveryProposal
     **/
    public function getProposal()
    {
        return $this->hasOne(TlDeliveryProposal::className(), ['id' => 'tl_delivery_proposal_id']);
    }

    /*
   * Relation has One with Store
   *
   * */
    public function getRouteFrom()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_from']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getRouteTo()
    {
        return $this->hasOne(Store::className(), ['id' => 'route_to']);
    }

    /*
    * Get value from extra filed
    * @param string Name field. Example: orders
    * @return string
    * */
    public function getExtraFieldValueByName($name)
    {
        $r = '';
        $extraField = (array)Json::decode($this->extra_fields);

        if(isset($extraField[$name])) {
            $r = $extraField[$name];
        }

        return $r;
    }


}
