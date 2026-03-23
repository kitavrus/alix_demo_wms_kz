<?php

namespace stockDepartment\modules\stock\models;
use common\modules\stock\models\Stock;
use Yii;

/**
 * This is the model class for table "stock_extra_fields".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $field_name
 * @property string $field_value
 * @property integer $date_created
 * @property integer $created_by
 */
class StockExtraField extends \common\modules\stock\models\StockExtraField
{

}