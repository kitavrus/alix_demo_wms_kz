<?php

namespace common\modules\client\models;

use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;

//use clientDepartment\modules\user\models\User;
/**
 * This is the model class for table "clients".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $full_name
 * @property integer $client_type
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
 * @property integer $on_stock
 * @property integer $internal_code_count
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 *
 */
class Client extends ActiveRecord
{
    public $password_repeat;

    /*
    * @var integer status
    * */
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /*
     * @var integer type
     * */
    const CLIENT_TYPE_PERSONAL = 1;
    const CLIENT_TYPE_CORPORATE = 2;
    const CLIENT_TYPE_CORPORATE_CONTRACT = 3;

    /* ID of major corporate clients
     * @var integer type
     *
     **/
    const CLIENT_DEFACTO = 2;
    const CLIENT_TUPPERWARE = 77;
    const CLIENT_MIELE = 93;

    const CLIENT_MACCOFFEEKZ = 78;
    const CLIENT_COLINS = 1;
    const CLIENT_KOTON = 21;
    const CLIENT_AKMARAL = 66;
	const CLIENT_ERENRETAIL = 103;
    /*
     *
     * */
    const ON_STOCK = 0;
    const ON_STOCK_WMS = 1;
    const ON_STOCK_TMS = 2;
    const ON_STOCK_WMS_TMS = 3;

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
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['external-client-self-update'] = ['full_name','phone', 'email', 'password', 'password_repeat'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['legal_company_name','title','on_stock'], 'required'],
            [['on_stock','internal_code_count','user_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted', 'client_type'], 'integer'],
            [['username', 'title','password'], 'string', 'max' => 128],
            [['email'], 'email'],
            [['first_name', 'full_name', 'middle_name', 'last_name', 'phone', 'phone_mobile', 'email'], 'string', 'max' => 64],

            // password rules
            ['password', 'required', 'on'=>'create'],
            ['password', 'string', 'min' => 6],

            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message'=> Yii::t('client/titles', 'Entered passwords does not match')],

            ['username', 'required','on' => ['create', 'update']],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9-_]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 25],
            ['username', 'unique'],
            ['username', 'trim'],
            ['username', 'common\validators\UniqueNameClientValidator'],

            ['email', 'required','on' => ['create', 'update']],
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
            'user_id' => Yii::t('forms', 'User ID'),
            'client_type' => Yii::t('forms', 'Client Type'),
            'password' => Yii::t('forms', 'Password'),
            'password_repeat' => Yii::t('client/forms', 'Password repeat'),
            'username' => Yii::t('forms', 'Client login'),
            'legal_company_name' => Yii::t('forms', 'Legal company name'),
            'title' => Yii::t('forms', 'Client title'),
            'full_name' => Yii::t('forms', 'Full Name'),
            'first_name' => Yii::t('forms', 'First Name'),
            'middle_name' => Yii::t('forms', 'Middle Name'),
            'last_name' => Yii::t('forms', 'Last Name'),
            'phone' => Yii::t('forms', 'Phone'),
            'phone_mobile' => Yii::t('forms', 'Phone Mobile'),
            'email' => Yii::t('forms', 'Email'),
            'status' => Yii::t('forms', 'Status'),
            'on_stock' => Yii::t('forms', 'On stock'),
            'internal_code_count' => Yii::t('forms', 'Internal code count'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Modified User ID'),
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
     * @return array
     */
    public static function getOnStockArray()
    {
        return [
            self::ON_STOCK => Yii::t('forms', 'Не указан'),
            self::ON_STOCK_WMS => Yii::t('forms', 'Склад'),
            self::ON_STOCK_TMS => Yii::t('forms', 'Транспорт'),
            self::ON_STOCK_WMS_TMS => Yii::t('forms', 'Склад и Транспорт'),
        ];
    }

    /**
     * @param integer $value
     * @return string
     */
    public function getOnStockValue($value = null)
    {
        if(is_null($value)){
            $value = $this->on_stock;
        }
        return ArrayHelper::getValue($this->getOnStockArray(), $value);
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatus()
    {
        $status = self::getStatusArray();
        return $status[$this->status];
    }

    /**
     * @return array Массив с типами.
     */
    public static function getClientTypeArray()
    {
        return [
            self::CLIENT_TYPE_PERSONAL => Yii::t('titles', 'Personal'),
            self::CLIENT_TYPE_CORPORATE => Yii::t('titles', 'Corporate'),
            self::CLIENT_TYPE_CORPORATE_CONTRACT => Yii::t('titles', 'Corporate by contract'),
        ];
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

    /*
    * Get list clients in status active
    * @return array
    * */
    public static function getActiveItems()
    {
        return ArrayHelper::map(self::find()->select('id, title')->andWhere(['status'=>self::STATUS_ACTIVE])->asArray()->all(),'id','title');
    }

   /*
    * Get list clients in status active
    * @return array
    * */
    public static function getActiveWMSItems()
    {
        return ArrayHelper::map(self::find()->select('id, title')->andWhere(['status'=>self::STATUS_ACTIVE,'on_stock'=>[self::ON_STOCK_WMS,self::ON_STOCK_WMS_TMS]])->asArray()->all(),'id','title');
    }

      /*
    * Get list clients in status active
    * @return array
    * */
    public static function getActiveByIDs($clientIDs)
    {
        return ArrayHelper::map(self::find()
                                ->select('id, title')
                                ->andWhere([
                                    'status'=>self::STATUS_ACTIVE,
                                    'id'=>$clientIDs
                                ])
                                ->asArray()->all(),
                                'id','title'
                            );
    }

    /*
    * Get list clients in status active
    * @return array
    * */
    public static function getActiveTMSItems()
    {
        return ArrayHelper::map(self::find()->select('id, title')->where(['status'=>self::STATUS_ACTIVE,'on_stock'=>[self::ON_STOCK_TMS,self::ON_STOCK_WMS_TMS]])->asArray()->all(),'id','title');
    }

    /*
   * Get list clients in status active
   * @return array
   * */
    public static function getCorporateClients()
    {
        return ArrayHelper::map(
            self::find()
                ->select('id, title')
                ->andWhere([
                    'status'=>self::STATUS_ACTIVE
                ])
                ->andWhere('client_type != :client_type', [':client_type' => Client::CLIENT_TYPE_PERSONAL])
                ->asArray()
                ->all(),'id','title');
    }

    /*
    *
    * Relation has many with Client employees table
    * */
    public function getEmployees()
    {
        return $this->hasMany(ClientEmployees::className(), ['client_id'=>'id']);
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

    public static function getClientNameByID($clientId = null) {
        $client = self::find()->andWhere(['id'=>$clientId])->one();
        return ($client ? $client->full_name : 'не найден');
    }

    public static function getClientLegalNameByID($clientId = null) {
        $client = self::find()->andWhere(['id'=>$clientId])->one();
        return ($client ? $client->legal_company_name : 'не найден');
    }

    /*
     *
     * */
//    public function getCountInternalCode()
//    {
//        return self::find()->select('internal_code_count')->andWhere(['id'=>$this->id])->scalar();
//    }
}
