<?php

use yii\db\Schema;
use yii\db\Migration;

class m150126_153458_add_columns_to_tl_billings extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `tl_delivery_proposal_billing` CHANGE `country_id` `from_country_id` INT( 11 ) NULL DEFAULT '0'");
        $this->execute("ALTER TABLE `tl_delivery_proposal_billing` CHANGE `region_id` `from_region_id` INT( 11 ) NULL DEFAULT '0'");
        $this->execute("ALTER TABLE `tl_delivery_proposal_billing` CHANGE `city_id` `from_city_id` INT( 11 ) NULL DEFAULT '0'");

        $this->addColumn('{{%tl_delivery_proposal_billing}}','to_country_id',Schema::TYPE_SMALLINT . ' DEFAULT 0 AFTER `from_city_id`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','to_region_id',Schema::TYPE_SMALLINT . ' DEFAULT 0 AFTER `to_country_id`');
        $this->addColumn('{{%tl_delivery_proposal_billing}}','to_city_id',Schema::TYPE_SMALLINT . ' DEFAULT 0 AFTER `to_region_id`');

    }

    public function down()
    {
        echo "m141104_081220_alter_field_floor_to_table_store cannot be reverted.\n";

        return false;
    }
}
