<?php

use yii\db\Migration;

class m260323_120000_add_kaspi_status_field_to_stock_table extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%ecommerce_stock}}',
            'kaspi_stock_status',
            $this->string(32)->defaultValue('')->comment('Kaspi stock sync status')
        );
        $this->addColumn(
            '{{%ecommerce_stock}}',
            'kaspi_order_status',
            $this->string(32)->defaultValue('')->comment('Kaspi order status')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_stock}}', 'kaspi_order_status');
        $this->dropColumn('{{%ecommerce_stock}}', 'kaspi_stock_status');
        return true;
    }
}

