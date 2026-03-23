<?php

use yii\db\Schema;
use yii\db\Migration;

class m141231_061853_add_field_manager_type__department_table_employees extends Migration
{
    public function up()
    {
        $this->addColumn('{{%employees}}','manager_type', Schema::TYPE_SMALLINT . ' NULL DEFAULT "0" COMMENT "Manager type: Director, simple manager, etc ..." AFTER `email`');
        $this->addColumn('{{%employees}}','department', Schema::TYPE_SMALLINT . ' NULL DEFAULT "0" COMMENT "Department: Stock, office, etc ..." AFTER `manager_type`');
    }

    public function down()
    {
        $this->dropColumn('{{%employees}}','manager_type');
        $this->dropColumn('{{%employees}}','department');
    }
}
