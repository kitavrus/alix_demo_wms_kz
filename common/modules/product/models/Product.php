<?php

namespace common\modules\product\models;

use Yii;
use common\models\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

//use yii\db\Expression;
//use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $client_product_id
 * @property string $name
 * @property string $sku
 * @property string $model
 * @property integer $status
 * @property integer $price
 * @property string $color
 * @property string $size
 * @property string $category
 * @property string $gender
 * @property string $field_extra1
 * @property string $field_extra2
 * @property integer $weight_brutto
 * @property integer $weight_netto
 * @property integer $m3
 * @property integer $length
 * @property integer $width
 * @property integer $height
 * @property string $barcode
 *
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Product extends \common\models\ActiveRecord
{
    /*
     * @var integer status
     * */
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
//                'value' => new Expression('NOW()'),
            ],
//			'blameableBehavior'=>[
//				'class' => BlameableBehavior::className(),
//				'createdByAttribute' => 'created_user_id',
//				'updatedByAttribute' => 'updated_user_id',
//			],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','model'], 'required'],
            [['client_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['client_product_id', 'sku','model'], 'string', 'max' => 256],
            [['name'], 'string', 'max' => 256],
            [['price'], 'integer'],
			[['field_extra1','field_extra2','field_extra3'], 'string'],
			[['color','size','category','gender'], 'string'],
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
            'client_product_id' => Yii::t('forms', 'Client Product ID'),
            'name' => Yii::t('forms', 'Product name'),
            'sku' => Yii::t('forms', 'SKU'),
            'status' => Yii::t('forms', 'Status'),
            'price' => Yii::t('forms', 'Price'),
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
     * @return string Читабельный статус поста.
     */
    public function getStatus()
    {
        $status = self::getStatusArray();
        return $status[$this->status];
    }
}