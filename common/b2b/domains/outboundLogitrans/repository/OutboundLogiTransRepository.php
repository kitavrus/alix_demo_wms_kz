<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.08.2020
 * Time: 8:09
 */

namespace common\b2b\domains\outboundLogitrans\repository;


use common\b2b\domains\outbound\entities\OutboundBoxList;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use yii\helpers\ArrayHelper;

class OutboundLogiTransRepository
{
    private $partyNumberList;

    /*
 * Get active parent order number by client
 * @param integer $clientID
 * @return array parent_order_number
 * */
    /**
     * OutboundLogiTransRepository constructor.
     */
    public function __construct()
    {
        $this->partyNumberList = [
//            '8940782',
//            '8945697',
//            '9071169',
//            '9094978',
//            '9128710',
//            '9129745',
//            '9129739',
//            '9129763',
//            '9175044',

//            '9129739',
//            '9188962',
//            '9217268',
            
            //'9249773',
            '9180719',
            '9239700',
        ];
    }

    public function getActiveParentOrderNumberByClientId($clientID)
    {
        $data = ConsignmentOutboundOrder::find()
            ->select('party_number')
            ->andWhere(['party_number'=>$this->partyNumberList])
            ->andWhere(['client_id'=>$clientID])
            ->andWhere(['NOT IN','status',[Stock::STATUS_OUTBOUND_COMPLETE]])
            ->asArray()->all();

        return ArrayHelper::map($data,'party_number','party_number');
    }

    public function getPickingListReadyForScanned()
    {
        return OutboundOrder::find()
            ->andWhere(['parent_order_number'=>$this->partyNumberList])
            ->andWhere(['client_id'=>2])
            ->andWhere(['status'=>[
                    Stock::STATUS_OUTBOUND_PICKED,
                    Stock::STATUS_OUTBOUND_SCANNING,
                    Stock::STATUS_OUTBOUND_SCANNED,
                    Stock::STATUS_OUTBOUND_SORTING,
                    Stock::STATUS_OUTBOUND_SORTED,
                ]
            ])
            ->all();
    }
}