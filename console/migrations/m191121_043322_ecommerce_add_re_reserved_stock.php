<?php

use yii\db\Migration;

/**
 * Class m191121_043322_ecommerce_add_product_model_name_stock
 */
class m191121_043322_ecommerce_add_re_reserved_stock extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_stock}}','reason_re_reserved',$this->string(64)->defaultValue('')->comment("Причина перерезерва")->after('condition_type'));
        $this->addColumn('{{%ecommerce_stock}}','order_re_reserved',$this->string(34)->defaultValue('')->comment("В каком заказе перерезерв")->after('reason_re_reserved'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}','reason_re_reserved');
        $this->dropColumn('{{%ecommerce_stock}}','order_re_reserved');
        return false;
    }
}