<?php

use yii\db\Migration;

/**
 * Handles adding field_from to table `points_table_return_order_items`.
 */
class m161015_183044_add_field_from_to_points_table_return_order_items extends Migration
{
    public function up()
    {
        $this->addColumn('{{%return_order_items}}','from_point_id',$this->integer()->defaultValue(0)->comment("Our from shop id")->after('client_box_barcode'));
        $this->addColumn('{{%return_order_items}}','from_point_client_id',$this->string(64)->defaultValue('')->comment("Client from shop code")->after('from_point_id'));

        $this->addColumn('{{%return_order_items}}','to_point_id',$this->integer()->defaultValue(0)->comment("Our to shop id")->after('from_point_client_id'));
        $this->addColumn('{{%return_order_items}}','to_point_client_id',$this->string(64)->defaultValue('')->comment("Client to shop code")->after('to_point_id'));
    }

    public function down()
    {
        $this->dropColumn('{{%return_order_items}}','from_point_id');
        $this->dropColumn('{{%return_order_items}}','from_point_client_id');
        $this->dropColumn('{{%return_order_items}}','to_point_id');
        $this->dropColumn('{{%return_order_items}}','to_point_client_id');
        return false;
    }
}