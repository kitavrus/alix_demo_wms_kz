<?php

namespace common\modules\client\models;

use Yii;
use common\models\ActiveRecord;
use common\modules\store\models\Store;

/**
 * This is the model class for table "client_managers".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $client_id
 * @property integer $user_id
 * @property string $username
 * @property string $full_name
 * @property string $password
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $phone
 * @property string $phone_mobile
 * @property string $email
 * @property integer $manager_type
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class ClientEmployees extends ActiveRecord
{

    /*
    * @var integer status
    * */
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;


   /*
    * @var integer manager type
    * */
    const TYPE_DIRECTOR = 1; // Директор
    const TYPE_MANAGER = 2; // Менеджер
    const TYPE_MANAGER_INTERN = 3;  // Менеджер стажер
    const TYPE_DIRECTOR_INTERN = 4; // Директор стажер
    const TYPE_LOGIST = 5; // Логист
    const TYPE_BASE_ACCOUNT = 6; // Базовый аккаунт
    const TYPE_OBSERVER = 7; // Наблюдатель (ничего не может редактировать)
    const TYPE_OBSERVER_NO_TARIFF = 8; // Наблюдатель (ничего не может редактировать) не видет тарифы
    const TYPE_PERSONAL_CLIENT = 9; // Персональный клиент
    const TYPE_CORPORATE_CLIENT = 10; // Корпоративный клиент
    const TYPE_REGIONAL_OBSERVER_RUSSIA = 11; // Региональный наблюдатель видит только свой регион Россия
    const TYPE_REGIONAL_OBSERVER_BELARUS = 12; // Региональный наблюдатель видит только свой регион Белорусия
    const TYPE_TRANSPORT_TMP_CLIENT = 13; // Временный получатель или отправитель для транспорта

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_employees';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['phone','phone_mobile'], 'required','on'=>'update'],
            [['client_id','manager_type'], 'required'],
//            [['store_id'], 'required','when'=>function($model) {
//                return !in_array($model->manager_type,[6,7]);
//            },'whenClient' => "function (attribute, value) {
//                return $('#clientemployees-store_id').val() != '6' || $('#clientemployees-store_id').val() != '7';
//            }"],
            [['store_id', 'client_id', 'user_id', 'manager_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
//            [['username'], 'string', 'max' => 25],
//            [['email'], 'email'],
            [['first_name','full_name', 'middle_name', 'last_name', 'phone', 'phone_mobile'], 'string', 'max' => 64],

            // password rules
            ['password', 'required','on'=>'create'],
            ['password', 'string', 'min' => 6],
//
            ['username', 'required'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9-_]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 25],
            ['username', 'unique'],
            ['username', 'trim'],
           //['username', 'common\validators\UniqueNameClientValidator'],
//
//            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            ['email', 'unique'],
            ['email', 'trim'],
           //['email', 'common\validators\UniqueEmailClientValidator'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'store_id' => Yii::t('forms', 'Store ID'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'user_id' => Yii::t('forms', 'User ID'),
            'username' => Yii::t('forms', 'Login'),
            'password' => Yii::t('forms', 'Password'),
            'first_name' => Yii::t('forms', 'First Name'),
            'middle_name' => Yii::t('forms', 'Middle Name'),
            'last_name' => Yii::t('forms', 'Last Name'),
            'phone' => Yii::t('forms', 'Phone'),
            'phone_mobile' => Yii::t('forms', 'Phone Mobile'),
            'email' => Yii::t('forms', 'Email'),
            'manager_type' => Yii::t('forms', 'Employee Type'),
            'status' => Yii::t('forms', 'Status'),
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
    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('forms', 'Not active'),
//            self::STATUS_DELETED => Yii::t('forms', 'Deleted'),
        ];
    }

    /**
     * @return string Читабельный статус.
     */
    public function getStatus()
    {
        $a = self::getStatusArray();
        return isset($a[$this->status]) ? $a[$this->status] : '-';
    }

    /**
     * @return array Array with manager type.
     */
    public static function getTypeArray()
    {
        return [
            self::TYPE_DIRECTOR => Yii::t('titles', 'Director'),
            self::TYPE_MANAGER => Yii::t('titles', 'Manager'),
            self::TYPE_MANAGER_INTERN => Yii::t('titles', 'Manager intern'),
            self::TYPE_DIRECTOR_INTERN => Yii::t('titles', 'Director intern'),
            self::TYPE_LOGIST => Yii::t('titles', 'Logist'),
            self::TYPE_BASE_ACCOUNT => Yii::t('titles', 'Base account'),
            self::TYPE_OBSERVER => Yii::t('titles', 'Observer'),
            self::TYPE_OBSERVER_NO_TARIFF => Yii::t('titles', 'Observer no tariff'),
            self::TYPE_PERSONAL_CLIENT => Yii::t('titles', 'Personal client'),
            self::TYPE_CORPORATE_CLIENT => Yii::t('titles', 'Corporate client'),
//            self::TYPE_REGIONAL_OBSERVER => Yii::t('titles', 'Regional observer'),
            self::TYPE_REGIONAL_OBSERVER_RUSSIA => Yii::t('titles', 'Regional observer Russia'),
            self::TYPE_REGIONAL_OBSERVER_BELARUS => Yii::t('titles', 'Regional observer Belarus'),
        ];
    }

    /**
     * @return string Manager type string.
     */
    public function getType()
    {
        $a = self::getTypeArray();
        return isset($a[$this->manager_type]) ? $a[$this->manager_type] : '-';
    }

    /*
    * Relation with Store table
    * */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /*
     * Get store title
     * */
    public function getStoreTitle()
    {
        $r = '-';
        if($store = $this->store) {
            $r = $store->getDisplayFullTitle();
        }

        return $r;
    }

    /*
     *
     * */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->full_name = $this->first_name.' '.$this->middle_name.' '.$this->last_name;
            return true;
        } else {
            return false;
        }
    }
}
