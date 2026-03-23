<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_consignment_universal_audit`.
 */
class m160505_051627_create_table_consignment_universal_audit extends Migration
{
    public function init()
    {
        $this->db = 'dbAudit';
        parent::init();
    }
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('consignment_universal_audit', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->comment("Modified object id"),
            'date_created' => $this->dateTime()->comment("Modification timestamp"),
            'created_by' =>$this->integer()->comment("Modified user_id"),
            'field_name' => $this->string(64)->defaultValue('')->comment("Modified object attribute name"),
            'before_value_text' => $this->text()->defaultValue('')->comment("Value of attribute before modification"),
            'after_value_text' => $this->text()->defaultValue('')->comment("Value of attribute after modification"),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('consignment_universal_audit');
    }
}