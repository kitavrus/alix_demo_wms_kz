<?php

use yii\db\Schema;
use yii\db\Migration;

class m150401_092821_add_column_extra_fields_table_outbound_upload_log extends Migration
{
    public function up()
    {
        $this->addColumn('{{%outbound_upload_log}}','extra_fields',Schema::TYPE_TEXT . ' NULL DEFAULT "" COMMENT "Example JSON: order_number, who received order, etc ..." AFTER `data_created_on_client`');
    }

    public function down()
    {
        $this->dropColumn('{{%outbound_upload_log}}','extra_fields');
    }
}
