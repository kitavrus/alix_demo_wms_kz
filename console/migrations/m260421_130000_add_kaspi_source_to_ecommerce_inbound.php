<?php

use yii\db\Migration;

/**
 * Поля под возвратный inbound Kaspi (OrderReturnService).
 * Пишутся при poll Kaspi-возвратов и при ручном /orders/<id>/partial-return.
 */
class m260421_130000_add_kaspi_source_to_ecommerce_inbound extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%ecommerce_inbound}}',
            'source_kaspi_order_id',
            $this->string(64)->null()->comment('Kaspi orderId, по которому создан возвратный inbound')
        );
        $this->addColumn(
            '{{%ecommerce_inbound}}',
            'source_kaspi_refund_code',
            $this->string(64)->null()->comment('Код возврата Kaspi (refund code)')
        );

        $this->createIndex(
            'idx_ecommerce_inbound_source_kaspi_order_id',
            '{{%ecommerce_inbound}}',
            'source_kaspi_order_id'
        );
    }

    public function down()
    {
        $this->dropIndex('idx_ecommerce_inbound_source_kaspi_order_id', '{{%ecommerce_inbound}}');
        $this->dropColumn('{{%ecommerce_inbound}}', 'source_kaspi_refund_code');
        $this->dropColumn('{{%ecommerce_inbound}}', 'source_kaspi_order_id');
        return true;
    }
}
