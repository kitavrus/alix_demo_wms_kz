<?php

namespace common\modules\kpiSettings\models;

use common\modules\client\models\Client;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "kpi_setting".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $operation_type
 * @property integer $one_item_time
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class KpiSetting extends \common\models\ActiveRecord
{
    /*
     * @var integer operation type
     * */
    const OPERATION_TYPE_UNSET = '0';
    const OPERATION_TYPE_PICKING = '1';
    const OPERATION_TYPE_SCANNING_INBOUND = '2';
    const OPERATION_TYPE_SCANNING_OUTBOUND = '3';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kpi_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'operation_type', 'one_item_time', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer']
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
            'operation_type' => Yii::t('forms', 'Operation Type'),
            'one_item_time' => Yii::t('forms', 'One Item Time'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /**
     * @return array
     */
    public static function getOperationTypeArray()
    {
        return [
            self::OPERATION_TYPE_UNSET => Yii::t('titles', 'Not set'),
            self::OPERATION_TYPE_PICKING => Yii::t('titles', 'Picking'),
            self::OPERATION_TYPE_SCANNING_INBOUND => Yii::t('titles', 'Scanning Inbound'),
            self::OPERATION_TYPE_SCANNING_OUTBOUND => Yii::t('titles', 'Scanning Outbound'),
        ];
    }

    /**
     * @return string.
     */
    public function getOperationTypeValue($value=null)
    {
        if(is_null($value)){
            $value = $this->operation_type;
        }
        return ArrayHelper::getValue(self::getOperationTypeArray(), $value);
    }

    /*
    * Relation with Client table
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
     * Get operation type by client id
     * @param integer $client_id
     * @param integer $operation_type
     * @return integer Second on one
     * */
    public static function getOperationTypeByClientID($client_id, $operation_type)
    {
        $r = 0;
        if($o = self::find()->where(['client_id'=>$client_id,'operation_type'=>$operation_type])->one()) {
            $r = $o->one_item_time;
        }
        return $r;
    }

    /*
    * Get picking type
    * @param integer $client_id
     *
    * @return integer second
    * */
    public static function getPickingTime($client_id, $count)
    {
        $oneTime = self::getOperationTypeByClientID($client_id,self::OPERATION_TYPE_PICKING);
        return $oneTime * intval($count);
    }

    /*
    * Get Outbound scanning type
    * @param integer $client_id
     *
    * @return integer second
    * */
    public static function getOutboundScanningTime($client_id, $count)
    {
        $oneTime = self::getOperationTypeByClientID($client_id,self::OPERATION_TYPE_SCANNING_OUTBOUND);
        return $oneTime * intval($count);
    }

    /*
   * Get inbound scanning type
   * @param integer $client_id
    *
   * @return integer second
   * */
    public static function getInboundScanningTime($client_id, $count)
    {
        $oneTime = self::getOperationTypeByClientID($client_id,self::OPERATION_TYPE_SCANNING_INBOUND);
        return $oneTime * intval($count);
    }
}
