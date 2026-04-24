<?php

use yii\db\Migration;

/**
 * Поля для связи EcommerceReturn с Kaspi-заказом (poll возвратов).
 * Ставятся при cron/kaspi-poll-returns, когда Kaspi переводит заказ
 * в статус KASPI_DELIVERY_RETURN_REQUESTED.
 */
class m260425_100000_add_kaspi_source_to_ecommerce_return extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%ecommerce_return}}',
            'source_kaspi_order_id',
            $this->string(64)->null()->comment('Kaspi orderId, по которому создан возврат')
        );
        $this->addColumn(
            '{{%ecommerce_return}}',
            'source_kaspi_refund_code',
            $this->string(64)->null()->comment('Код возврата Kaspi (refund code)')
        );

        $this->createIndex(
            'idx_ecommerce_return_source_kaspi_order_id',
            '{{%ecommerce_return}}',
            'source_kaspi_order_id'
        );
    }

    public function down()
    {
        $this->dropIndex('idx_ecommerce_return_source_kaspi_order_id', '{{%ecommerce_return}}');
        $this->dropColumn('{{%ecommerce_return}}', 'source_kaspi_refund_code');
        $this->dropColumn('{{%ecommerce_return}}', 'source_kaspi_order_id');
        return true;
    }
}
