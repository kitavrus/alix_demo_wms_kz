<?php

namespace stockDepartmentartment\modules\store\models;

use Yii;


/**
 * This is the model class for table "store_reviews".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $store_id
 * @property integer $tl_delivery_proposal_id
 * @property integer $delivery_datetime
 * @property integer $rate
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class StoreReviews extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_reviews';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'store_id', 'tl_delivery_proposal_id', 'number_of_places', 'rate', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'string', 'max' => 999],
            [['delivery_datetime'], 'safe'],
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
            'store_id' => Yii::t('forms', 'Store ID'),
            'tl_delivery_proposal_id' => Yii::t('forms', 'Tl Delivery Proposal ID'),
            'delivery_datetime' => Yii::t('forms', 'Delivery Datetime'),
            'number_of_places' => Yii::t('forms', 'Number of places'),
            'rate' => Yii::t('forms', 'Rate'),
            'comment' => Yii::t('forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Modified User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /*
    * Relation with Store table
    * */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /*
    * Relation has one with Client
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
   * Relation has one with Proposal
   * */
    public function getProposal()
    {
        return $this->hasOne(TlDeliveryProposal::className(), ['id' => 'tl_delivery_proposal_id']);
    }
}
