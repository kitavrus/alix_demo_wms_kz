<?php

use yii\db\Migration;

/**
 * Флаг «заказ на Kaspi-доставку» в ecommerce_outbound.
 *
 * Используется для гейта транзита в Kaspi: transferToCourier (ASSEMBLE) имеет
 * смысл только для isKaspiDelivery=true. Для own-delivery / самовывоза
 * запрос waybill у Kaspi не нужен — Kaspi отдаст 400.
 *
 * Источник значения — поле attributes.isKaspiDelivery в ответе Kaspi API v2,
 * гидрируется в OrderDto::isKaspiDelivery и сохраняется при импорте
 * в OrderImportService::createOutbound.
 */
class m260422_100000_add_is_kaspi_delivery_to_ecommerce_outbound extends Migration
{
    public function up()
    {
        // Через raw SQL, т.к. Schema\Builder::tinyInteger() появился только в Yii 2.0.14
        // (у нас версия старее). Поведение то же: TINYINT(1) NOT NULL DEFAULT 0.
        $this->execute(
            "ALTER TABLE {{%ecommerce_outbound}} "
            . "ADD COLUMN `is_kaspi_delivery` TINYINT(1) NOT NULL DEFAULT 0 "
            . "COMMENT '1 если заказ на Kaspi-доставку (isKaspiDelivery из Kaspi API)'"
        );
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}', 'is_kaspi_delivery');
        return true;
    }
}
