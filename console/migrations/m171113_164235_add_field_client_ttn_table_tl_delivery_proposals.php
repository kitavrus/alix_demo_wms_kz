<?php

use yii\db\Migration;

class m171113_164235_add_field_client_ttn_table_tl_delivery_proposals extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','client_ttn',$this->string(16)->defaultValue('')->comment("Client TTN")->after('extra_fields'));
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','client_ttn');
        return false;
    }
}