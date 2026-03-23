<?php

namespace common\modules\leads\models;

use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "transportation_tariff_company_lead".
 *
 * @property integer $id
 * @property string $customer_name
 * @property string $customer_company_name
 * @property string $customer_position
 * @property string $customer_phone
 * @property string $customer_email
 * @property integer $status
 * @property integer $cooperation_type_1
 * @property integer $cooperation_type_2
 * @property integer $cooperation_type_3
 * @property string $customer_comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TtCompanyLead extends ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_VIEWED = 2;

    /**
     * @return array Массив с статусами.
     */
    public function getStatusArray()
    {
        return [
            self::STATUS_NEW => Yii::t('frontend/titles', 'New'),
            self::STATUS_VIEWED => Yii::t('frontend/titles', 'Viewed'),
        ];
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatusValue($status = null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(), $status);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transportation_tariff_company_lead';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'cooperation_type_1', 'cooperation_type_2', 'cooperation_type_3', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['customer_name', 'customer_company_name', 'customer_position', 'customer_phone', 'customer_email'], 'string', 'max' => 128],
            [['customer_comment'], 'string', 'max' => 255],
            [['customer_name','customer_company_name', 'customer_email' ], 'required'],
            ['customer_email', 'match', 'pattern' => '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend/forms', 'ID'),
            'customer_name' => Yii::t('frontend/forms', 'Customer Name'),
            'customer_company_name' => Yii::t('frontend/forms', 'Customer Company Name'),
            'customer_position' => Yii::t('frontend/forms', 'Customer Position'),
            'customer_phone' => Yii::t('frontend/forms', 'Customer Phone'),
            'customer_email' => Yii::t('frontend/forms', 'Customer Email'),
            'status' => Yii::t('forms', 'Status'),
            'cooperation_type_1' => Yii::t('frontend/forms', 'One-time transportation'),
            'cooperation_type_2' => Yii::t('frontend/forms', 'Full freight on the basis of a contract'),
            'cooperation_type_3' => Yii::t('frontend/forms', 'Composite cargo on the basis of a contract'),
            'customer_comment' => Yii::t('frontend/forms', 'Customer Comment'),
            'created_user_id' => Yii::t('frontend/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('frontend/forms', 'Updated User ID'),
            'created_at' => Yii::t('frontend/forms', 'Created At'),
            'updated_at' => Yii::t('frontend/forms', 'Updated At'),
            'deleted' => Yii::t('frontend/forms', 'Deleted'),
        ];
    }
}
