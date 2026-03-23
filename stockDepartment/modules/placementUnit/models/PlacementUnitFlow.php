<?php

namespace app\modules\placementUnit\controllers;

use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "placement_unit_flow".
 *
 * @property integer $id
 * @property integer $count_unit
 * @property integer $client_id
 * @property integer $stock_id
 * @property integer $zone_id
 * @property integer $inbound_order_id
 * @property integer $inbound_order_item_id
 * @property integer $outbound_order_id
 * @property integer $outbound_order_item_id
 * @property integer $placement_unit_barcode_id
 * @property string $placement_unit_barcode
 * @property integer $product_id
 * @property string $product_barcode
 * @property string $product_model
 * @property string $product_name
 * @property string $product_sku
 * @property integer $product_qty
 * @property integer $status
 * @property string $to_rack_address
 * @property string $to_pallet_address
 * @property string $to_box_address
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class PlacementUnitFlow extends \common\modules\placementUnit\models\PlacementUnitFlow
{

}
