<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.11.14
 * Time: 10:46
 */

namespace common\modules\transportLogistics\components;

use Yii;
//use yii\helpers\ArrayHelper;
//use common\modules\store\models\Store;
//use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
//use common\modules\city\models\City;
//use common\modules\city\models\Country;
//use common\modules\city\models\Region;
//use common\modules\transportLogistics\models\TlDeliveryRoutes;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use app\modules\transportLogistics\transportLogistics;
use yii\helpers\VarDumper;

//use yii\helpers\VarDumper;

class TLManager {

    /*
    * Recalculate DP and Routes
    * @param integer $dpID $delivery_proposal_id
    * @param integer $dprID $delivery_proposal_route_id
    * @param array $recalculateParams Example: ['change_mckgnp'=>true,'change_price'=>false,'updateCascadedMcKgNp'=>true]
    * */
    public static function recalculateDpAndDpr($dpID,$dprID = null,$recalculateParams = [])
    {
//        if($dpr = TlDeliveryRoutes::findOne($dprID)) {
//            $dpr->recalculateExpensesRoute();
//        }

//        VarDumper::dump($recalculateParams,10,true);
//        die('-recalculateDpAndDpr-');

        if($dp = TlDeliveryProposal::findOne($dpID)) {
            $dp->recalculateExpensesOrder($recalculateParams);
            $dp->setCascadedStatus();
        }

        return true;
    }

} 