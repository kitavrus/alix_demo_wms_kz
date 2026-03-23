<?php

use yii\db\Migration;

/**
 * Class m190814_162538_ecommerce_get_in_bound_data_response_api_log
 */
class m190814_162538_ecommerce_get_in_bound_data_response_api_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('ecommerce_get_inbound_data_response', [
            'id' => $this->primaryKey(),
            'our_inbound_id' => $this->integer(11)->comment(''),
            'get_inbound_data_id' => $this->integer(11)->comment(''),
            'InboundId' => $this->string(64)->defaultValue('')->comment(''),
            'FromBusinessUnitId' => $this->string(64)->defaultValue('')->comment(''),
            'LcOrCartonLabel' => $this->string(64)->defaultValue('')->comment(''),
            'NumberOfCartons' => $this->string(64)->defaultValue('')->comment(''),
            'SkuId' => $this->string(64)->defaultValue('')->comment(''),
            'LotOrSingleBarcode' => $this->string(64)->defaultValue('')->comment(''),
            'LotOrSingleQuantity' => $this->string(64)->defaultValue('')->comment(''),
            'Status' => $this->string(64)->defaultValue('')->comment(''),
            'ToBusinessUnitId' => $this->string(64)->defaultValue('')->comment(''),
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
        $this->dropTable('{{%ecommerce_get_inbound_data_response}}');
    }
}
