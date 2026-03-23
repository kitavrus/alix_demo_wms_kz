<?php

use yii\db\Schema;
use yii\db\Migration;

class m141015_040605_add_field__created_updated_user_to_tlagents extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tl_agents}}','created_user_id',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Created user id"');
        $this->addColumn('{{%tl_agents}}','updated_user_id',Schema::TYPE_INTEGER . '(11) NULL COMMENT "Updated user id"');
    }

    public function down()
    {
        $this->dropColumn('{{%tl_agents}}','created_user_id');
        $this->dropColumn('{{%tl_agents}}','updated_user_id');
    }
}
