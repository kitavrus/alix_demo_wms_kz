<?php

namespace common\modules\codebook\models;

use Yii;
use common\models\ActiveRecord;
//use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "codebook".
 *
 * @property integer $id
 * @property integer $base_type
 * @property string $cod_prefix
 * @property string $name
 * @property string $count_cell
 * @property integer $barcode
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $modified_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Codebook extends ActiveRecord
{
    /*
     * @var integer status
     * */
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /*
     * @var integer base type
     * */
    const BASE_TYPE_BOX = 1; // Короб
    const BASE_TYPE_REGIMENT = 2; // Полка
    const BASE_TYPE_RACK = 3; // Стеллаж
    const BASE_TYPE_PALLET = 4; // Палета
    const BASE_TYPE_BOX_COLINS_OLD = 5; // короб сolins old

    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'timestampBehavior' => [
//                'class' => TimestampBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
//                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
//                ],
//                'value' => new Expression('NOW()'),
//            ],
//        ];
//    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'codebook';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['base_type','cod_prefix', 'name'], 'required'],
            [['base_type','barcode','count_cell', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['cod_prefix'], 'string', 'min' => 2, 'max' => 3],
            [['name'], 'string', 'max' => 128],
//            [['cod_prefix'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'cod_prefix' => Yii::t('forms', 'Code Prefix'),
            'base_type' => Yii::t('forms', 'Base type'),
            'name' => Yii::t('forms', 'Name'),
            'count_cell' => Yii::t('forms', 'Count Cell'),
            'barcode' => Yii::t('forms', 'Barcode'),
            'status' => Yii::t('forms', 'Status'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Modified User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }



    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('forms', 'Not active'),
            self::STATUS_DELETED => Yii::t('forms', 'Deleted'),
        ];
    }

    /**
     * @return string Читабельный статус.
     */
    public function getStatusValue()
    {
        return ArrayHelper::getValue($this->getStatusArray(),$this->status);
    }

    /**
     * @return array With base default type.
     */
    public static function getBaseTypeArray()
    {
        return [
            self::BASE_TYPE_BOX => Yii::t('forms', 'Box'),
            self::BASE_TYPE_BOX_COLINS_OLD => Yii::t('forms', 'Box Colins old'),
            self::BASE_TYPE_REGIMENT => Yii::t('forms', 'Regiment'),//стелаж
            self::BASE_TYPE_RACK => Yii::t('forms', 'Rack'),
            self::BASE_TYPE_PALLET => Yii::t('forms', 'Pallet'),
        ];
    }

    /**
     * @return string Display value
     */
    public function getBaseTypeValue()
    {
        return ArrayHelper::getValue($this->getBaseTypeArray(),$this->base_type);
    }
}