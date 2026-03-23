<?php

use yii\db\Schema;
use yii\db\Migration;

class m141004_111512_add_deleted_to_all_tables extends Migration
{
    public function up()
    {
        $this->addColumn('{{%city}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%client}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%codebook}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%country}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%inbound_orders}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%inbound_order_items}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%order_process}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%outbound_orders}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%outbound_order_items}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%product}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%product_barcodes}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%region}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%store}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%store_reviews}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%sync_products}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_agents}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_cars}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposals}}','deleted',Schema::TYPE_SMALLINT .  ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_orders}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_order_extras}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_routes}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_routes_car}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_route_cars}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_route_orders}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_delivery_proposal_route_unforeseen_expenses}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_order}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%tl_order_items}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
        $this->addColumn('{{%warehouse}}','deleted',Schema::TYPE_SMALLINT . ' DEFAULT 0');
    }

    public function down()
    {

    }
}
