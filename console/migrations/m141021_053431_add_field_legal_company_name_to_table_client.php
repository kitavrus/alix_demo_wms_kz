<?php

use yii\db\Schema;
use yii\db\Migration;

class m141021_053431_add_field_legal_company_name_to_table_client extends Migration
{
    public function up()
    {
        $this->addColumn('{{%clients}}','legal_company_name',Schema::TYPE_STRING . '(128) NULL COMMENT "Legal company name" AFTER `username`');
    }

    public function down()
    {
        $this->dropColumn('{{%clients}}','legal_company_name');
    }
}
