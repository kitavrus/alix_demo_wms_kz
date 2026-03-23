<?php

namespace stockDepartment\modules\other\models;

use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\stock\models\Stock;
use yii\db\Query;
use yii\helpers\VarDumper;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ArrayDataProvider;

/**
 * StockSearch represents the model behind the search form about `common\modules\stock\models\Stock`.
 */
class StockRemains extends Stock
{
    public $qty;

}
