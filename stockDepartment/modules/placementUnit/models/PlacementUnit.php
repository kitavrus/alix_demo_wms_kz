<?php

namespace app\modules\placementUnit\models;

use Yii;
use common\models\ActiveRecord;

/**
 * This is the model class for table "placement_unit".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $zone_id
 * @property integer $count_unit
 * @property integer $type_inout
 * @property string $barcode
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class PlacementUnit extends \common\modules\placementUnit\models\PlacementUnit
{

}
