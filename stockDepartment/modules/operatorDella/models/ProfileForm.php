<?php
namespace app\modules\operatorDella\models;

use yii\base\Model;
use Yii;

class ProfileForm extends Model {

    public $name;
    public $phone;
    public $email;
    public $client_type;
    public $company_name;
    public $status;
    public $registration_date;
    public $password;
    public $password_repeat;


    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('client/forms', 'Customer Name'),
            'phone' => Yii::t('client/forms', 'Customer Phone'),
            'password' => Yii::t('client/forms', 'Password'),
            'password_repeat' => Yii::t('client/forms', 'Password repeat'),
            'status' => Yii::t('client/forms', 'Status'),
            'email' => Yii::t('client/forms', 'Customer Email'),
            'client_type' => Yii::t('client/forms', 'Please indicate how the person you are'),
            'company_name' => Yii::t('client/forms', 'Customer Company Name'),
            'registration_date' => Yii::t('client/forms', 'Registration date'),
        ];
    }


    /** @inheritdoc */
    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required'],
            ['name', 'string'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'unique'],
            ['email', 'email'],

            ['password', 'string', 'min' => 8],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message'=> Yii::t('client/titles', 'Entered passwords does not match')],

            ['phone', 'required'],
            ['phone', 'number'],
            [['password', 'password_repeat'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        return [
            'external-client-self-update'=>['name','phone', 'email', 'password', 'password_repeat'],
            'external-client-register'=>['name','phone', 'email', 'password', 'password_repeat'],
        ];
    }

}