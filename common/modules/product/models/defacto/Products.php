<?php

namespace common\modules\product\models\defacto;

use common\overloads\ArrayHelper;
use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property string $SkuId
 * @property string $LotOrSingleBarcode
 * @property string $ShortCode
 * @property string $Description
 * @property string $Note
 * @property integer $LotSingle
 * @property string $Classification
 * @property string $Color
 * @property string $FDate
 * @property integer $Perc
 * @property integer $Origin
 * @property string $ProcessTime
 * @property string $Nop
 * @property string $Size
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class Products extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbDefactoSpecial');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['SkuId', 'LotSingle', 'Classification', 'Perc', 'Origin', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['LotOrSingleBarcode'], 'string', 'max' => 18],
            [['ShortCode'], 'string', 'max' => 28],
            [['Nop','Size','Description'], 'string', 'max' => 128],
            [['Note', 'Color'], 'string', 'max' => 16],
            [['FDate', 'ProcessTime'], 'string', 'max' => 68],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'SkuId' => Yii::t('app', 'Sku ID'),
            'LotOrSingleBarcode' => Yii::t('app', 'Lot Or Single Barcode'),
            'ShortCode' => Yii::t('app', 'Short Code'),
            'Description' => Yii::t('app', 'Description'),
            'Note' => Yii::t('app', 'Note'),
            'LotSingle' => Yii::t('app', 'Lot Single'),
            'Classification' => Yii::t('app', 'Classification'),
            'Color' => Yii::t('app', 'Color'),
            'FDate' => Yii::t('app', 'Fdate'),
            'Perc' => Yii::t('app', 'Perc'),
            'Origin' => Yii::t('app', 'Origin'),
            'ProcessTime' => Yii::t('app', 'Process Time'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /*
     * @param integer $SkuId  Lot id in Defacto system
     * @param integer $LotOrSingleBarcode  Lot barcode
     * @param integer $ShortCode  Lot model
     * @return AR Products
     * */
    public static function create($skuId,
                                  $lotOrSingleBarcode,
                                  $shortCode,
                                  $color = '',
                                  $description = '',
                                  $nop = '',
                                  $lotSingle='',
                                  $classification='',
                                  $perc='',
                                  $origin='',
                                  $size=''
    )
    {
        $product = new Products();
        $product->SkuId = $skuId;
        $product->LotOrSingleBarcode = $lotOrSingleBarcode;
        $product->ShortCode = $shortCode;
        $product->Description = $description;
        $product->LotSingle = $lotSingle;
        $product->Classification = $classification;
        $product->Perc = $perc;
        $product->Origin = $origin;
        $product->Nop = $nop;
        $product->Size = $size;

        if(!empty($color)) { $product->Color = $color; }
        $product->save(false);
        return $product;
    }

    /*
     * @param integer $SkuId  Lot id in Defacto system
     * @param integer $LotOrSingleBarcode  Lot barcode
     * @param integer $ShortCode  Lot model
     * @return AR Products
     * */
    public static function createByAttributes($dataFromDeFactoAPI)
    {
        $product = new Products();
        $product->SkuId = ArrayHelper::getValue($dataFromDeFactoAPI,'SkuId');
        $product->LotOrSingleBarcode =  ArrayHelper::getValue($dataFromDeFactoAPI,'LotOrSingleBarcode');
        $product->ShortCode = ArrayHelper::getValue($dataFromDeFactoAPI,'ShortCode');
        $product->Description = ArrayHelper::getValue($dataFromDeFactoAPI,'Description');
        $product->LotSingle = ArrayHelper::getValue($dataFromDeFactoAPI,'LotSingle');
        $product->Classification = ArrayHelper::getValue($dataFromDeFactoAPI,'Classification');
        $product->Perc = ArrayHelper::getValue($dataFromDeFactoAPI,'Perc');
        $product->Origin = ArrayHelper::getValue($dataFromDeFactoAPI,'Origin');
        $product->Color = ArrayHelper::getValue($dataFromDeFactoAPI,'Color');
        $product->Nop = ArrayHelper::getValue($dataFromDeFactoAPI,'Nop');
        $product->Size = ArrayHelper::getValue($dataFromDeFactoAPI,'Size');
        $product->save(false);
        return $product;
    }

    /*
     * @param integer $SkuId  Lot id in Defacto system
     * @param integer $LotOrSingleBarcode  Lot barcode
     * @param integer $ShortCode  Lot model
     * @return boolean
     * */
    public static function isExists($SkuId,$LotOrSingleBarcode,$ShortCode)
    {
        return Products::find()->andWhere([
            'SkuId'=>$SkuId,
            'LotOrSingleBarcode'=>$LotOrSingleBarcode,
            'ShortCode'=>$ShortCode,
        ])->exists();
    }
}