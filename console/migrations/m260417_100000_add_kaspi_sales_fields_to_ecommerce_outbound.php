<?php

use yii\db\Migration;

/**
 * Поля под sales-flow Kaspi → EcommerceOutbound:
 *  - индекс по external_order_number для быстрой идемпотентной вставки
 *    при poll из Kaspi (уникальность не ставим: исторически поле может быть пустым
 *    у заказов не из Kaspi);
 *  - поля для трекинга синхронизации с 1С.
 */
class m260417_100000_add_kaspi_sales_fields_to_ecommerce_outbound extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%ecommerce_outbound}}',
            'external_kaspi_status',
            $this->string(32)->defaultValue('')->comment('Локальный снимок статуса заказа в Kaspi')
        );
        $this->addColumn(
            '{{%ecommerce_outbound}}',
            'sent_to_1c_at',
            $this->integer()->null()->comment('Unix-timestamp отправки продажи в 1С')
        );
        $this->addColumn(
            '{{%ecommerce_outbound}}',
            'one_c_status',
            $this->string(32)->defaultValue('')->comment('Статус передачи в 1С: PENDING/SENT/ERROR')
        );
        $this->addColumn(
            '{{%ecommerce_outbound}}',
            'one_c_response',
            $this->text()->null()->comment('Ответ/ошибка от 1С')
        );

        $this->createIndex(
            'idx_ecommerce_outbound_external_order_number',
            '{{%ecommerce_outbound}}',
            'external_order_number'
        );
        $this->createIndex(
            'idx_ecommerce_outbound_external_kaspi_status',
            '{{%ecommerce_outbound}}',
            'external_kaspi_status'
        );
        $this->createIndex(
            'idx_ecommerce_outbound_one_c_status',
            '{{%ecommerce_outbound}}',
            'one_c_status'
        );
    }

    public function down()
    {
        $this->dropIndex('idx_ecommerce_outbound_one_c_status', '{{%ecommerce_outbound}}');
        $this->dropIndex('idx_ecommerce_outbound_external_kaspi_status', '{{%ecommerce_outbound}}');
        $this->dropIndex('idx_ecommerce_outbound_external_order_number', '{{%ecommerce_outbound}}');
        $this->dropColumn('{{%ecommerce_outbound}}', 'one_c_response');
        $this->dropColumn('{{%ecommerce_outbound}}', 'one_c_status');
        $this->dropColumn('{{%ecommerce_outbound}}', 'sent_to_1c_at');
        $this->dropColumn('{{%ecommerce_outbound}}', 'external_kaspi_status');
        return true;
    }
}
