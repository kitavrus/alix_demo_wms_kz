<?php

namespace stockDepartment\modules\product\models;

use Yii;
use common\models\ActiveRecord;
//use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "product_barcodes".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $product_id
 * @property string $barcode
 * @property integer $created_user_id
 * @property integer $modified_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class ProductBarcodes extends ActiveRecord
{
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
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_barcodes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'product_id', 'barcode', 'created_user_id', 'modified_user_id', 'created_at', 'updated_at'], 'required'],
            [['client_id', 'product_id', 'created_user_id', 'modified_user_id', 'created_at', 'updated_at'], 'integer'],
            [['barcode'], 'string', 'max' => 24]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('titles', 'ID'),
            'client_id' => Yii::t('titles', 'Client ID'),
            'product_id' => Yii::t('titles', 'Product Id'),
            'barcode' => Yii::t('titles', 'Barc ID'),
            'created_user_id' => Yii::t('titles', 'Created User Id'),
            'modified_user_id' => Yii::t('titles', 'Modified User Id'),
            'created_at' => Yii::t('titles', 'Created ID'),
            'updated_at' => Yii::t('titles', 'Updated ID'),
        ];
    }

    /*
     * Get product data
     * */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /*
     * Check whether there exists this item from this client
     * @param integer $client_id
     * @param string $barcode Product barcode
     * @return integer Count product by barcode
     * */
    public static function checkBarcodeExistsFromClient($client_id,$barcode)
    {
        return (int)self::find()->where('client_id = :client_id AND barcode = :barcode',[':client_id'=>$client_id,':barcode'=>$barcode])->count();
    }

    /*
     * Get product by barcode
     * @param string $barcode
     * @return mix | Return product model or null
     * */
    public static function getProductByBarcode($client_id,$barcode)
    {
        $product = null;
        if($pb = self::find()->where('client_id = :client_id AND barcode = :barcode',[':client_id'=>$client_id,':barcode'=>$barcode])->one()) {
            $product = $pb->product;
        }
        return $product;
    }

    /*
     * Check barcode in other product
     * @param integer $client_id
     * @param integer $product_id
     * @param string $barcode Product barcode
     * */
    public static function checkBarcodeInOtherProduct($client_id,$product_id,$barcode)
    {
        // TODO ...
    }
}
