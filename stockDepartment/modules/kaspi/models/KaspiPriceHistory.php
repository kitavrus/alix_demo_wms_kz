<?php

namespace stockDepartment\modules\kaspi\models;

use yii\db\ActiveRecord;

/**
 * Историческая запись об изменении цены товара на Kaspi.
 *
 * Намеренно наследуется от \yii\db\ActiveRecord (не от common\models\ActiveRecord),
 * чтобы не подключать TimestampBehavior/BlameableBehavior/AuditBehavior —
 * в нашей таблице нет updated_at, updated_user_id и deleted.
 *
 * @property int    $id
 * @property string $product_guid    GUID / SKU товара на Kaspi
 * @property float  $price           Цена в KZT (целое число в тенге)
 * @property string $price_type      Тип цены (BASE, SALE, PROMO и т.п.)
 * @property string $note            Произвольная заметка
 * @property int    $effective_from  Unix timestamp даты активации цены
 * @property string $push_status     Статус: PENDING, SENT, ERROR, SKIPPED
 * @property string $push_response   Сырой ответ при генерации/отправке
 * @property int    $push_at         Unix timestamp момента активации
 * @property int    $created_at      Unix timestamp создания записи
 * @property int    $created_user_id ID пользователя, создавшего запись
 */
class KaspiPriceHistory extends ActiveRecord
{
    const PUSH_STATUS_PENDING = 'PENDING';
    const PUSH_STATUS_SENT    = 'SENT';
    const PUSH_STATUS_ERROR   = 'ERROR';
    const PUSH_STATUS_SKIPPED = 'SKIPPED';

    public static function tableName()
    {
        return '{{%kaspi_price_history}}';
    }

    public function rules()
    {
        return [
            [['product_guid', 'price', 'price_type', 'effective_from'], 'required'],
            [['price'], 'number', 'min' => 0],
            [['effective_from', 'push_at', 'created_at', 'created_user_id'], 'integer'],
            [['product_guid'], 'string', 'max' => 128],
            [['price_type'], 'string', 'max' => 64],
            [['push_status'], 'string', 'max' => 16],
            [['note', 'push_response'], 'string'],
            [['push_status'], 'default', 'value' => self::PUSH_STATUS_PENDING],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'product_guid'    => 'GUID товара (Kaspi SKU)',
            'price'           => 'Цена (KZT)',
            'price_type'      => 'Тип цены',
            'note'            => 'Заметка',
            'effective_from'  => 'Активна с (дата)',
            'push_status'     => 'Статус',
            'push_response'   => 'Ответ при генерации',
            'push_at'         => 'Активировано',
            'created_at'      => 'Создано',
            'created_user_id' => 'Создал',
        ];
    }

    /**
     * Дата активации уже наступила?
     */
    public function isEffective()
    {
        return $this->effective_from <= time();
    }
}
