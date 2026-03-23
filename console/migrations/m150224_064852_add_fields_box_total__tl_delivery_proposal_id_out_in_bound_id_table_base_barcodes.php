<?php

use yii\db\Schema;
use yii\db\Migration;

class m150224_064852_add_fields_box_total__tl_delivery_proposal_id_out_in_bound_id_table_base_barcodes extends Migration
{
    public function up()
    {
        $this->addColumn('{{%base_barcodes}}','box_total', Schema::TYPE_STRING . '(24) NULL DEFAULT "" AFTER `box_barcode`');
        $this->addColumn('{{%base_barcodes}}','tl_delivery_proposal_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" AFTER `id`');
        $this->addColumn('{{%base_barcodes}}','outbound_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" AFTER `tl_delivery_proposal_id`');
        $this->addColumn('{{%base_barcodes}}','inbound_id', Schema::TYPE_INTEGER . '(11) NULL DEFAULT "0" AFTER `outbound_id`');
    }

    public function down()
    {
        $this->dropColumn('{{%base_barcodes}}','box_total');
        $this->dropColumn('{{%base_barcodes}}','tl_delivery_proposal_id');
        $this->dropColumn('{{%base_barcodes}}','outbound_id');
        $this->dropColumn('{{%base_barcodes}}','inbound_id');

        return false;
    }
}
