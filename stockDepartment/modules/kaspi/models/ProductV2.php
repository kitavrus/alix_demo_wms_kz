<?php

namespace stockDepartment\modules\kaspi\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Карточка товара v2 (синхронизируется из NMDX 1C).
 *
 * @property int         $id
 * @property string      $guid                 GUID товара в NMDX (уникальный ключ для upsert)
 * @property string|null $barcode              Основной штрих-код из мастер-данных
 * @property string|null $article              Артикул
 * @property string|null $category             Категория
 * @property string|null $name                 Наименование (RU)
 * @property string|null $name_kaz             Наименование (KZ)
 * @property string|null $brand                Бренд
 * @property string|null $VAT_rate             Ставка НДС, %
 * @property string|null $country_of_origin    Страна происхождения
 * @property string|null $description          Описание
 * @property string|null $color_code           Код цвета (может быть буквенно-цифровым: "AF501", "P01", "G03")
 * @property string|null $color_name           Название цвета
 * @property string|null $filling              Объём / вес
 * @property string|null $code_tnved           Код ТН ВЭД
 * @property string|null $code_nkt             Код НКТ
 * @property int         $created_at
 * @property int         $updated_at
 * @property int|null    $deleted
 */
class ProductV2 extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%product_v2}}';
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
            [['guid'], 'required'],
            [['guid'], 'string', 'max' => 64],
            [['barcode', 'article', 'code_tnved', 'code_nkt', 'color_code'], 'string', 'max' => 32],
            [['category', 'name', 'name_kaz'], 'string', 'max' => 256],
            [['brand', 'country_of_origin', 'color_name'], 'string', 'max' => 128],
            [['filling'], 'string', 'max' => 64],
            [['description'], 'string'],
            [['VAT_rate'], 'number'],
            [['deleted'], 'integer'],
            [['guid'], 'unique'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBarcodes()
    {
        return $this->hasMany(ProductBarcodesV2::class, ['product_id' => 'id']);
    }

    /**
     * @param string $guid
     * @return self|null
     */
    public static function findByGuid($guid)
    {
        return static::find()->andWhere(['guid' => $guid])->one();
    }
}
