<?php

use yii\db\Schema;
use yii\db\Migration;

class m150304_120443_add_description_and_declared_value_to_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','declared_value', Schema::TYPE_DECIMAL . '(26,2) NULL DEFAULT "0" comment "Declared value of shipment" AFTER `number_places_actual`');
        $this->addColumn('{{%tl_delivery_proposals}}','shipment_description', Schema::TYPE_STRING . ' NULL AFTER `declared_value`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','declared_value');
        $this->dropColumn('{{%tl_delivery_proposals}}','shipment_description');

        return true;
    }
}
