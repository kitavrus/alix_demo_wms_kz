<?php

use yii\db\Migration;

/**
 * Момент, когда Kaspi API подтвердил перевод заказа в RETURNED (фискальная
 * регистрация возврата на стороне Kaspi). Ставится при успешном вызове
 * changeOrderStatus($orderId, RETURNED) после «Принять» на форме возврата.
 *
 * Отличается от date_confirm (локальное подтверждение оператором до вызова API).
 */
class m260426_100000_add_kaspi_returned_at_to_ecommerce_return extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%ecommerce_return}}',
            'kaspi_returned_at',
            $this->integer()->null()->comment('Timestamp, когда Kaspi подтвердил RETURNED')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_return}}', 'kaspi_returned_at');
        return true;
    }
}
