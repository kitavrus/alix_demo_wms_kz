<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_colors`.
 */
class m170215_090545_create_table_colors extends Migration
{
    public function init()
    {
        $this->db = 'dbDefactoSpecial';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('colors', [
            'id' => $this->primaryKey(),
            'cod' => $this->string(16)->defaultValue('')->comment("color cod: ZP6"),
            'title' => $this->string(64)->defaultValue('')->comment("color title: GREEN"),
            'created_user_id' =>$this->integer()->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer()->defaultValue(null)->comment("Updated user id"),

            'created_at' =>$this->integer()->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer()->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('colors');
    }
}
