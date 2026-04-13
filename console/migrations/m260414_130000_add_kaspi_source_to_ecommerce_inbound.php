<?php

use yii\db\Migration;

/**
 * Добавляет поля для связи возврата с заказом Kaspi.
 *
 * Используется сценарием «частичный возврат после доставки» — оператор вручную
 * заводит inbound-возврат, ссылаясь на оригинальный Kaspi-заказ и код возврата.
 */
class m260414_130000_add_kaspi_source_to_ecommerce_inbound extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_inbound}}', 'source_kaspi_order_id', $this->string(64)->null()->comment('ID оригинального Kaspi-заказа (для возвратов)'));
        $this->addColumn('{{%ecommerce_inbound}}', 'source_kaspi_refund_code', $this->string(64)->null()->comment('Код возврата на стороне Kaspi'));

        $this->createIndex('idx_ecommerce_inbound_kaspi_order_id', '{{%ecommerce_inbound}}', 'source_kaspi_order_id');
    }

    public function down()
    {
        $this->dropIndex('idx_ecommerce_inbound_kaspi_order_id', '{{%ecommerce_inbound}}');
        $this->dropColumn('{{%ecommerce_inbound}}', 'source_kaspi_refund_code');
        $this->dropColumn('{{%ecommerce_inbound}}', 'source_kaspi_order_id');
    }
}
