<?php

use yii\db\Migration;

/**
 * Class m190914_101429_ecommerce_get_cargo_label_response
 */
class m190914_101429_ecommerce_get_cargo_label_response extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_get_cargo_label_response', [
            'id' => $this->primaryKey(),
            'our_outbound_id' => $this->integer(11)->comment(''),
            'ExternalShipmentId' => $this->string(512)->defaultValue('')->comment(''),
            'ShipmentId' => $this->string(512)->defaultValue('')->comment(''),

            'FileExtension' => $this->string(512)->defaultValue('')->comment(''),
            'FileData' => $this->text()->defaultValue('')->comment(''),
            'TrackingNumber' => $this->string(512)->defaultValue('')->comment(''),
            'TrackingUrl' => $this->string(512)->defaultValue('')->comment(''),
            'ReferenceNumber' => $this->string(512)->defaultValue('')->comment(''),
            'PageSize' => $this->string(512)->defaultValue('')->comment(''),

            'error_message' => $this->text()->defaultValue('')->comment(''),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%ecommerce_get_cargo_label_response}}');
    }
}