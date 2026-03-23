<?php

use yii\db\Schema;
use yii\db\Migration;

class m150804_144213_add_title_description_to_dp_orders extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_delivery_proposal_orders}}','title',Schema::TYPE_STRING . ' NULL DEFAULT NULL  AFTER `order_number`');
        $this->addColumn('{{%tl_delivery_proposal_orders}}','description',Schema::TYPE_TEXT . ' NULL DEFAULT NULL AFTER `title`');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_delivery_proposal_orders }}','title');
        $this->dropColumn('{{%tl_delivery_proposal_orders }}','description');
    }

}