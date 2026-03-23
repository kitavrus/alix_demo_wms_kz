<?php

namespace stockDepartment\modules\client\models;

use Yii;
use common\models\ActiveRecord;
use stockDepartment\modules\store\models\Store;

/**
 * This is the model class for table "client_managers".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $client_id
 * @property integer $user_id
 * @property string $name
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
class ClientManagers extends ActiveRecord
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
    const TYPE_DIRECTOR = 1;
    const TYPE_MANAGER = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_managers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','store_id', 'client_id','manager_type'], 'required'],
            [['store_id', 'client_id', 'user_id', 'manager_type', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['password','first_name', 'middle_name', 'last_name', 'phone', 'phone_mobile', 'email'], 'string', 'max' => 64]
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
            'name' => Yii::t('forms', 'Name'),
            'password' => Yii::t('forms', 'Password'),
            'first_name' => Yii::t('forms', 'First Name'),
            'middle_name' => Yii::t('forms', 'Middle Name'),
            'last_name' => Yii::t('forms', 'Last Name'),
            'phone' => Yii::t('forms', 'Phone'),
            'phone_mobile' => Yii::t('forms', 'Phone Mobile'),
            'email' => Yii::t('forms', 'Email'),
            'manager_type' => Yii::t('forms', 'Manager Type'),
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
            self::STATUS_DELETED => Yii::t('forms', 'Deleted'),
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
            self::TYPE_DIRECTOR => Yii::t('titles', 'Директор'),
            self::TYPE_MANAGER => Yii::t('titles', 'Менеджер'),
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
        return $this->store->getDisplayFullTitle();
    }
}
