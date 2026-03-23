<?php

namespace common\modules\transportLogistics\models;

use Yii;

/**
 * This is the model class for table "tl_agent_employees".
 *
 * @property integer $id
 * @property integer $tl_agent_id
 * @property integer $user_id
 * @property string $username
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $phone
 * @property string $phone_mobile
 * @property string $email
 * @property integer $manager_type
 * @property integer $status
 * @property string $password
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class TlAgentEmployees extends \common\models\ActiveRecord
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_agent_employees';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tl_agent_id','manager_type','email'], 'required'],
            [['tl_agent_id', 'user_id', 'manager_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['username'], 'string', 'max' => 128],
            [['first_name', 'middle_name', 'last_name', 'phone', 'phone_mobile', 'email'], 'string', 'max' => 64],

            // password rules
            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['username', 'required'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9-_]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 25],
            ['username', 'unique'],
            ['username', 'trim'],
            ['username', 'common\validators\UniqueNameClientValidator'],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique'],
            ['email', 'trim'],
            ['email', 'common\validators\UniqueEmailClientValidator'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'tl_agent_id' => Yii::t('forms', 'Tl Agent ID'),
            'user_id' => Yii::t('forms', 'User ID'),
            'username' => Yii::t('forms', 'Login'),
            'first_name' => Yii::t('forms', 'First Name'),
            'middle_name' => Yii::t('forms', 'Middle Name'),
            'last_name' => Yii::t('forms', 'Last Name'),
            'phone' => Yii::t('forms', 'Phone'),
            'phone_mobile' => Yii::t('forms', 'Phone Mobile'),
            'email' => Yii::t('forms', 'Email'),
            'manager_type' => Yii::t('forms', 'Manager Type'),
            'status' => Yii::t('forms', 'Status'),
            'password' => Yii::t('forms', 'Password'),
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

}
