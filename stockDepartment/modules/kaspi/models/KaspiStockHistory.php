<?php

namespace stockDepartment\modules\kaspi\models;

use yii\db\ActiveRecord;

/**
 * Историческая запись об изменении остатка товара на Kaspi.
 *
 * @property int    $id
 * @property string $product_guid    GUID / SKU товара на Kaspi
 * @property int    $qty             Количество (override для PP1)
 * @property string $note            Произвольная заметка
 * @property int    $effective_from  Unix timestamp даты активации
 * @property string $push_status     Статус: PENDING, SENT, ERROR, SKIPPED
 * @property string $push_response   Сырой ответ при генерации/отправке
 * @property int    $push_at         Unix timestamp момента активации
 * @property int    $created_at      Unix timestamp создания записи
 * @property int    $created_user_id ID пользователя, создавшего запись
 */
class KaspiStockHistory extends ActiveRecord
{
    const PUSH_STATUS_PENDING = 'PENDING';
    const PUSH_STATUS_SENT    = 'SENT';
    const PUSH_STATUS_ERROR   = 'ERROR';
    const PUSH_STATUS_SKIPPED = 'SKIPPED';

    public static function tableName()
    {
        return '{{%kaspi_stock_history}}';
    }

    public function rules()
    {
        return [
            [['product_guid', 'qty', 'effective_from'], 'required'],
            [['qty', 'effective_from', 'push_at', 'created_at', 'created_user_id'], 'integer'],
            [['qty'], 'integer', 'min' => 0],
            [['product_guid'], 'string', 'max' => 128],
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
            'qty'             => 'Остаток',
            'note'            => 'Заметка',
            'effective_from'  => 'Активен с (дата)',
            'push_status'     => 'Статус',
            'push_response'   => 'Ответ при генерации',
            'push_at'         => 'Активировано',
            'created_at'      => 'Создано',
            'created_user_id' => 'Создал',
        ];
    }

    public function isEffective()
    {
        return $this->effective_from <= time();
    }
}
