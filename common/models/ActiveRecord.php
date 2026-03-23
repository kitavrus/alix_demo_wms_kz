<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 10.08.14
 * Time: 15:53
 */

namespace common\models;

//use frontend\modules\warehouse\models\Warehouse;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;
use dektrium\user\models\User;
use common\modules\client\models\Client;
use common\modules\city\models\City;

class ActiveRecord extends \yii\db\ActiveRecord {

    /*
    * @var integer payment method
    * */
    const METHOD_UNDEFINED = 0; //не указан
    const METHOD_CASH = 1; //наличный
    const METHOD_CHAR = 2; //безналичный

    /*
    * @var integer deleted status show
    * */
    const NOT_SHOW_DELETED = 0; //не указан
    const SHOW_DELETED = 1; //наличный

    /*
    * @var integer invoice status
    * */
    const INVOICE_UNDEFINED = 0; //счет не выставлен
    const INVOICE_NOT_SET = 1; //счет не выставлен
    const INVOICE_SET = 2; //счет выставлен
    const INVOICE_PAID = 3; //счет оплачен

    /*
   * @var integer currency
   * */
    const CURRENCY_EUR= 1;
    const CURRENCY_USD = 2;


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
            'blameableBehavior'=>[
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_user_id',
                'updatedByAttribute' => 'updated_user_id',
            ],

            'auditBehavior' => [
                'class' => 'common\behaviors\AuditBehavior',
                'ignoredAttributes' => [
                    'created_user_id',
                    'updated_user_id',
                    'created_at',
                    'updated_at',
                    'extra_fields',
                    'bl_data',
                ],
                'allowedClasses' => [
                    'common\modules\transportLogistics\models\TlDeliveryProposal',
                    'common\modules\store\models\StoreReviews',
                    'common\modules\store\models\Store',
                    'common\modules\transportLogistics\models\TlDeliveryProposalRouteCars',
                    'common\modules\transportLogistics\models\TlDeliveryProposalRouteTransport',
                    'common\modules\transportLogistics\models\TlDeliveryProposalOrders',
                    'common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses',
                    'common\modules\transportLogistics\models\TlDeliveryRoutes',
                    'common\modules\transportLogistics\models\TlAgents',
                    'common\modules\transportLogistics\models\TlOutboundRegistry',
                    'common\modules\transportLogistics\models\TlOutboundRegistryItems',
                    'common\modules\billing\models\TlDeliveryProposalBilling',
                    'common\modules\billing\models\TlDeliveryProposalBillingConditions',
                    'common\modules\agentBilling\models\TlAgentBilling',
                    'common\modules\agentBilling\models\TlAgentBillingConditions',
                    'common\modules\inbound\models\InboundOrder',
                    'common\modules\inbound\models\InboundOrderItem',
                    'common\modules\outbound\models\OutboundOrder',
                    'common\modules\outbound\models\OutboundOrderItem',
                    'common\modules\outbound\models\OutboundPickingLists',
                    'common\modules\stock\models\Stock',
                    'common\modules\crossDock\models\CrossDock',
                    'common\modules\crossDock\models\CrossDockItems',
                    'common\modules\bookkeeper\models\Bookkeeper',
                    'stockDepartment\modules\bookkeeper\models\Bookkeeper',
                    'common\modules\stock\models\ConsignmentUniversal',
                ],
            ]
        ];
    }

    /*
     * Define default scope
     * */
    public static function find()
    {
        $alias = static::tableName();
        return parent::find()->andWhere([$alias.'.deleted' => self::NOT_SHOW_DELETED]);
    }

    /*
    * Get username by ID
    * */
    public static function getUserName($key=null)
    {
        $data = User::findOne(['id' => $key]);

        return empty($data) ? Yii::t('titles', 'Not defined') : $data->username;
    }

    /**
     * @return array with client id=>username
     */
    public function getClientArray($key=null)
    {
        $data = ArrayHelper::map(Client::findAll(['status' => Client::STATUS_ACTIVE]), 'id', 'title');

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return array with client id=>username
     */
//    public function getWarehouseArray($key=null)
//    {
//        $data = ArrayHelper::map(Warehouse::findAll(['status' => Warehouse::STATUS_ACTIVE]), 'id', 'name');
//
//        return isset($data[$key]) ? $data[$key] : $data;
//    }

    /**
     * @return array with client id=>username
     */
    public function getCityArray($key=null)
    {
        $data = ArrayHelper::map(City::find()->all(), 'id', 'name');

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return array Массив с формами оплаты.
     */
    public static function getPaymentMethodArray($key=null)
    {
        $data = [
            self::METHOD_UNDEFINED => Yii::t('forms', 'Undefined'), //Не определен
            self::METHOD_CASH => Yii::t('forms', 'Cash'), //наличный
            self::METHOD_CHAR => Yii::t('forms', 'Charging to account') //безналичный
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return array Значение формы оплаты.
     */
    public function getPaymentMethodValue($cash_no=null)
    {
        if(is_null($cash_no)){
            $cash_no = $this->cash_no;
        }
        return ArrayHelper::getValue(self::getPaymentMethodArray(), $cash_no);
    }


    /**
     * @return array Массив со статусами оплаты.
     */
    public static function getInvoiceStatusArray($key=null)
    {
        $data = [
            self::INVOICE_UNDEFINED => Yii::t('forms', 'Undefined'), //Не определен
            self::INVOICE_NOT_SET => Yii::t('forms', 'Not set'), //Не выставлен
            self::INVOICE_SET => Yii::t('forms', 'Set'), //Выставлен
            self::INVOICE_PAID => Yii::t('forms', 'Paid'), //Оплачен
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return array Значение со статусом оплаты.
     */
    public function getInvoiceStatusValue($status_invoice=null)
    {
        if(is_null($status_invoice)){
            $status_invoice = $this->status_invoice;
        }
        return ArrayHelper::getValue(self::getInvoiceStatusArray(), $status_invoice);
    }



    /*
    * Relation has one with user
    * */
    public function getCreatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_user_id']);
    }

    /*
     * Relation has one with user
     * */
    public function getUpdatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_user_id']);
    }

} 