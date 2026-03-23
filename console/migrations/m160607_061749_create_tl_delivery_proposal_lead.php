<?php

use yii\db\Migration;

/**
 * Handles the creation for table `tl_delivery_proposal_lead`.
 */
class m160607_061749_create_tl_delivery_proposal_lead extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('tl_delivery_proposal_lead', [
            'id' => $this->primaryKey(),
            'tl_delivery_proposal_id' => $this->integer()->comment("Delivery proposal id"),
            'from_city_id' => $this->integer()->comment("From city"),
            'to_city_id' => $this->integer()->comment("To city"),

            'status' => $this->smallInteger()->comment("Status"),
            'price' => $this->decimal(26,3)->defaultValue(0)->comment("Price"),

            'm3' => $this->decimal(26,3)->defaultValue(0)->comment("M3"),
            'kg' => $this->decimal(26,3)->defaultValue(0)->comment("Kg"),

            'name' => $this->string(128)->defaultValue('')->comment("Name"),
            'phone' => $this->string(128)->defaultValue('')->comment("Name"),
            'comment' => $this->text()->defaultValue('')->comment("Comment"),

            'created_user_id' => $this->integer()->defaultValue(0),
            'updated_user_id' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->defaultValue(0),
            'updated_at' => $this->integer()->defaultValue(0),
            'deleted' => $this->integer()->defaultValue(0),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tl_delivery_proposal_lead');
    }
}
