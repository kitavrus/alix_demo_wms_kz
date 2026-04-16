<?php

namespace stockDepartment\modules\kaspi\dto;

/**
 * DTO одной позиции номенклатуры, приходящей из сервиса Alix 1C.
 *
 * Источник: GET {alix1cBaseUrl}/items
 *
 * Пример JSON:
 * {
 *   "guid": "e8a98110-2cf0-11f1-9759-ac1f6b8140a5",
 *   "barcode": "8682538223186",
 *   "article": "1100014950",
 *   "category": "СИЯЮЩИЙ ПРАЙМЕР ДЛЯ ЛИЦА",
 *   "name": "СИЯЮЩИЙ ПРАЙМЕР ДЛЯ ЛИЦА",
 *   "name_kaz": "ЖАРҚЫРАЙТЫН БЕТ ҮШІН ПРАЙМЕР",
 *   "brand": "Alix Avien",
 *   "VAT_rate": 16,
 *   "country_of_origin": "ТУРЦИЯ",
 *   "description": "",
 *   "color_code": 0,
 *   "color_name": "",
 *   "filling": "30",
 *   "code_tnved": "",
 *   "code_nkt": ""
 * }
 */
class AlixItemDto
{
    /** @var string */
    public $guid;
    /** @var string|null */
    public $barcode;
    /** @var string|null */
    public $article;
    /** @var string|null */
    public $category;
    /** @var string|null */
    public $name;
    /** @var string|null */
    public $name_kaz;
    /** @var string|null */
    public $brand;
    /** @var float|int|null */
    public $VAT_rate;
    /** @var string|null */
    public $country_of_origin;
    /** @var string|null */
    public $description;
    /** @var string|int|null Может быть числом (0, 101) или строкой с буквенным префиксом ("AF501", "P01", "G03"). */
    public $color_code;
    /** @var string|null */
    public $color_name;
    /** @var string|null */
    public $filling;
    /** @var string|null */
    public $code_tnved;
    /** @var string|null */
    public $code_nkt;

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data)
    {
        $dto = new self();
        foreach (self::fieldNames() as $field) {
            if (array_key_exists($field, $data)) {
                $dto->{$field} = $data[$field];
            }
        }

        return $dto;
    }

    /**
     * @return string[]
     */
    public static function fieldNames()
    {
        return [
            'guid',
            'barcode',
            'article',
            'category',
            'name',
            'name_kaz',
            'brand',
            'VAT_rate',
            'country_of_origin',
            'description',
            'color_code',
            'color_name',
            'filling',
            'code_tnved',
            'code_nkt',
        ];
    }

    /**
     * Валиден ли DTO для записи в БД (минимум — guid).
     *
     * @return bool
     */
    public function isValid()
    {
        return is_string($this->guid) && $this->guid !== '';
    }
}
