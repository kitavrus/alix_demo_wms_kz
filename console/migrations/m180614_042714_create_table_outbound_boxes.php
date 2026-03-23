<?php

use yii\db\Migration;

/**
 * Class m180614_042714_create_table_outbound_boxes
 */
class m180614_042714_create_table_outbound_boxes extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('outbound_boxes', [
            'id' => $this->primaryKey(),
            'our_box' => $this->string(13)->defaultValue('')->comment("Our box"),
            'client_box' => $this->string(13)->defaultValue('')->comment("Client box"),
            'client_extra_json' => $this->text()->defaultValue('')->comment("Client extra json data"),
            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);

        $this->execute('ALTER TABLE `outbound_boxes` ADD INDEX `our_box` (`our_box`), ADD INDEX `client_box` (`client_box`);');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('outbound_boxes');
    }
}