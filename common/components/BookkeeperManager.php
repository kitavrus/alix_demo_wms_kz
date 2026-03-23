<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 10:49
 */
namespace common\components;

use common\modules\bookkeeper\models\Bookkeeper;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\product\models\Product;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use common\modules\product\models\ProductBarcodes;
use common\components\DeliveryProposalManager;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class BookkeeperManager extends Component
{
    /*
     *
     * */
    public static function showBalance()
    {
        return Bookkeeper::find()->select('balance_sum')->andWhere(['status'=>[Bookkeeper::STATUS_MONEY_GIVEN,Bookkeeper::STATUS_MONEY_RECEIVED,Bookkeeper::STATUS_DONE]])->orderBy(['sort_order'=>SORT_DESC])->scalar();
    }

    /*
     * @param boolean $fullRecalculate  Default false
     * */
    public static function recalculateBalance($fullRecalculate = false)
    {
        $all = Bookkeeper::find()->orderBy(['date_at' => SORT_ASC])->all();
        $i = 0;
        foreach($all as $v) {
            $v->sort_order = $i++;
            $v->save(false);
        }

        $balanceSum = 0;
        $items = Bookkeeper::find()->andWhere(['status' => [Bookkeeper::STATUS_MONEY_GIVEN,Bookkeeper::STATUS_MONEY_RECEIVED,Bookkeeper::STATUS_DONE]])->orderBy(['sort_order' => SORT_ASC])->all();

        if($items) {
            foreach($items as $item) {
                if ($item->type_id == Bookkeeper::TYPE_PLUS) {
                    $balanceSum += $item->price;
                } elseif ($item->type_id == Bookkeeper::TYPE_MINUS) {
                    $balanceSum -= $item->price;
                }
                $item->balance_sum = $balanceSum;
                $item->save(false);
            }
        }
    }

    /*
     * @param array $attributes
     * @return void
     * */
    public static function createOrUpdate($attributes = [])
    {
        if(!empty($attributes)) {
            $unique_key = ArrayHelper::getValue($attributes,'unique_key');

            if(!($m = Bookkeeper::find()->andWhere(['unique_key'=>$unique_key])->one())) {
                $m = new Bookkeeper();
            }

            $m->setAttributes($attributes,false);
            $m->save(false);
        }
        self::recalculateBalance();
        return true;
    }

    /*
     * Update Delivery proposal route unforeseen expenses
     * @param model Bookkeeper $bookkeeper
     * @return model TlDeliveryProposalRouteUnforeseenExpenses
     * */
    public static function updateDpRouteUnforeseenExpenses(Bookkeeper $bookkeeper)
    {
        if($obj = TlDeliveryProposalRouteUnforeseenExpenses::findOne($bookkeeper->tl_delivery_proposal_route_unforeseen_expenses_id)) {
            $obj->price_cache = $bookkeeper->price;
            $obj->save(false);
            $dpManager = new DeliveryProposalManager(['id'=>$obj->tl_delivery_proposal_id]);
            $dpManager->onChangeRoute();
        }

        return $obj;
    }

    /*
     * @param string $unique_key
     * */
    public static function deleteByUniqueKey($uniqueKey)
    {
        if(!empty($uniqueKey)) {
           if($m = Bookkeeper::find()->andWhere(['unique_key' => $uniqueKey])->one()) {
              $m->deleted = 1;
              $m->save(false);
               return 1;
           }
        }
        return 0;
    }

}