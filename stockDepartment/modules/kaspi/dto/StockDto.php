<?php

namespace stockDepartment\modules\kaspi\dto;

/**
 * @property string $sku Артикул товара
 * @property string $title Название товара
 * @property string $brand Бренд
 * @property string $category Категория
 * @property string|null $description Описание товара
 * @property array $images Массив изображений (урлы)
 * @property AttributeDto[] $attributes Атрибуты (Код атрибута, Значение))
 * Пример атрибута
 * {
 *  "code": "Exercise notebooks*Obsie harakteristiki.exercise notebooks*type",
 *  "value": "тетрадь-блокнот"
 * }
 */
class StockDto
{
    public $sku;
    public $title;
    public $brand;
    public $category;
    public $description;
    public $images;
    public $attributes;

    /**
     * @param string $sku Артикул товара
     * @param string $title Название товара
     * @param string $brand Бренд
     * @param string $category Категория
     * @param string|null $description Описание
     * @param array $images Массив изображений
     * @param AttributeDto[] $attributes Массив атрибутов
     */
    public function __construct(
        $sku,
        $title,
        $brand,
        $category,
        $description = null,
        array $images = [],
        array $attributes = []
    ) {
        $this->sku = $sku;
        $this->title = $title;
        $this->brand = $brand;
        $this->category = $category;
        $this->description = $description;
        $this->images = $images;
        $this->attributes = $attributes;
    }

    /**
     * Преобразует объект в массив для API
     * @return array
     */
    public function toArray()
    {
        return [
            'sku' => $this->sku,
            'title' => $this->title,
            'brand' => $this->brand,
            'category' => $this->category,
            'description' => $this->description,
            'images' => $this->images,
            'attributes' => array_map(function(AttributeDto $attr) {
                return $attr->toArray();
            }, $this->attributes)
        ];
    }
}

