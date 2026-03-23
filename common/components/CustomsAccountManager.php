<?php
namespace common\components;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use common\modules\broker\models\CustomsAccount;
use common\modules\broker\models\CustomsAccountCost;




class CustomsAccountManager extends Component
{
    /*
     * Current Delivery Proposal
     * */
    private $_customAccountID;


    /*
     * init data
     * @param integer $id DeliveryProposal id
      * */
    public function __construct($config)
    {
        $this->_customAccountID = $config['id'];
        parent::__construct();
    }

    public function onUpdateCustom()
    {
        return $this->recalculateCustomExpensions()
                    ->recalculateCustomCost();

    }



    /*
     * Высчитываем стоимость таможенного счета
     * @return self
     **/
    public function recalculateCustomCost()
    {
        $ca = $this->findCustomAccount();
        //$clientCost = 0;
        $clientCostNDS = 0;

        if($cac = $ca->costs){

            foreach ($cac as $cost){
                //$clientCost += $cost->price_cost_client;
                if($cost->cost_type == CustomsAccountCost::COST_TYPE_ACCOUNTABLE){
                    $clientCostNDS += $cost->price_nds_cost_client;
                }

            }

        }
        //$ca->price = $clientCost;
        $ca->price_nds = $clientCostNDS;
        //$ca->price_profit = $ca->price ?  $ca->price - $ca->price_expenses_total : $ca->price_nds - $ca->price_expenses_total;
        $ca->price_profit = $ca->price_nds - $ca->price_expenses_total;
        $ca->save(false);

        return $this;
    }

    /*
    * Высчитывает расходы таможенного счета
    * @return self
    **/
    public function recalculateCustomExpensions()
    {
        $ca = $this->findCustomAccount();
        $expNDS = 0;
        //$expCache = 0;

        if($cac = $ca->costs){
            foreach ($cac as $cost){
                if($cost->who_pay == CustomsAccountCost::WHO_PAY_WE && $cost->cost_type == CustomsAccountCost::COST_TYPE_ACCOUNTABLE){
                    //$expCache += $cost->price_cost_our;
                    $expNDS += $cost->price_nds_cost_our;
                }

            }

        }

        $ca->price_expenses_nds = $expNDS;
        //$ca->price_expenses_cache = $expCache;
        $ca->price_expenses_total = $expNDS;
        $ca->save(false);


        return $this;
    }

    private function findCustomAccount()
    {
        if ($ca = CustomsAccount::findOne($this->_customAccountID)) {
            return $ca;
        }
        throw new NotFoundHttpException('The requested record not found.');
    }

}