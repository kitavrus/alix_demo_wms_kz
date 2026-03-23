<?php

namespace stockDepartment\modules\audit\models;

use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use Yii;
use yii\data\ActiveDataProvider;
use common\modules\audit\models\Audit;
use common\modules\audit\models\TlDeliveryProposalAudit;
use common\modules\audit\models\TlDeliveryProposalRouteCarsAudit;
use common\modules\audit\models\TlDeliveryProposalRouteTransportAudit;
use common\modules\audit\models\TlDeliveryRoutesAudit;
use common\modules\audit\models\TlDeliveryProposalBillingConditionsAudit;
use common\modules\audit\models\TlDeliveryProposalRouteUnforeseenExpensesAudit;
use common\modules\audit\models\TlDeliveryProposalBillingAudit;
use common\modules\audit\models\StoreAudit;
use common\modules\audit\models\StoreReviewsAudit;


/**
 * TlDeliveryProposalSearch represents the model behind the search form about `common\modules\transportLogistics\models\TlDeliveryProposal`.
 */
class AuditSearch extends Audit
{
    private $auditTableClass;

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($parent_id)
    {

        $query = $this->auditTableClass->find();

        $query->andFilterWhere([
            'parent_id'=>$parent_id
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);


        return $dataProvider;
    }


    public function __construct($className)
    {
        $auditClass = 'common\modules\audit\models\\' . $className . 'Audit';
        $this->auditTableClass = new $auditClass;
    }
}
