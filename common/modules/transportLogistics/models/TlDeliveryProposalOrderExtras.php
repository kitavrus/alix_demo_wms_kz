<?php

namespace common\modules\transportLogistics\models;

use Yii;
use common\models\ActiveRecord;

/**
 * This is the model class for table "tl_delivery_proposal_order_extras".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $tl_delivery_proposal_id
 * @property integer $tl_delivery_route_id
 * @property integer $tl_delivery_proposal_order_id
 * @property string $name
 * @property integer $number_places
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class TlDeliveryProposalOrderExtras extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_order_extras';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['client_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'required'],
            [['client_id', 'tl_delivery_proposal_id', 'tl_delivery_route_id', 'tl_delivery_proposal_order_id', 'number_places', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 255]
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
            'tl_delivery_proposal_id' => Yii::t('forms', 'Tl Delivery Proposal ID'),
            'tl_delivery_route_id' => Yii::t('forms', 'Tl Delivery Route ID'),
            'tl_delivery_proposal_order_id' => Yii::t('forms', 'Tl Delivery Proposal Order ID'),
            'name' => Yii::t('forms', 'Name'),
            'number_places' => Yii::t('forms', 'Number of places'),
            'comment' => Yii::t('forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }
}
