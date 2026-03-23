<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace stockDepartment\modules\transportLogistics\models\forms;
use common\modules\codebook\models\BaseBarcode;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalDefaultRoute;
use common\modules\transportLogistics\models\TlOutboundRegistryItems;
use yii\base\Model;
use Yii;
use yii\helpers\VarDumper;


class RegistryScanningForm extends Model {

    public $proposal_barcode;
    public $registry_id;

    /*
     *
     * */
    public function rules()
    {
        return [
            [['registry_id'], 'integer'],
            [['proposal_barcode'], 'required'],
            [['proposal_barcode'], 'filter', 'filter' => function ($value) {
                return (int)$value;
            }],
            [['proposal_barcode'],'validateProposalBarcode'],


        ];
    }

    /*
     * Validate proposal
     **/
    public function validateProposalBarcode($attribute, $params)
    {
        $value = $this->$attribute;
        if ($bb = BaseBarcode::findOne($value)) {
            if ($dp = TlDeliveryProposal::findOne($bb->ttn_barcode)) {

                $badStatuses = [TlDeliveryProposal::STATUS_DONE, TlDeliveryProposal::STATUS_ON_ROUTE, TlDeliveryProposal::STATUS_DELIVERED];
                if (in_array($dp->status, $badStatuses)) {
                    $this->addError($attribute, '<b> [ ' . $value . ' ] </b> ' . Yii::t('transportLogistics/errors', 'Proposal <b>{0}</b> already shipped', [$dp->id]));
                }

                if ($exists = TlOutboundRegistryItems::find()->andWhere(['tl_outbound_registry_id' => $this->registry_id, 'tl_delivery_proposal_id' => $dp->id])->one()) {
                    $this->addError($attribute, '<b> [ ' . $value . ' ] </b> ' . Yii::t('transportLogistics/errors', 'Proposal <b>{0}</b> already exist in this registry', [$dp->id]));
                }
                $routes = $dp->proposalRoutes;
                $defaultRoute = TlDeliveryProposalDefaultRoute::find()->andWhere(['from_point_id' => $dp->route_from, 'to_point_id' => $dp->route_to])->one();

                if (!$routes) {
                    if ($defaultRoute) {
                        if (!$subRoutes = $defaultRoute->subRoutes) {
                            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> ' . Yii::t('transportLogistics/errors', 'Proposal <b>{0}</b> has default route, but has no sub routes', [$dp->id]));
                        }
                    } else {
                        $this->addError($attribute, '<b> [ ' . $value . ' ] </b> ' . Yii::t('transportLogistics/errors', 'Proposal <b>{0}</b> has no routes and default routes', [$dp->id]));
                    }

                }


            } else {
                $this->addError($attribute, '<b> [ ' . $value . ' ] </b> ' . Yii::t('transportLogistics/errors', 'Proposal <b>{0}</b> not found', [$bb->ttn_barcode]));
            }
        } else {
            $this->addError($attribute, '<b> [ ' . $value . ' ] </b> ' . Yii::t('transportLogistics/errors', 'Неверный ШК короба'));
        }


    }

    public function attributeLabels()
    {
        return [
            'proposal_barcode' => Yii::t('transportLogistics/forms', 'Box barcode'),
        ];
    }

}