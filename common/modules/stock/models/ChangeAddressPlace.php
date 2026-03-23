<?php
namespace common\modules\stock\models;

use Yii;

/**
 * This is the model class for table "change_address_place".
 *
 * @property int $id
 * @property string $from_barcode Address/Box barcode
 * @property string $to_barcode Address/Box barcode
 * @property string $change_type Change type
 * @property string $message Message
 * @property int $created_user_id Created user id
 * @property int $updated_user_id Updated user id
 * @property int $created_at Created at
 * @property int $updated_at Updated at
 * @property int $deleted Deleted
 */
class ChangeAddressPlace extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'change_address_place';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['change_type','created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['from_barcode', 'to_barcode'], 'string', 'max' => 16],
            [['message'], 'text'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'from_barcode' => Yii::t('app', 'From Barcode'),
            'to_barcode' => Yii::t('app', 'To Barcode'),
            'change_type' => Yii::t('app', 'Type'),
            'message' => Yii::t('app', 'Message'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
