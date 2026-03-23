<?php

namespace common\modules\transportLogistics\models;
use common\modules\client\models\Client;
use common\modules\store\models\Store;
use common\models\ActiveRecord;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultSubRoute;
use Yii;

/**
 * This is the model class for table "tl_delivery_proposal_default_routes".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $from_point_id
 * @property integer $to_point_id
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlDeliveryProposalDefaultRoute extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_default_routes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'from_point_id', 'to_point_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
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
            'client_id' => Yii::t('forms', 'Client ID'),
            'from_point_id' => Yii::t('forms', 'From Point ID'),
            'to_point_id' => Yii::t('forms', 'To Point ID'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /*
     * Relation has one with Client
     **/
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

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
   * Relation has one with conditions
   * */
    public function getSubRoutes()
    {
        return $this->hasMany(TlDeliveryProposalDefaultSubRoute::className(), ['tl_delivery_proposal_default_route_id' => 'id']);
    }
}
