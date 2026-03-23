<?php

namespace stockDepartment\modules\kaspi\dto;

/**
 * DTO для атрибутов товара в Kaspi
 * @property string $code Код атрибута
 * @property string $value Значение атрибута
 */
class AttributeDto
{
    public $code;
    public $value;

    /**
     * @param string $code Код атрибута
     * @param string $value Значение атрибута
     */
    public function __construct($code, $value)
    {
        $this->code = $code;
        $this->value = $value;
    }

    /**
     * Преобразует объект в массив для API
     * @return array
     */
    public function toArray()
    {
        return [
            'code' => $this->code,
            'value' => $this->value
        ];
    }
}

