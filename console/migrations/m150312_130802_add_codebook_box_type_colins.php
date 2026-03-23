<?php

use yii\db\Schema;
use yii\db\Migration;
use common\modules\codebook\models\Codebook;

class m150312_130802_add_codebook_box_type_colins extends Migration
{
    public function up()
    {
        $this->insert('codebook', ['cod_prefix'=>'b', 'name'=>'Короб Colins (старый)', 'count_cell'=>0, 'status'=>Codebook::STATUS_ACTIVE, 'base_type'=>Codebook::BASE_TYPE_BOX_COLINS_OLD]);
    }

    public function down()
    {
        echo "m150312_130802_add_codebook_box_type_colins cannot be reverted.\n";

        return false;
    }
}
