<?php

namespace common\modules\outbound\models;
use common\modules\client\models\Client;
use Yii;

/**
 * This is the model class for table "outbound_box_labels".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $filename
 * @property integer $outbound_order_id
 * @property integer $return_order_id
 * @property integer $outbound_order_number
 * @property string $box_label_url
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class OutboundBoxLabels extends \common\models\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outbound_box_labels';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'outbound_order_id', 'return_order_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['box_label_url', 'filename', 'outbound_order_number'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'to_point_title' => Yii::t('outbound/forms', 'To point title'),
            'id' => Yii::t('forms', 'ID'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'outbound_order_id' => Yii::t('outbound/forms', 'Outbound Order ID'),
            'outbound_order_number' => Yii::t('outbound/forms', 'Outbound Order Number'),
            'box_label_url' => Yii::t('outbound/forms', 'Box Label Url'),
            'filename' => Yii::t('outbound/forms', 'Filename'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /*
   * Relation has one with Client
   * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

}
