<?php

namespace common\modules\bookkeeper\models;

use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses;
use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "bookkeeper".
 *
 * @property integer $id
 * @property integer $sort_order
 * @property integer $tl_delivery_proposal_id
 * @property integer $tl_delivery_proposal_route_unforeseen_expenses_id
 * @property string  $unique_key
 * @property integer $department_id
 * @property integer $doc_type_id
 * @property string $doc_file
 * @property string $name_supplier
 * @property string $description
 * @property string $price
 * @property string $balance_sum
 * @property integer $type_id
 * @property integer $expenses_type_id
 * @property integer $cash_type
 * @property integer $client_id
 * @property integer $status
 * @property integer $date_at
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class Bookkeeper extends ActiveRecord
{
    const DEPARTMENT_STOCK = 1;
    const DEPARTMENT_TRANSPORT = 2;
    const DEPARTMENT_OFFICE = 3;

    const DOC_TYPE_CHECK = 1;
    const DOC_TYPE_NO_CHECK = 2;
    const DOC_TYPE_INVOICE = 3;

    const STATUS_NEW = 0; //новая
    const STATUS_DONE = 1; //расход закрыт в пересчете не участвует
    const STATUS_MONEY_RECEIVED = 2; //деньги получены
    const STATUS_MONEY_GIVEN = 3; //деньги отданы

    const TYPE_PLUS = 1; //приход
    const TYPE_MINUS = 2; //расход

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bookkeeper';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'expenses_type_id' => Yii::t('app', 'Тип расхода'),
            'cash_type' => Yii::t('app', 'Форма оплаты'),
            'client_id' => Yii::t('app', 'Клиент'),
            'tl_delivery_proposal_id' => Yii::t('app', 'Заявка на доставку'),
            'tl_delivery_proposal_route_unforeseen_expenses_id' => Yii::t('app', 'Расход по маршруту'),
            'department_id' => Yii::t('app', 'Отдел'),
            'doc_type_id' => Yii::t('app', 'Тип док-та'),
            'doc_file' => Yii::t('app', 'Документ'),
            'name_supplier' => Yii::t('app', 'Поставщик'),
            'description' => Yii::t('app', 'Описание'),
            'price' => Yii::t('app', 'Приход / Расход'),
//            'plus_sum' => Yii::t('app', 'Приход'),
//            'minus_sum' => Yii::t('app', 'Расход'),
            'balance_sum' => Yii::t('app', 'Остаток'),
            'type_id' => Yii::t('app', 'Тип'),
            'status' => Yii::t('app', 'Статус'),
            'date_at' => Yii::t('app', 'Дата'),
            'created_user_id' => Yii::t('app', 'Создал(а)'),
            'updated_user_id' => Yii::t('app', 'Обновил(а)'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата обновления'),
        ];
    }

    public function showDateAt()
    {
        return !empty($this->date_at) ? Yii::$app->formatter->asDate($this->date_at) : '';
    }

    public function showPlus()
    {
        return ($this->type_id == self::TYPE_PLUS ? $this->price : '');
    }

    public function showMinus()
    {
        return ($this->type_id == self::TYPE_MINUS ? $this->price : '');
    }
    /**
     * @return array Массив.
     */
    public static function getDepartmentIdArray()
    {
        return [
            self::DEPARTMENT_STOCK => Yii::t('stock/titles', 'Склад'),
            self::DEPARTMENT_TRANSPORT => Yii::t('stock/titles', 'Транспорт'),
            self::DEPARTMENT_OFFICE => Yii::t('stock/titles', 'Офис'),
        ];
    }

    /**
     * @return string
     */
    public function getDepartmentIdValue($department_id = null)
    {
        if(is_null($department_id)){
            $department_id = $this->department_id;
        }
        return ArrayHelper::getValue(self::getDepartmentIdArray(), $department_id);
    }

    /**
     * @return array Массив.
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_NEW => Yii::t('stock/titles', 'Новый'),
            self::STATUS_MONEY_RECEIVED => Yii::t('stock/titles', 'Деньги получены'),
            self::STATUS_MONEY_GIVEN => Yii::t('stock/titles', 'Деньги отданы'),
            self::STATUS_DONE => Yii::t('stock/titles', 'Закрыт'),
        ];
    }

    /**
     * @param string $status
     * @return string
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue(self::getStatusArray(), $status);
    }


    /**
     * @return array Массив.
     */
    public static function getDocTypeIdArray()
    {
        return [
            self::DOC_TYPE_CHECK => Yii::t('stock/titles', 'Чек'),
            self::DOC_TYPE_NO_CHECK => Yii::t('stock/titles', 'Без чека'),
            self::DOC_TYPE_INVOICE => Yii::t('stock/titles', 'Счёт-фактура'),
        ];
    }

    /**
     * @return string
     */
    public function getDocTypeIdValue($doc_type_id = null)
    {
        if(is_null($doc_type_id)) {
            $doc_type_id = $this->doc_type_id;
        }
        return ArrayHelper::getValue(self::getDocTypeIdArray(), $doc_type_id);
    }

    /**
     * @return array Массив.
     */
    public static function getTypeArray()
    {
        return [
            self::TYPE_PLUS => Yii::t('stock/titles', 'Приход'),
            self::TYPE_MINUS => Yii::t('stock/titles', 'Расход'),
        ];
    }

    /**
     * @return string
     */
    public function getTypeValue($type_id = null)
    {
        if(is_null($type_id)){
            $type_id = $this->type_id;
        }
        return ArrayHelper::getValue(self::getTypeArray(), $type_id);
    }

    /**
     * @return array Массив.
     */
    public static function getExpensesTypeIdArray()
    {
        return TlDeliveryProposalRouteUnforeseenExpenses::getTypeArray();
    }

    /**
     * @return string
     */
    public function getExpensesTypeIdValue($expenses_type_id = null)
    {
        if(is_null($expenses_type_id)){
            $expenses_type_id = $this->expenses_type_id;
        }
        return ArrayHelper::getValue(self::getExpensesTypeIdArray(), $expenses_type_id);
    }

    /**
     * @return array Массив.
     */
    public static function getCashTypeArray()
    {
        return self::getPaymentMethodArray();
    }

    /**
     * @return string
     */
    public function getCashTypeValue($cash_type = null)
    {
        if(is_null($cash_type)){
            $cash_type = $this->cash_type;
        }
        return ArrayHelper::getValue(self::getCashTypeArray(), $cash_type);
    }

    /*
     * @param array $storeArray
     * */
    public function showDp($storeArray,$link = true)
    {
        $show = '';
        if($dp = $this->deliveryProposal) {
            $show .= isset($storeArray[$dp->route_from]) ? $storeArray[$dp->route_from] : '';
            $show .= ' > ';
            $show .= isset($storeArray[$dp->route_to]) ? $storeArray[$dp->route_to] : '';
            if ($link) {
//                $show = Html::a($show, ['/transportLogistics/tl-delivery-proposal/view', 'id' => $dp->id]);
                $show = Html::a($dp->id, ['/tms/default/view', 'id' => $dp->id]);
            }
        }
        return $show;
    }

    /*
     * @param array $storeArray
     * */
    public function showClientTitleDp($clientArray)
    {
        $show = '';
        if($dp = $this->deliveryProposal) {
            $show = ArrayHelper::getValue($clientArray,$dp->client_id);
        }
        return $show;
    }

    /*
     *
     * */
    public function showDpUnforeseenExpenses()
    {
        return '';
    }

    /*
    * Relation has one with Delivery proposal
    **/
    public function getDeliveryProposal()
    {
        return $this->hasOne(TlDeliveryProposal::className(), ['id' => 'tl_delivery_proposal_id']);
    }

    /*
    * Relation has one with Delivery proposal unforeseen expenses
    **/
    public function getDeliveryProposalUnforeseenExpenses()
    {
        return $this->hasOne(TlDeliveryProposalRouteUnforeseenExpenses::className(), ['id' => 'tl_delivery_proposal_route_unforeseen_expenses_id']);
    }
}