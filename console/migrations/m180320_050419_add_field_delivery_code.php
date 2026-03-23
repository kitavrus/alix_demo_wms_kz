<?php

use yii\db\Migration;

/**
 * Class m180320_050419_add_field_delivery_code
 */
class m180320_050419_add_field_delivery_code extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store_reviews}}','delivery_code',$this->string()->defaultValue('')->comment("Delivery secret code")->after('delivery_datetime'));
    }

    public function down()
    {
        $this->dropColumn('{{%store_reviews}}','delivery_code');
        return false;
    }
}
