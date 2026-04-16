<?php

namespace stockDepartment\modules\kaspi\dto;

use yii\base\Model;

/**
 * DTO для частичного возврата после доставки (сценарий B п.9).
 *
 * Пример тела запроса (JSON):
 * {
 *   "items":       [{"product_guid":"KZ_SKU_12345","qty":1}],
 *   "refund_code": "KASPI_REFUND_123",
 *   "note":        "Клиент вернул один из двух товаров"
 * }
 */
class PartialReturnRequestDto extends Model
{
    /** @var array<int, array{product_guid:string, qty:int}> */
    public $items = [];

    /** @var string|null Код возврата на стороне Kaspi */
    public $refund_code;

    /** @var string|null Произвольная заметка */
    public $note;

    public function rules()
    {
        return [
            [['items'], 'required'],
            [['items'], 'validateItems'],
            [['refund_code'], 'string', 'max' => 64],
            [['note'], 'string'],
        ];
    }

    public function validateItems($attribute)
    {
        if (!is_array($this->items) || empty($this->items)) {
            $this->addError($attribute, 'Список items не должен быть пустым');
            return;
        }

        foreach ($this->items as $index => $item) {
            if (!is_array($item)) {
                $this->addError($attribute, "items[{$index}]: ожидается объект");
                continue;
            }
            $guid = isset($item['product_guid']) ? (string) $item['product_guid'] : '';
            $qty  = isset($item['qty']) ? (int) $item['qty'] : 0;
            if ($guid === '') {
                $this->addError($attribute, "items[{$index}].product_guid обязателен");
            }
            if ($qty <= 0) {
                $this->addError($attribute, "items[{$index}].qty должен быть > 0");
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'items'       => 'Позиции возврата',
            'refund_code' => 'Код возврата Kaspi',
            'note'        => 'Заметка',
        ];
    }
}
