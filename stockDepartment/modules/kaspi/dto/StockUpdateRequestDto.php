<?php

namespace stockDepartment\modules\kaspi\dto;

use yii\base\Model;

/**
 * DTO для входящего запроса обновления остатка товара.
 *
 * Пример тела запроса (JSON):
 * {
 *   "product_guid":    "KZ_SKU_12345",
 *   "qty":             5,
 *   "note":            "Ручная корректировка",
 *   "effective_from":  "2026-05-01"
 * }
 */
class StockUpdateRequestDto extends Model
{
    /** @var string GUID / merchantProductCode товара на Kaspi */
    public $product_guid;

    /** @var int Количество на складе */
    public $qty;

    /** @var string|null Произвольная заметка */
    public $note;

    /**
     * @var string Дата активации в формате Y-m-d.
     * Если не указана — считается «сегодня» и override применяется немедленно.
     */
    public $effective_from;

    public function rules()
    {
        return [
            [['product_guid', 'qty'], 'required'],
            [['qty'], 'integer', 'min' => 0],
            [['product_guid'], 'string', 'max' => 128],
            [['note'], 'string'],
            [['effective_from'], 'default', 'value' => date('Y-m-d')],
            [['effective_from'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'product_guid'   => 'GUID товара',
            'qty'            => 'Остаток',
            'note'           => 'Заметка',
            'effective_from' => 'Активен с',
        ];
    }

    /**
     * Unix timestamp даты активации (начало дня по Asia/Almaty).
     */
    public function getEffectiveFromTimestamp()
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $this->effective_from, new \DateTimeZone('Asia/Almaty'));
        $dt->setTime(0, 0, 0);
        return $dt->getTimestamp();
    }

    public function isImmediatelyEffective()
    {
        return $this->getEffectiveFromTimestamp() <= time();
    }
}
