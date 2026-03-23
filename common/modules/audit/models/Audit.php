<?php

namespace common\modules\audit\models;

use kartik\helpers\Html;
use Yii;
use common\modules\user\models\User;
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
use common\modules\audit\models\TlDeliveryProposalOrdersAudit;
use common\modules\audit\models\CrossDockAudit;
use common\modules\audit\models\CrossDockItemsAudit;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "tl_delivery_proposals_audit".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $date_created
 * @property integer $created_by
 * @property string $field_name
 * @property string $before_value_text
 * @property string $after_value_text
 */
class Audit extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->dbAudit;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['parent_id', 'date_created', 'created_by', 'field_name', 'before_value_text', 'after_value_text'], 'required'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'parent_id' => Yii::t('forms', 'Parent ID'),
            'date_created' => Yii::t('forms', 'Change date'),
            'created_by' => Yii::t('forms', 'User update'),
            'field_name' => Yii::t('forms', 'Field Name'),
            'before_value_text' => Yii::t('forms', 'Before Value Text'),
            'after_value_text' => Yii::t('forms', 'After Value Text'),
        ];
    }

    /*
    * Relation has one with user
    * */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /*
     * Try to find audit record for specified class
     * @param $parentID int id of checked record
     * @param $className string name of checked class
     * @return bool
     * */
    public static function haveAuditOrNot($parentID, $className)
    {
        $auditClass = 'common\modules\audit\models\\' . $className . 'Audit';

        if($audit = new $auditClass){

            $auditRecord = $audit::find()->where(['parent_id' => $parentID])->one();

            if(!empty($auditRecord)){
                return true;
            }

        }

        return false;
    }

    /*
    * Relation has one with user
    * */
    public function getFieldLabel()
    {
        if(method_exists($this->className(), 'getAuditObjectClass')){
            $object = Yii::createObject($this->getAuditObjectClass());
            $label = $object->getAttributeLabel($this->field_name);
            if(!empty($label)){
                return Html::encode($label);
            }
        }

        return false;
    }
}
