<?php

namespace common\modules\store\models;

use common\components\MailManager;
use common\helpers\DateHelper;
use Yii;
use common\models\ActiveRecord;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\components\DeliveryProposalManager;


/**
 * This is the model class for table "store_reviews".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $store_id
 * @property integer $tl_delivery_proposal_id
 * @property string $delivery_code
 * @property integer $delivery_datetime
 * @property integer $rate
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class StoreReviews extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_reviews';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'store_id', 'tl_delivery_proposal_id', 'number_of_places', 'rate', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['comment'], 'string', 'max' => 999],
            [['delivery_datetime','delivery_code'], 'string'],
//            [['delivery_datetime'], 'required', 'on' => ['update']],
            [['delivery_code'], 'required', 'on' => ['update']],
            [['delivery_code'], 'validateDeliveryCode', 'on' => ['update']],
            [['number_of_places'], 'required', 'on' => ['update']],
        ];
    }

    /*
     *
     *
     * */
    public function scenarios()
    {
        return [
            'default'=>[
                'delivery_code',
                'number_of_places',
                'rate',
                'comment',
            ],
            'update' => [
                'delivery_code',
                'number_of_places',
                'rate',
                'comment',
            ],
        ];
    }

    public function validateDeliveryCode($attribute,$params) {

        $proposal = $this->proposal;

        if (!in_array($proposal->route_to, ArrayHelper::map(StoreReviews::isAlmatyStores($proposal->client_id),'id','id') )) {
            return;
        }

        if ($this->delivery_code != $proposal->getSecureReviewCodePrefix()) {
            $this->addError($attribute, Yii::t('inbound/errors', 'Неверный секретный код'));
        }
    }

    public static function isAlmatyStores($clientId) {
        return Store::find()->andWhere([
            'client_id' =>$clientId,
            'region_id' => 1,
            'type_use' => 1,
        ])->asArray()->all();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'store_id' => Yii::t('forms', 'Store ID'),
            'tl_delivery_proposal_id' => Yii::t('forms', 'Tl Delivery Proposal ID'),
            'delivery_code' => Yii::t('forms', '(Отсканируйте) Штрих код ТТН'),
            'delivery_datetime' => Yii::t('forms', 'Delivery Datetime'),
            'number_of_places' => Yii::t('forms', 'Number of places scanned'),
            'rate' => Yii::t('forms', 'Rate'),
            'comment' => Yii::t('forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Modified User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /*
    * Relation with Store table
    * */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /*
    * Relation has one with Client
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /*
   * Relation has one with Proposal
   * */
    public function getProposal()
    {
        return $this->hasOne(TlDeliveryProposal::className(), ['id' => 'tl_delivery_proposal_id']);
    }

    /*
     *
     *
     * */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->getOldAttribute('delivery_datetime') != $this->getAttribute('delivery_datetime')) {
                if ($dp = TlDeliveryProposal::findOne($this->tl_delivery_proposal_id) ) {
                    $dp->delivery_date = $this->getAttribute('delivery_datetime');

                    if(in_array($dp->status,[TlDeliveryProposal::STATUS_ON_ROUTE])) {
                        $dp->status = TlDeliveryProposal::STATUS_DELIVERED;
                    }

                    $dp->save();
//                    $dp->recalculateExpensesOrder();
//                    $dp->setCascadedStatus();
                    $dpManager = new DeliveryProposalManager(['id' => $dp->id]);
                    $dpManager->setCascadeDeliveryDate();
                    $dpManager->setCascadedStatus();
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /*
* After save add order to route order
* */
    public function afterSave( $insert, $changedAttributes )
    {
        //S:
        if ( (isset($changedAttributes['delivery_datetime'])) ) {
            $mm =  new MailManager();
            $mm->sendMailToStockIfClientCreateNewReviewDP($this);
        }
        //E:

        return parent::afterSave($insert, $changedAttributes);
    }


}
