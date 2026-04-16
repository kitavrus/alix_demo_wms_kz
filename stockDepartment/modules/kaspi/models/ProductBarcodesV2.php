<?php

namespace stockDepartment\modules\kaspi\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Штрих-код карточки товара v2.
 *
 * @property int      $id
 * @property int      $product_id   FK -> product_v2.id
 * @property string   $barcode      Штрих-код
 * @property int      $created_at
 * @property int      $updated_at
 * @property int|null $deleted
 */
class ProductBarcodesV2 extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%product_barcodes_v2}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['product_id', 'barcode'], 'required'],
            [['product_id', 'deleted'], 'integer'],
            [['barcode'], 'string', 'max' => 32],
            [['product_id', 'barcode'], 'unique', 'targetAttribute' => ['product_id', 'barcode']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(ProductV2::class, ['id' => 'product_id']);
    }

    /**
     * @param int $productId
     * @param string $barcode
     * @return bool
     */
    public static function existsFor($productId, $barcode)
    {
        return static::find()
            ->andWhere(['product_id' => $productId, 'barcode' => $barcode])
            ->exists();
    }
}
