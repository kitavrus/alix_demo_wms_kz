<?php

use yii\db\Schema;
use yii\db\Migration;

class m141107_051114_add_field_deleted_to_table_tl_delivery_proposal_billing extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_billing}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
    }

    public function down()
    {
        echo "m141107_051114_add_field_deleted_to_table_tl_delivery_proposal_billing cannot be reverted.\n";

        return false;
    }
}
