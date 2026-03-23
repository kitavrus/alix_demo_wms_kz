<?php

namespace common\modules\client\models;
use common\modules\client\models\Client;
use common\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "client_settings".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $option_name
 * @property integer $option_value
 * @property string $description
 * @property string $default_value
 * @property string $option_type
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class ClientSettings extends ActiveRecord
{
    //option type
    const OPTION_TYPE_FUNCTION = 1;
    const OPTION_TYPE_DROPDOWN = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['client_id', 'deleted', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'option_type'], 'integer'],
            [['description'], 'string'],
            [['option_name', 'option_value', 'default_value'], 'safe']
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
            'option_name' => Yii::t('forms', 'Option name'),
            'option_value' => Yii::t('forms', 'Option value'),
            'option_type' => Yii::t('forms', 'Option type'),
            'description' => Yii::t('forms', 'Description'),
            'default_value' => Yii::t('forms', 'Default value'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /*
     * Relation with client
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }


    /*
     * Array with option type
     */
    public function getOptionType($key=NULL){
        $data = [
            self::OPTION_TYPE_FUNCTION => Yii::t('titles', 'Function'),
            self::OPTION_TYPE_DROPDOWN => Yii::t('titles', 'Dropdown'),
        ];

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**getNoChangeMcKgNpArray()
     * Get list no change price
     * @return array .
     */
    public static function getOptionsList($key=null)
    {
        $data = [
            'getPaymentMethodArray' => 'Способ оплаты(нал\безнал)',
            'getNoChangePriceArray' => 'Изменять цену (автоматически)',
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }
}
