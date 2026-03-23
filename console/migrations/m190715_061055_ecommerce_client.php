<?php

use yii\db\Migration;

/**
 * Class m190715_061055_ecommerce_client
 */
class m190715_061055_ecommerce_client extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_client', [
            'id' => $this->primaryKey(),
            'status' => $this->smallInteger()->defaultValue(0)->comment("Status"),
            'legal_company_name' => $this->string(64)->defaultValue('')->comment("legal company name"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_client}}');
    }
}
