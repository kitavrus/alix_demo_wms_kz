<?php

namespace stockDepartment\modules\client\models;

use Yii;
use common\models\ActiveRecord;
/**
 * This is the model class for table "clients".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $legal_company_name
 * @property string $password
 * @property string $title
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $phone
 * @property string $phone_mobile
 * @property string $email
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 *
 * @property ClientSocialAccount[] $clientSocialAccounts
 */
class Client extends ActiveRecord
{

    /*
    * @var integer status
    * */
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
//    const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','legal_company_name'], 'required'],
            [['user_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['username', 'title','password'], 'string', 'max' => 128],
            [['first_name', 'middle_name', 'last_name', 'phone', 'phone_mobile', 'email'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('forms', 'ID'),
            'user_id' => Yii::t('forms', 'User ID'),
            'password' => Yii::t('forms', 'Password'),

            'username' => Yii::t('forms', 'Username'),
            'legal_company_name' => Yii::t('forms', 'Legal company name'),

            'title' => Yii::t('forms', 'Title'),
            'first_name' => Yii::t('forms', 'First Name'),
            'middle_name' => Yii::t('forms', 'Middle Name'),
            'last_name' => Yii::t('forms', 'Last Name'),
            'phone' => Yii::t('forms', 'Phone'),
            'phone_mobile' => Yii::t('forms', 'Phone Mobile'),
            'email' => Yii::t('forms', 'Email'),
            'status' => Yii::t('forms', 'Status'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Modified User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getClientSocialAccounts()
//    {
//        return $this->hasMany(ClientSocialAccount::className(), ['user_id' => 'id']);
//    }

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
     * @return string Читабельный статус поста.
     */
    public function getStatus()
    {
        $status = self::getStatusArray();
        return $status[$this->status];
    }

    /*
    *
    * Relation has many with Client managers table
    * */
    public function getManagers()
    {
        return $this->hasMany(ClientManagers::className(), ['client_id'=>'id']);
    }
}
