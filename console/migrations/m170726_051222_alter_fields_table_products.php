<?php

use yii\db\Migration;

class m170726_051222_alter_fields_table_products extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `product`
CHANGE `weight_brutto` `weight_brutto` decimal(9,3) NULL DEFAULT '0.000' COMMENT 'Weight brutto' AFTER `price`,
CHANGE `weight_netto` `weight_netto` decimal(9,3) NULL DEFAULT '0.000' COMMENT 'Weight netto' AFTER `weight_brutto`,
CHANGE `m3` `m3` decimal(9,3) NULL DEFAULT '0.000' COMMENT 'The Value' AFTER `weight_netto`,
CHANGE `length` `length` decimal(9,3) NULL DEFAULT '0.000' COMMENT 'Length' AFTER `m3`,
CHANGE `width` `width` decimal(9,3) NULL DEFAULT '0.000' COMMENT 'Width' AFTER `length`,
CHANGE `height` `height` decimal(9,3) NULL DEFAULT '0.000' COMMENT 'Height' AFTER `width`;");
    }

    public function down()
    {
        echo "m170726_051222_alter_fields_table_products cannot be reverted.\n";

        return false;
    }
}