<?php

namespace common\modules\transportLogistics\models;
use common\models\ActiveRecord;
use Yii;
use common\modules\store\models\Store;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlAgents;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tl_delivery_proposal_default_sub_routes".
 *
 * @property integer $id
 * @property integer $tl_delivery_proposal_default_route_id
 * @property integer $client_id
 * @property integer $agent_id
 * @property integer $car_id
 * @property integer $transport_type
 * @property integer $from_point_id
 * @property integer $to_point_id
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlDeliveryProposalDefaultSubRoute extends ActiveRecord
{

    const TRANSPORT_TYPE_AUTO = 1;
    const TRANSPORT_TYPE_RAIL = 2;
    const TRANSPORT_TYPE_AIR = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_default_sub_routes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_delivery_proposal_default_route_id', 'client_id', 'from_point_id', 'to_point_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted', 'transport_type', 'agent_id', 'car_id'], 'integer'],
            [['from_point_id'], 'compare', 'compareAttribute'=>'to_point_id', 'operator'=>'!='],
            [['to_point_id'], 'compare', 'compareAttribute'=>'from_point_id', 'operator'=>'!='],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'tl_delivery_proposal_default_route_id' => Yii::t('forms', 'Tl Delivery Proposal Default Route ID'),
            'client_id' => Yii::t('forms', 'Client ID'),// NOT USED
            'agent_id' => Yii::t('forms', 'Agent ID'),
            'car_id' => Yii::t('forms', 'Car ID'),
            'transport_type' => Yii::t('forms', 'Transport Type'),
            'from_point_id' => Yii::t('forms', 'From Point ID'),
            'to_point_id' => Yii::t('forms', 'To Point ID'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

//    /*
//         * Relation has one with Client
//         **/
//    public function getClient()
//    {
//        return $this->hasOne(Client::className(), ['id' => 'client_id']);
//    }

    /*
   * Relation has One with Store
   *
   * */
    public function getToPoint()
    {
        return $this->hasOne(Store::className(), ['id' => 'to_point_id']);
    }

    /*
    * Relation has One with Store
    *
    * */
    public function getFromPoint()
    {
        return $this->hasOne(Store::className(), ['id' => 'from_point_id']);
    }

    /*
    * Relation has One with Car
    *
    * */
    public function getCar()
    {
        return $this->hasOne(TlCars::className(), ['id' => 'car_id']);
    }

    /*
     * Relation has One with Agent
     *
     * */
    public function getAgent()
    {
        return $this->hasOne(TlAgents::className(), ['id' => 'agent_id']);
    }

    /**
     * @return array .
     */
    public static function getTransportTypeArray()
    {
        return [
            self::TRANSPORT_TYPE_AUTO => Yii::t('titles', 'Auto'),
            self::TRANSPORT_TYPE_RAIL => Yii::t('titles', 'Train'),
            self::TRANSPORT_TYPE_AIR => Yii::t('titles', 'Air'),
        ];
    }

    /**
     * @return string .
     */
    public function getTransportTypeValue($value = null)
    {
        if(is_null($value)) {
            $value = $this->transport_type;
        }

        return ArrayHelper::getValue(self::getTransportTypeArray(),$value);
    }

    /*
    * Relation has many with TlDeliveryProposalDefaultUnforeseenExpensess
    * */
    public function getTlDeliveryProposalRouteUnforeseenExpenses()
    {
        return $this->hasMany(TlDeliveryProposalDefaultUnforeseenExpenses::className(), ['tl_delivery_proposal_default_sub_route_id'=>'id'])->andWhere(['deleted'=>0]);
    }
}
