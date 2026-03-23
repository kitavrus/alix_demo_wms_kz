<?php

use yii\db\Schema;
use yii\db\Migration;

class m140919_063427_add_field_is_client_confirmed_to_table_tl_delivery_proposal extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposals}}','is_client_confirmed',Schema::TYPE_SMALLINT . ' NULL COMMENT "If dp created our operator" AFTER `client_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposals}}','is_client_confirmed');
    }
}
