<?php

namespace common\modules\bookkeeper\models;

use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "tl_agents_bookkeeper".
 *
 * @property integer $id
 * @property integer $agent_id
 * @property string $name
 * @property string $description
 * @property string $invoice
 * @property string $month_from
 * @property string $month_to
 * @property integer $status
 * @property integer $date_of_invoice
 * @property integer $payment_date_invoice
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlAgentsBookkeeper extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_agents_bookkeeper';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'agent_id' => Yii::t('app', 'Agent ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'invoice' => Yii::t('app', 'Invoice'),
            'month_from' => Yii::t('app', 'Month From'),
            'month_to' => Yii::t('app', 'Month To'),
            'status' => Yii::t('app', 'Status'),
            'date_of_invoice' => Yii::t('app', 'Date Of Invoice'),
            'payment_date_invoice' => Yii::t('app', 'Payment Date Invoice'),
            'created_user_id' => Yii::t('app', 'Created User ID'),
            'updated_user_id' => Yii::t('app', 'Updated User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
