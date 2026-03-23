<?php

namespace app\modules\bookkeeper\controllers;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteCars;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport;
use stockDepartment\modules\bookkeeper\models\AgentsBookkeeperForm;
use Yii;
use stockDepartment\modules\bookkeeper\models\TlAgentsBookkeeper;
use stockDepartment\modules\bookkeeper\models\TlAgentsBookkeeperSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AgentController implements the CRUD actions for TlAgentsBookkeeper model.
 */
class AgentController extends Controller
{
    /**
     * Lists all TlAgentsBookkeeper models.
     * @return mixed
     */
    public function actionIndex()
    {
        $filter = Yii::$app->request->get('filter',1);
        $monthArray = [];

        $agents = TlAgents::find()->andWhere(['payment_period'=>$filter])->all();
        $agentArray = ArrayHelper::map($agents,'id','name');
        $agentIDs = ArrayHelper::getColumn($agents,'id');

        $mStarNow = date('n',time()) - 2;
        $mStar = $mStarNow <= 9 ? "0".$mStarNow : $mStarNow;

        if($filter == TlAgents::MONTH_PAYMENT_PERIOD) {
            $y = date('Y',time());
            $mNow = date('n',time());
            $monthArray = [];
            for($i = $mStarNow; $i <= $mNow; $i++) {
                $m = $i <= 9 ? "0".$i : $i;

                $start = $y.'-'.$m.'-01';
                $end = $y.'-'.$m.'-'.date('t',strtotime($start));

                $monthArray[] = [
                    'year'=>$y,
                    'start'=>$start,
                    'end'=>$end,
                    'monthTitle'=>Yii::$app->formatter->asDate($start,'php:F'),
                ];
            }
        }

        if($filter == TlAgents::WEEK_PAYMENT_PERIOD) {

            $monthArray = [];
            $weekStart = '2016-'.$mStar.'-01';
//            $weekStart = '2016-01-04';
            $dateStart = new \DateTime('now');
            $nowDate = $dateStart->getTimestamp();
            $endDate = '0';
            $flag = 0;
            while ($nowDate > $endDate) {

                $dateEnd = new \DateTime($weekStart);
                $start = $dateEnd->format('Y-m-d');
                if(!$flag) {
                    $dateEnd->modify('+7 day');
                    $flag = true;
                } else {
                    $dateEnd->modify('+6 day');
                }
                $end = $dateEnd->format('Y-m-d');

//                $weekStart = $end;
//                $endDate = $dateEnd->getTimestamp();
                //$y = $dateEnd->format('Y');

                $dateEnd->modify('+1 day');
                $endDate = $dateEnd->getTimestamp();
                $weekStart = $dateEnd->format('Y-m-d');
                $y = $dateEnd->format('Y');

                $monthArray[] = [
                    'year' => $y,
                    'start' => $start,
                    'end' => $end,
                    'monthTitle' => Yii::$app->formatter->asDate($start, 'php:F'),
                ];
            }
        }

        if($filter == TlAgents::TWO_IN_MONTH_PAYMENT_PERIOD) {

            $monthArray = [];
            $weekStart = '2016-'.$mStar.'-01';
            $dateStart = new \DateTime('now');
            $nowDate = $dateStart->getTimestamp();
            $endDate = '0';
            $flag = true;
            $step = 14;
            while ($nowDate > $endDate) {

                $dateEnd = new \DateTime($weekStart);
                $start = $dateEnd->format('Y-m-d');
                if($flag) {
                    $dateEnd->modify('+'.$step.' day');
                    $flag = false;
                } else {
                    $dateEnd->modify('+'.(date('t',strtotime($start))-$dateEnd->format('j')).' day');
                    $flag = true;

                }
                $end = $dateEnd->format('Y-m-d');

                $dateEnd->modify('+1 day');
                $endDate = $dateEnd->getTimestamp();
                $weekStart = $dateEnd->format('Y-m-d');
                $y = $dateEnd->format('Y');

                $monthArray[] = [
                    'year' => $y,
                    'start' => $start,
                    'end' => $end,
                    'monthTitle' => Yii::$app->formatter->asDate($start, 'php:F'),
                ];
            }
        }

        $activeAgentIDs = [];
        foreach($monthArray as $key=>$item) {

            $activeAgentIDItem = [];

            $query = TlDeliveryProposalRouteCars::find();
            $dateStart = $item['start'] . ' 00:00:00';
            $dateEnd = $item['end'] . ' 23:59:59';

            $query->select('id, agent_id, price_invoice');
            $query->andWhere(['between', 'shipped_datetime', strtotime($dateStart), strtotime($dateEnd)]);
            $query->andWhere(['agent_id'=>$agentIDs]);

            $query->orderBy(['id'=>SORT_DESC]);

            $carAll = $query->asArray()->all();
            if($carAll) {
               foreach($carAll as $carValue) {
                   $DPRTCount = TlDeliveryProposalRouteTransport::find()->andWhere(['tl_delivery_proposal_route_cars_id'=>$carValue['id']])->count();
                   if($DPRTCount) {
                       $flag = 0;
                       $DPRTIds = TlDeliveryProposalRouteTransport::find()->andWhere(['tl_delivery_proposal_route_cars_id'=>$carValue['id']])->all();
                       foreach($DPRTIds as $DPRTItem) {
                           if(TlDeliveryProposal::find()->andWhere(['id'=>$DPRTItem->tl_delivery_proposal_id])->exists()) {
                               $flag++;
                           }
                       }

                       if($flag) {
                           if (!isset($activeAgentIDItem [$carValue['agent_id']]['sum_price_invoice'])) {
                               $activeAgentIDItem [$carValue['agent_id']]['sum_price_invoice'] = $carValue['price_invoice'];
                           } else {
                               $activeAgentIDItem [$carValue['agent_id']]['sum_price_invoice'] += $carValue['price_invoice'];
                           }
                       }
                       $activeAgentIDs[$carValue['agent_id']] = $carValue['agent_id'];
                   }
               }
            }
            $monthArray[$key]['activeAgentIDs'] =  $activeAgentIDItem;
        }

        return $this->render('index', [
            'filter' => $filter,
            'agentArray' => $agentArray,
            'monthArray' => $monthArray,
            'activeAgentIDs' => $activeAgentIDs,
            'agentsBookkeeperForm' => new AgentsBookkeeperForm(),
        ]);
    }

