<?php

namespace common\modules\employees\models;

use common\events\EmployeeEvent;
use Yii;
use common\modules\user\models\User;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "employees".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $password
 * @property string $title
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $barcode
 * @property string $phone
 * @property string $phone_mobile
 * @property string $email
 * @property integer $manager_type
 * @property integer $department
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class Employees extends \common\models\ActiveRecord
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
    const TYPE_ACCOUNTANT = 1; // Бухгалтер
    const TYPE_MANAGER_WHS_MAIN = 2; // Главный менеджер по складу
    const TYPE_MANAGER_TRAFFIC_MAIN = 3; // Главный менеджер по транспорту
    const TYPE_EMPLOYEE_WHS = 4; // Работник склада
    const TYPE_EMPLOYEE_SENIOR_WHS = 5; // Старший работник склада
    const TYPE_POINT_OPERATOR = 6; //оператор точки
    const TYPE_OPERATOR_DELLA = 7; // Оператор по делле
    const TYPE_MAIN_STOCK_EMPLOYEE = 8; // Главный аккаунт для всех работников склада


    const EVENT_CUSTOM_AFTER_SAVE   = 'eventCustomAfterSave';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employees';
    }

    /*
 *
 * */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_CUSTOM_AFTER_SAVE,[$this,'eventCustomAfterSave']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manager_type'], 'required'],
//            [['store_id'], 'required','when'=>function($model) {
//                return !in_array($model->manager_type,[6,7]);
//            },'whenClient' => "function (attribute, value) {
//                return $('#clientemployees-store_id').val() != '6' || $('#clientemployees-store_id').val() != '7';
//            }"],
            [['user_id', 'manager_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['barcode'], 'string', 'max' => 32],
            [['first_name', 'middle_name', 'last_name', 'phone', 'phone_mobile'], 'string', 'max' => 64],

            // password rules
            ['password', 'required','on'=>'create'],
            ['password', 'string', 'min' => 6],

            ['username', 'required'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9-_]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 25],
            ['username', 'unique'],
            ['username', 'trim'],
            ['username', 'common\validators\UniqueNameClientValidator'],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
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
            'id' => Yii::t('employees/forms', 'ID'),
            'user_id' => Yii::t('employees/forms', 'User ID'),
            'username' => Yii::t('employees/forms', 'Username'),
            'password' => Yii::t('employees/forms', 'Password'),
            'title' => Yii::t('employees/forms', 'Title'),
            'first_name' => Yii::t('employees/forms', 'First Name'),
            'middle_name' => Yii::t('employees/forms', 'Middle Name'),
            'last_name' => Yii::t('employees/forms', 'Last Name'),
            'barcode' => Yii::t('employees/forms', 'Barcode'),
            'phone' => Yii::t('employees/forms', 'Phone'),
            'phone_mobile' => Yii::t('employees/forms', 'Phone Mobile'),
            'email' => Yii::t('employees/forms', 'Email'),
            'manager_type' => Yii::t('employees/forms', 'Manager Type'),
            'department' => Yii::t('employees/forms', 'Department'),
            'status' => Yii::t('employees/forms', 'Status'),
            'created_user_id' => Yii::t('employees/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('employees/forms', 'Updated User ID'),
            'created_at' => Yii::t('employees/forms', 'Created At'),
            'updated_at' => Yii::t('employees/forms', 'Updated At'),
            'deleted' => Yii::t('employees/forms', 'Deleted'),
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
        ];
    }

    /**
     * @return string Читабельный статус.
     */
    public function getStatus()
    {
//        $a = self::getStatusArray();
        return ArrayHelper::getValue(self::getStatusArray(),$this->status);
//        return isset($a[$this->status]) ? $a[$this->status] : '-';
    }

    /**
     * @return array Array with manager type.
     */
    public static function getTypeArray()
    {
        return [
            self::TYPE_ACCOUNTANT => Yii::t('titles', 'Accountant'),
            self::TYPE_MANAGER_WHS_MAIN => Yii::t('titles', 'Main whs manager'),
            self::TYPE_MANAGER_TRAFFIC_MAIN => Yii::t('titles', 'Main traffic manager'),
            self::TYPE_EMPLOYEE_SENIOR_WHS => Yii::t('titles', 'Senior employee whs'),
            self::TYPE_EMPLOYEE_WHS => Yii::t('titles', 'Employee whs'),
            self::TYPE_POINT_OPERATOR => Yii::t('titles', 'Point operator'),
            self::TYPE_OPERATOR_DELLA => Yii::t('titles', 'DELLA_OPERATOR'),
            self::TYPE_MAIN_STOCK_EMPLOYEE => Yii::t('titles', 'MAIN_STOCK_EMPLOYEE'),
        ];
    }

    /**
     * @return string Manager type string.
     */
    public function getType()
    {
//        $a = self::getTypeArray();
//        return isset($a[$this->manager_type]) ? $a[$this->manager_type] : '-';
        return ArrayHelper::getValue(self::getTypeArray(),$this->manager_type);
    }

    /*
    * After save
    * */
    public function afterSave( $insert, $changedAttributes )
    {
        $e = new EmployeeEvent();
        $e->insert = $insert;
        $this->trigger(self::EVENT_CUSTOM_AFTER_SAVE,$e);

        return parent::afterSave($insert, $changedAttributes);
    }

    /*
     * Custom event handler
     * @param common\events\EmployeeEvent $event
     * */
    public function eventCustomAfterSave($event)
    {
        $this->off(self::EVENT_CUSTOM_AFTER_SAVE);

        if($event->insert) {

            $userModel = \Yii::createObject([
                'class'    => User::className(),
                'scenario' => 'create',
            ]);

            //после сохранения записи Employees добавляем запись в таблицу User
            $userModel->username = $this->username;
            $userModel->email = $this->email;
            $userModel->user_type = User::USER_TYPE_STOCK_WORKER;
            $userModel->password = $this->password;

            if ($userModel->create()) {
                $this->user_id = $userModel->id;
                // Clear password
                $this->password = '';
                $this->save(false);
            }

        } elseif ($userModel = \Yii::$container->get(\dektrium\user\Finder::className())->findUserById($this->user_id)) {
                $userModel->scenario = 'update';
                $userModel->username = $this->username;
                $userModel->email = $this->email;
                $userModel->password = $this->password;
                $userModel->save(false);
        }

        return true;
    }
}
