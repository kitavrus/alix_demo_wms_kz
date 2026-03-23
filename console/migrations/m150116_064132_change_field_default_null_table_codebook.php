<?php

use yii\db\Schema;
use yii\db\Migration;

class m150116_064132_change_field_default_null_table_codebook extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `codebook` CHANGE `cod_prefix` `cod_prefix` varchar(4) NULL DEFAULT NULL");
        $this->execute("ALTER TABLE `codebook` CHANGE `count_cell` `count_cell` INT(11) NULL DEFAULT NULL");
        $this->execute("ALTER TABLE `codebook` CHANGE `name` `name` varchar(128) NULL DEFAULT NULL");
        $this->execute("ALTER TABLE `codebook` CHANGE `barcode` `barcode` INT( 11 ) NULL DEFAULT '0'");
    }

    public function down()
    {
        echo "m150116_064132_change_field_count_cell_table_codebook cannot be reverted.\n";

        return false;
    }
}