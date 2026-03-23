<?php

use yii\db\Schema;
use yii\db\Migration;

class m140801_024502_add_column_first_name_middle_name_last_name_to_store_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%store}}','contact_first_name',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Contact first name" AFTER `name`');
        $this->addColumn('{{%store}}','contact_middle_name',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Contact middle name" AFTER `contact_first_name`');
        $this->addColumn('{{%store}}','contact_last_name',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Contact last name" AFTER `contact_middle_name`');

        $this->addColumn('{{%store}}','contact_first_name2',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Contact first name" AFTER `contact_last_name`');
        $this->addColumn('{{%store}}','contact_middle_name2',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Contact middle name" AFTER `contact_first_name2`');
        $this->addColumn('{{%store}}','contact_last_name2',Schema::TYPE_STRING . '(64) NOT NULL COMMENT "Contact last name" AFTER `contact_middle_name2`');
    }

    public function down()
    {
        $this->dropColumn('{{%store}}','contact_first_name');
        $this->dropColumn('{{%store}}','contact_middle_name');
        $this->dropColumn('{{%store}}','contact_last_name');

        $this->dropColumn('{{%store}}','contact_first_name2');
        $this->dropColumn('{{%store}}','contact_middle_name2');
        $this->dropColumn('{{%store}}','contact_last_name2');
    }
}
