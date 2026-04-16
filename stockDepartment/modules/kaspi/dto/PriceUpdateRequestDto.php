<?php

namespace stockDepartment\modules\kaspi\dto;

use yii\base\Model;

/**
 * DTO для входящего запроса обновления цены товара.
 *
 * Пример тела запроса (JSON):
 * {
 *   "product_guid":    "KZ_SKU_12345",
 *   "price":           19990.00,
 *   "price_type":      "BASE",
 *   "note":            "Весенняя коллекция",
 *   "effective_from":  "2026-05-01"
 * }
 */
class PriceUpdateRequestDto extends Model
{
    /** @var string GUID / merchantProductCode товара на Kaspi */
    public $product_guid;

    /** @var float Новая цена в KZT */
    public $price;

    /** @var string Тип цены: BASE, SALE, PROMO и т.д. */
    public $price_type = 'BASE';

    /** @var string|null Произвольная заметка */
    public $note;

    /**
     * @var string Дата активации цены в формате Y-m-d.
     * Если не указана — считается «сегодня» и цена отправляется в Kaspi немедленно.
     */
    public $effective_from;

    public function rules()
    {
        return [
            [['product_guid', 'price', 'price_type'], 'required'],
            [['price'], 'number', 'min' => 1],
            [['product_guid'], 'string', 'max' => 128],
            [['price_type'], 'in', 'range' => ['BASE', 'SALE', 'PROMO']],
            [['note'], 'string'],
            [['effective_from'], 'default', 'value' => date('Y-m-d')],
            [['effective_from'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'product_guid'   => 'GUID товара',
            'price'          => 'Цена',
            'price_type'     => 'Тип цены',
            'note'           => 'Заметка',
            'effective_from' => 'Активна с',
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

    /**
     * Цена должна быть применена прямо сейчас?
     */
    public function isImmediatelyEffective()
    {
        return $this->getEffectiveFromTimestamp() <= time();
    }
}
