<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.01.2019
 * Time: 18:36
 */

namespace common\components\FailDeliveryStatus;

use yii\base\Model;
use Yii;

class AddStatusForm extends Model {

    public $status;
    public $otherStatus;
    public $deliveryProposalId;

    private $_other_reason;

    function init() {
        $this->_other_reason = \common\components\FailDeliveryStatus\StatusList::OTHER_REASON;
    }

    public function attributeLabels()
    {
        return [
            'status' => Yii::t('inbound/forms', 'STATUS'),
            'otherStatus' => Yii::t('inbound/forms', 'OTHER_STATUS'),
            'deliveryProposalId' => Yii::t('inbound/forms', 'DELIVERY_PROPOSAL_ID'),
        ];
    }

    public function rules()
    {
        return [
            [['status'], 'required'],
            [['otherStatus'], 'required','when' => function($model) {
                return $model->status == $this->_other_reason;
            }, 'whenClient' => "function (attribute, value) {
                return $('#addstatusform-status').val() == '".$this->_other_reason."';
            }"],
        ];
    }

    public function getDTO() {
        return  [
            'status'=>$this->status,
            'otherStatus'=>$this->otherStatus,
            'deliveryProposalId'=>$this->deliveryProposalId,
        ];
    }
}