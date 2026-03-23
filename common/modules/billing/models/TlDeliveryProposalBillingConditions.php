<?php

namespace common\modules\billing\models;

use Yii;
use common\modules\billing\components\BillingManager;

/**
 * This is the model class for table "tl_delivery_proposal_billing_conditions".
 *
 * @property integer $id
 * @property integer $tl_delivery_proposal_billing_id
 * @property integer $client_id
 * @property string $price_invoice
 * @property string $price_invoice_with_vat
 * @property string $formula_tariff
 * @property integer $status
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 * @property integer $title
 * @property integer $sort_order
 * @property integer $delivery_type
 */
class TlDeliveryProposalBillingConditions extends \common\models\ActiveRecord
{

    /*
    * @var integer status
    * */
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /*
   * Delivery type
   * */
    const DELIVERY_TYPE_UNDEFINED = 0;
    const DELIVERY_TYPE_WAREHOUSE_WAREHOUSE = 1;
    const DELIVERY_TYPE_DOOR_DOOR = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_delivery_proposal_billing_conditions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_delivery_proposal_billing_id', 'client_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted', 'sort_order', 'delivery_type'], 'integer'],
            [['price_invoice', 'price_invoice_with_vat'], 'number'],
            [['formula_tariff', 'comment', 'title'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'tl_delivery_proposal_billing_id' => Yii::t('forms', 'Tl Delivery Proposal Billing ID'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'price_invoice' => Yii::t('forms', 'Price Invoice'),
            'price_invoice_with_vat' => Yii::t('forms', 'Price Invoice With Vat'),
            'formula_tariff' => Yii::t('forms', 'Formula Tariff'),
            'status' => Yii::t('forms', 'Status'),
            'comment' => Yii::t('forms', 'Comment'),
            'title' => Yii::t('forms', 'Name'),
            'delivery_type' => Yii::t('forms', 'Delivery Type'),
            'sort_order' => Yii::t('forms', 'Sort Order'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray($key = null)
    {
        $data = [
            self::STATUS_ACTIVE => Yii::t('forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('forms', 'Not active'),
            self::STATUS_DELETED => Yii::t('forms', 'Deleted'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatus()
    {
        $a = self::getStatusArray();
        return isset($a[$this->status]) ? $a[$this->status] : '-';
    }

    /*
    * This method is called at the beginning of inserting or updating a record.
    *
    * */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $b = new BillingManager();
            $this->price_invoice = $b->calculateWithOutNDS( $this->price_invoice_with_vat);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array Массив с типами доставки
     */
    public static function getDeliveryTypeArray($key = null)
    {
        $data = [
            self::DELIVERY_TYPE_UNDEFINED => Yii::t('forms', 'Undefined'),
            self::DELIVERY_TYPE_WAREHOUSE_WAREHOUSE => Yii::t('forms', 'Warehouse-warehouse'),
            self::DELIVERY_TYPE_DOOR_DOOR => Yii::t('forms', 'Door-Door'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getDeliveryType()
    {
        $a = self::getDeliveryTypeArray();
        return isset($a[$this->delivery_type]) ? $a[$this->delivery_type] : '-';
    }

    public function getConditionTitle(){
       return str_replace('{price}', Yii::$app->formatter->asCurrency($this->price_invoice_with_vat), $this->title);
    }
}
