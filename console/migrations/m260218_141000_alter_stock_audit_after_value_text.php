<?php

use yii\db\Migration;
use yii\db\Schema;

class m260218_141000_alter_stock_audit_after_value_text extends Migration
{
    public function init()
    {
        $this->db = 'dbAudit';
        parent::init();
    }

    public function up()
    {
        $this->alterColumn('{{%stock_audit}}', 'after_value_text', Schema::TYPE_TEXT . " NULL COMMENT 'Value of attribute after modification'");
        $this->alterColumn('{{%stock_audit}}', 'before_value_text', Schema::TYPE_TEXT . " NULL COMMENT 'Value of attribute before modification'");
    }

    public function down()
    {
        // Возвращаем обратно в varchar(255)
        $this->alterColumn('{{%stock_audit}}', 'after_value_text', Schema::TYPE_STRING . " NULL COMMENT 'Value of attribute after modification'");
        $this->alterColumn('{{%stock_audit}}', 'before_value_text', Schema::TYPE_TEXT . " NULL COMMENT 'Value of attribute before modification'");
    }
}

