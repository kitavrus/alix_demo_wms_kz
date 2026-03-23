<?php

use yii\db\Migration;
use yii\db\Schema;
/**
 * Handles the creation for table `table_products`.
 */
class m161021_063232_create_table_products extends Migration
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
        $this->createTable('products', [
            'id' => $this->primaryKey(),
            'SkuId' => $this->bigInteger()->defaultValue(0)->comment("Sku Id"),
            'LotOrSingleBarcode' => $this->string(18)->defaultValue('')->comment("LotOrSingleBarcode"),
            'ShortCode' => $this->string(28)->defaultValue('')->comment("Short Code"),
            'Description' => $this->string(128)->defaultValue('')->comment("Description"),
            'Note' => $this->string(16)->defaultValue('')->comment("Note"),
            'LotSingle' => $this->integer()->defaultValue(0)->comment("Lot Single"),
            'Classification' => $this->bigInteger()->defaultValue(0)->comment("Classification"),
            'Color' => $this->string(16)->defaultValue('')->comment("Color"),
            'FDate' => $this->string(68)->defaultValue('')->comment("FDate"),
            'Perc' => $this->integer()->defaultValue(0)->comment("Perc"),
            'Origin' => $this->integer()->defaultValue(0)->comment("Origin"),
            'ProcessTime' => $this->string(68)->defaultValue('')->comment("ProcessTime"),

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
        $this->dropTable('products');
    }
}
