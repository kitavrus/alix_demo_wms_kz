<?php

namespace common\modules\leads\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "external_client_lead".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $client_type
 * @property string $username
 * @property string $legal_company_name
 * @property string $full_name
 * @property string $phone
 * @property string $email
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ExternalClientLead extends \common\models\ActiveRecord
{

    public $password;
    public $password_repeat;

    const CLIENT_TYPE_PERSON = 1;
    const CLIENT_TYPE_CORPORATE = 2;

    const CLIENT_STATUS_UNCONFIRMED = 1;
    const CLIENT_STATUS_CONFIRMED = 2;
    const CLIENT_STATUS_BLOCKED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'external_client_lead';
    }


    /** @inheritdoc */
    public function rules()
    {
        return [
            [['user_id', 'client_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['full_name', 'phone'], 'filter', 'filter' => 'trim'],
            ['full_name', 'required'],
            [['full_name', 'legal_company_name'], 'string'],

            //['email', 'required'],
            ['phone', 'unique'],
            ['email', 'email'],

            ['password', 'string', 'min' => 8],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message'=> Yii::t('client/titles', 'Entered passwords does not match')],
            ['client_type', 'required'],
            ['phone', 'required'],
            ['phone', 'number'],
            [['password', 'password_repeat'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['external-client-self-update'] = ['full_name','phone', 'email', 'password', 'password_repeat'];
        $scenarios['create-client-from-order'] = ['full_name','phone'];
        $scenarios['external-client-register'] = ['full_name', 'legal_company_name', 'phone', 'email', 'password', 'password_repeat', 'client_type'];

        return $scenarios;
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'full_name' => Yii::t('client/forms', 'Customer Name'),
            'phone' => Yii::t('client/forms', 'Customer Phone'),
            'password' => Yii::t('client/forms', 'Password'),
            'password_repeat' => Yii::t('client/forms', 'Password repeat'),
            'status' => Yii::t('client/forms', 'Status'),
            'email' => Yii::t('client/forms', 'Customer Email'),
            'client_type' => Yii::t('client/forms', 'Please indicate how the person you are'),
            'legal_company_name' => Yii::t('client/forms', 'Customer Company Name'),
            'created_at' => Yii::t('client/forms', 'Registration date'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getClientTypeArray()
    {
        $data =[
            self::CLIENT_TYPE_PERSON => Yii::t('client/titles', 'Person'),
            self::CLIENT_TYPE_CORPORATE =>Yii::t('client/titles', 'Corporate'),
        ];
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getClientTypeValue($client_type=null)
    {
       if(is_null($client_type)){
           $client_type = $this->client_type;
       }
        return ArrayHelper::getValue($this->getClientTypeArray(), $client_type);
    }

    /**
     * @inheritdoc
     */
    public function getClientStatusArray()
    {
        $data =[
            self::CLIENT_STATUS_UNCONFIRMED => Yii::t('client/titles', 'Unconfirmed client'),
            self::CLIENT_STATUS_CONFIRMED =>Yii::t('client/titles', 'Confirmed client'),
            self::CLIENT_STATUS_BLOCKED =>Yii::t('client/titles', 'Blocked client'),
        ];
        return $data;
    }

    /**
     * @inheritdoc
     */
    public  function getClientStatusValue($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getClientStatusArray(), $status);
    }

}
