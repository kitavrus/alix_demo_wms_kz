<?php

use yii\db\Migration;

/**
 * Таймстамп успешного скачивания Kaspi-этикетки для заказа.
 *
 * Kaspi может отдавать PDF-накладную не сразу после ASSEMBLE — клиент часто
 * получает 4xx/пустой ответ на первый getShippingLabel и должен забрать
 * этикетку позже. Это поле нужно, чтобы:
 *  1) на форме листа отгрузки показывать список заказов, ждущих этикетку,
 *  2) не дёргать Kaspi повторно по уже скачанным заказам.
 *
 * Хранится как UNIX timestamp INT NULL — формат совместим с остальными
 * датами в этой таблице (packing_date, date_left_warehouse и т.д.).
 */
class m260513_100000_add_kaspi_label_fetched_at_to_ecommerce_outbound extends Migration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE {{%ecommerce_outbound}} "
            . "ADD COLUMN `kaspi_label_fetched_at` INT(11) NULL DEFAULT NULL "
            . "COMMENT 'UNIX ts момента успешного скачивания Kaspi-накладной'"
        );
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}', 'kaspi_label_fetched_at');
        return true;
    }
}
