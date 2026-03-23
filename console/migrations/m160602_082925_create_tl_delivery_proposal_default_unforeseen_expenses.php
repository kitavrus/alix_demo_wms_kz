<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_tl_delivery_proposal_default_unforeseen_expenses`.
 */
class m160602_082925_create_tl_delivery_proposal_default_unforeseen_expenses extends Migration
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

        $this->createTable('tl_delivery_proposal_default_unforeseen_expenses', [
            'id' => $this->primaryKey(),
            'tl_delivery_proposal_default_route_id' => $this->integer()->comment("Delivery proposal route id"),
            'tl_delivery_proposal_default_sub_route_id' => $this->integer()->comment("Delivery proposal sub route id"),
            'type_id' => $this->integer()->defaultValue(0)->comment("Type unforeseen expenses id"),
            'name' => $this->string(255)->comment("Name"),
            'who_pays' => $this->smallInteger()->comment("Who pays"),
            'price_cache' => $this->decimal(26,3)->defaultValue(0)->comment("Price expenses"),
            'cash_no' => $this->smallInteger()->defaultValue(0)->comment("Nal/bez"),
            'price_with_vat' => $this->decimal(26,3)->defaultValue(0)->comment("C NDS"),
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
        $this->dropTable('tl_delivery_proposal_default_unforeseen_expenses');
    }
}