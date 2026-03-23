<?php

use yii\db\Migration;

/**
 * Class m191024_102518_ecommerce_outbound_add_field_client_StoreName
 */
class m191024_102518_ecommerce_outbound_add_field_client_StoreName extends Migration
{
    public function up()
    {
        $this->addColumn('{{%ecommerce_outbound}}','client_StoreName',$this->string(36)->defaultValue('')->comment("client Store Name")->after('client_GiftWrappingMessage'));
    }

    public function down()
    {
        $this->dropColumn('{{%ecommerce_outbound}}','client_StoreName');
        return false;
    }
}