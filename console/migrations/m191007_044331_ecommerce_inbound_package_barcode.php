<?php
use yii\db\Migration;

/**
 * Class m191007_044331_ecommerce_inbound_package_barcode
 */
class m191007_044331_ecommerce_inbound_package_barcode extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ecommerce_inbound_place_barcode', [
            'id' => $this->primaryKey(),
            'barcode' => $this->string(16)->defaultValue('')->comment("Place barcode"),

            'created_user_id' =>$this->integer(11)->defaultValue(null)->comment("Created user id"),
            'updated_user_id' =>$this->integer(11)->defaultValue(null)->comment("Updated user id"),
            'created_at' =>$this->integer(11)->defaultValue(null)->comment("Created at"),
            'updated_at' =>$this->integer(11)->defaultValue(null)->comment("Updated at"),
            'deleted' => $this->smallInteger()->defaultValue(0)->comment("Deleted"),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('ecommerce_inbound_place_barcode');
    }
}