    /**
     * Creates a new TlAgentsBookkeeper model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new TlAgentsBookkeeper();

        if ($model->load(Yii::$app->request->post(),'AgentsBookkeeperForm') && $model->validate()) {
            $status = $model->status;
            if($m = TlAgentsBookkeeper::find()->andWhere(['agent_id'=>$model->agent_id,'month_from'=>$model->month_from,'month_to'=>$model->month_to])->one()) {
                $m->invoice = $model->invoice;
                $m->status = $status;
                $m->save(false);

            } else {
                $model->save(false);
            }

            $query = TlDeliveryProposalRouteCars::find();
            $dateStart = $model->month_from . ' 00:00:00';
            $dateEnd = $model->month_to . ' 23:59:59';

            $query->andWhere(['between', 'shipped_datetime', strtotime($dateStart), strtotime($dateEnd)]);
            $query->andWhere(['agent_id'=>$model->agent_id,]);
            $query->orderBy(['id'=>SORT_DESC]);

            $carAll = $query->all();
            if($carAll) {
                foreach($carAll as $carValue) {
                    if( TlDeliveryProposalRouteTransport::find()->andWhere(['tl_delivery_proposal_route_cars_id'=>$carValue['id']])->count()) {
                        $carValue->status_invoice = $status;
                        $carValue->save(false);
                    }
                }
            }

            return ['color'=>AgentsBookkeeperForm::selectColorByStatus($status)];
        }
        return ['color'=>'NO'];
    }
}