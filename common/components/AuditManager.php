<?php
namespace common\components;

use Yii;
use yii\base\Component;
use common\modules\audit\models\Audit;
//use common\modules\audit\models\TlDeliveryProposalAudit;
//use common\modules\audit\models\TlDeliveryProposalRouteCarsAudit;
//use common\modules\audit\models\TlDeliveryProposalRouteTransportAudit;
//use common\modules\audit\models\TlDeliveryRoutesAudit;
//use common\modules\audit\models\TlDeliveryProposalBillingConditionsAudit;
//use common\modules\audit\models\TlDeliveryProposalRouteUnforeseenExpensesAudit;
//use common\modules\audit\models\TlDeliveryProposalBillingAudit;
//use common\modules\audit\models\StoreAudit;
//use common\modules\audit\models\StoreReviewsAudit;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;



class AuditManager extends Component
{
    private $baseClass;
    private $auditClass;

    /**
     * @inheritdoc
     */
    public function __construct($baseClass)
    {
        $this->baseClass = $baseClass;
        $this->init();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setAuditTableClass();

    }

    /**
     * Creates data provider instance with search query applied
     * @param $parent_id
     * @return ActiveDataProvider
     */
    public function auditRecordSearch($parent_id)
    {
        $query = $this->auditClass->find();

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

    /**
     * @inheritdoc
     */
    public function setAuditTableClass()
    {
        $auditClassName = 'common\modules\audit\models\\'.\yii\helpers\StringHelper::basename(get_class($this->baseClass) . 'Audit');

        if(class_exists($auditClassName)){
            $this->auditClass = Yii::createObject($auditClassName);
        } else {
            $this->auditClass = false;
        }
    }

    /**
     * @inheritdoc
     */
    public function getAuditTableClass()
    {
       if(!empty($this->auditClass) && $this->auditClass instanceof Audit){
           return $this->auditClass;
       }

        return false;
    }
}