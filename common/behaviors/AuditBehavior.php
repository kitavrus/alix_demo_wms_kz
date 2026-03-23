<?php
namespace common\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use common\modules\audit\models\TlDeliveryProposalAudit;
use common\modules\audit\models\TlDeliveryProposalRouteCarsAudit;
use common\modules\audit\models\TlDeliveryProposalRouteTransportAudit;
use common\modules\audit\models\TlDeliveryRoutesAudit;
use common\modules\audit\models\TlDeliveryProposalBillingConditionsAudit;
use common\modules\audit\models\TlDeliveryProposalRouteUnforeseenExpensesAudit;
use common\modules\audit\models\TlDeliveryProposalBillingAudit;
use common\modules\audit\models\StoreAudit;
use common\modules\audit\models\TlAgentsAudit;
use common\modules\audit\models\StoreReviewsAudit;
use common\modules\audit\models\InboundOrderAudit;
use common\modules\audit\models\InboundOrderItemAudit;
use common\modules\audit\models\OutboundOrderAudit;
use common\modules\audit\models\OutboundOrderItemAudit;
use common\modules\audit\models\OutboundPickingListsAudit;
use common\modules\audit\models\StockAudit;

class AuditBehavior extends Behavior
{
    public $auditTableClass;
    public $ignoredAttributes = [];
    public $allowedClasses = [];
    private $_oldAttributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'prepareAudit' //перед изменением записи
        ];
    }

    // S: Filter and prepare data for writing in DB
    public function prepareAudit($event)
    {
        $newattributes = $this->owner->dirtyAttributes;
        $oldattributes = $this->owner->oldAttributes;
        $allowedClasses = $this->allowedClasses;
        $ignoredAttributes = $this->ignoredAttributes;

        //only allowed classes will be written in audit table
        if (sizeof($allowedClasses) > 0) {
            if (in_array(get_class($this->owner), $allowedClasses) == false) {
                return;
            }
        }

        //unset fields which are ignored or empty
        if (sizeof($ignoredAttributes) > 0) {
            foreach ($newattributes as $f => $v) {
                if (array_search($f, $ignoredAttributes) !== false) {
                    unset($newattributes[$f]);
                }
            }
            foreach ($oldattributes as $f => $v) {
                if (array_search($f, $ignoredAttributes) !== false) {
                    unset($oldattributes[$f]);
                }
            }
        }

        //check for existing values
        foreach ($newattributes as $name => $value) {
            if (isset($oldattributes[$name]) && $value == $oldattributes[$name]) {
                unset ($newattributes[$name]);
            } else {
                continue;
            };
        }

        //get attributes values
        if($this->owner->hasMethod('getAttributesValuesMap')){
            foreach ($newattributes as $attribute => $value){
                if($method = $this->owner->getAttributesValuesMap($attribute)){
                    $attributeValue = call_user_func([$this->owner, $method], $value);
                    $newattributes[$attribute] = !empty($attributeValue) ? $attributeValue : $value;
                }
            }
            foreach ($oldattributes as $attribute => $value){
                if($method = $this->owner->getAttributesValuesMap($attribute)){
                    $attributeValue = call_user_func([$this->owner, $method], $value);
                    $oldattributes[$attribute] = !empty($attributeValue) ? $attributeValue : $value;
                }
            }
        }

        //write data in audit table
        $this->createAudit($oldattributes, $newattributes);

        //reset old values
        $this->setOldAttributes($this->owner->oldAttributes);
    }
    // E: Filter and prepare data for writing in DB


    //write filtered data to audit table
    public function createAudit($oldattributes, $newattributes)
    {
        $this->setAuditTableClass();
        if (!empty ($newattributes) && !empty($oldattributes)) {
            foreach ($newattributes as $name => $value) {
                $a = new $this->auditTableClass;
                $a->parent_id = $this->owner->id;
                $a->date_created = $this->getDateTime();
                $a->created_by = $this->getUser();
                $a->field_name = $name;
                $a->before_value_text = isset ($oldattributes[$name]) ? $oldattributes[$name] : NULL;
                $a->after_value_text = $value;
                $a->save(false);
            }
        }

    }

    public function getUser()
    {
        $user = Yii::$app->get('user', false);
        return $user && !$user->isGuest ? $user->id : null;

//        $userid = Yii::$app->user->id;
//        return empty($userid) ? null : $userid;

    }

    public function setAuditTableClass()
    {

        if (empty($this->auditTableClass)) {

           // $this->auditTableClass =  \yii\helpers\StringHelper::basename(get_class($this->owner).'Audit');
            $this->auditTableClass =  'common\modules\audit\models\\'.\yii\helpers\StringHelper::basename(get_class($this->owner) . 'Audit');
            //$this->auditTableClass =  'common'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'audit'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.\yii\helpers\StringHelper::basename(get_class($this->owner).'Audit');
            //$this->auditTableClass = $this->owner->className() . "Audit";
        }

    }

    public function setOldAttributes($value)
    {
        $this->_oldAttributes = $value;
    }

    public function getOldAttributes()
    {
        return $this->_oldAttributes;
    }

    public function getDateTime()
    {
        $d = new \DateTime('now');
        return $d->format('Y-m-d H:i:s');
    }

}