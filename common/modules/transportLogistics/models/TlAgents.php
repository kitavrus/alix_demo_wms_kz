<?php

namespace common\modules\transportLogistics\models;

use Yii;
use common\models\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\modules\transportLogistics\transportLogistics;
use common\modules\city\models\City;
use common\modules\transportLogistics\models\TlAgentEmployees;
use common\modules\city\models\Country;
use common\modules\city\models\Region;
/**
 * This is the model class for table "tl_agents".
 *
 * @property integer $id
 * @property integer $country_d
 * @property integer $region_id
 * @property integer $city_id
 * @property string $name
 * @property string $title
 * @property string $phone
 * @property string $phone_mobile
 * @property string $description
 * @property integer $status
 * @property integer $payment_period
 * @property string $contact_first_name
 * @property string $contact_middle_name
 * @property string $contact_last_name
 * @property string $contact_phone
 * @property string $contact_phone_mobile
 * @property string $contact_first_name2
 * @property string $contact_middle_name2
 * @property string $contact_last_name2
 * @property string $contact_phone2
 * @property string $contact_phone_mobile2
 * @property string $address_title
 * @property string $zip_code
 * @property string $street
 * @property string $house
 * @property string $entrance
 * @property string $flat
 * @property integer $intercom
 * @property integer $floor
 * @property integer $flag_nds
 * @property string $comment
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class TlAgents extends ActiveRecord
{
    /*
     * @var integer status
     * */
    const STATUS_ACTIVE = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /*
    * @var integer status
    * */
    const FLAG_NDS_UNDEFINED = 0;
    const FLAG_NDS_TRUE = 2;
    const FLAG_NDS_FALSE= 1;

    /*
     * @var payment_period
     * */
    const MONTH_PAYMENT_PERIOD = 1;
    const WEEK_PAYMENT_PERIOD = 2;
    const TWO_IN_MONTH_PAYMENT_PERIOD = 3;

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray($key=null)
    {
        $data = [
            self::STATUS_ACTIVE => Yii::t('transportLogistics/forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('transportLogistics/forms', 'Not active'),
            self::STATUS_DELETED => Yii::t('transportLogistics/forms', 'Deleted'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tl_agents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'comment'], 'string'],
            [['payment_period','created_user_id', 'updated_user_id','city_id','region_id','country_id','status', 'intercom', 'floor', 'flag_nds'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'street'], 'string', 'max' => 128],
            [['title'], 'string', 'max' => 255],
            [['phone', 'phone_mobile', 'contact_first_name', 'contact_middle_name', 'contact_last_name', 'contact_phone', 'contact_phone_mobile', 'contact_first_name2', 'contact_middle_name2', 'contact_last_name2', 'contact_phone2', 'contact_phone_mobile2'], 'string', 'max' => 64],
            [['address_title'], 'string', 'max' => 256],
            [['zip_code'], 'string', 'max' => 9],
            [['house', 'entrance', 'flat'], 'string', 'max' => 6]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payment_period' => Yii::t('transportLogistics/forms', 'Payment period'),
            'id' => Yii::t('transportLogistics/forms', 'ID'),
            'name' => Yii::t('transportLogistics/forms', 'Name'),
            'title' => Yii::t('transportLogistics/forms', 'Title'),
            'phone' => Yii::t('transportLogistics/forms', 'Main phone'),
            'phone_mobile' => Yii::t('transportLogistics/forms', 'Mobile phone'),
            'description' => Yii::t('transportLogistics/forms', 'Description'),
            'status' => Yii::t('transportLogistics/forms', 'Status'),
            'flag_nds' => Yii::t('transportLogistics/forms', 'Flag NDS'),
            'contact_first_name' => Yii::t('transportLogistics/forms', 'Contact first name'),
            'contact_middle_name' => Yii::t('transportLogistics/forms', 'Contact middle name'),
            'contact_last_name' => Yii::t('transportLogistics/forms', 'Contact last name'),
            'contact_phone' => Yii::t('transportLogistics/forms', 'Phone contact'),
            'contact_phone_mobile' => Yii::t('transportLogistics/forms', 'Mobile phone contact'),
            'contact_first_name2' => Yii::t('transportLogistics/forms', 'Add. contact first name'),
            'contact_middle_name2' => Yii::t('transportLogistics/forms', 'Add. contact middle name'),
            'contact_last_name2' => Yii::t('transportLogistics/forms', 'Add. contact last name'),
            'contact_phone2' => Yii::t('transportLogistics/forms', 'Add. phone contact'),
            'contact_phone_mobile2' => Yii::t('transportLogistics/forms', 'Add. mobile phone contact'),
            'address_title' => Yii::t('transportLogistics/forms', 'Address Title'),
            'country_id' => Yii::t('transportLogistics/forms', 'Country'),
            'region_id' => Yii::t('transportLogistics/forms', 'Region'),
            'city_id' => Yii::t('transportLogistics/forms', 'City'),
            'zip_code' => Yii::t('transportLogistics/forms', 'Zip Code'),
            'street' => Yii::t('transportLogistics/forms', 'Street'),
            'house' => Yii::t('transportLogistics/forms', 'House'),
            'entrance' => Yii::t('transportLogistics/forms', 'Entrance'),
            'flat' => Yii::t('transportLogistics/forms', 'Flat'),
            'intercom' => Yii::t('transportLogistics/forms', 'Intercom'),
            'floor' => Yii::t('transportLogistics/forms', 'Floor'),
            'comment' => Yii::t('transportLogistics/forms', 'Comment'),
            'created_at' => Yii::t('transportLogistics/forms', 'Created At'),
            'updated_at' => Yii::t('transportLogistics/forms', 'Updated At'),
            'created_user_id' => Yii::t('transportLogistics/forms', 'Created User ID'),
            'updated_user_id' => Yii::t('transportLogistics/forms', 'Updated User ID'),
        ];
    }

    /*
     * @return array with store id=>title
     */
    public static function getAgentsArray($key = null)
    {
        $data =  ArrayHelper::map(self::find()->orderBy('name')->all(), 'id', 'name');

        return isset($data[$key]) ? $data[$key] : $data;
    }

    /*
    * @return array with store id=>title
    */
    public static function getActiveAgentsArray($key = null)
    {
        $data =  ArrayHelper::map(self::find()->andWhere(['status'=>self::STATUS_ACTIVE])->orderBy('name')->all(), 'id', 'name');

        return isset($data[$key]) ? $data[$key] : $data;
    }

   /*
     * @return string with store ititle
     */
    public static function getAgentValue($key = null)
    {
        $data =  ArrayHelper::map(self::find()->orderBy('name')->all(), 'id', 'name');

        return isset($data[$key]) ? $data[$key] : '-';
    }

    /*
    *
    * Relation has many with Cars table
    * */
    public function getCars()
    {
        // TODO Rename column to tl_agent_id
        return $this->hasMany(TlCars::className(), ['agent_id'=>'id']);
//        return $this->hasMany(TlCars::className(), ['tl_agent_id'=>'id']);
    }

    /*
    * Relation has one with Country
    * */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /*
    * Relation has one with Region
    * */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /*
    * Relation has one with City
    * */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /*
    *
    * Relation has many with Agent employees table
    * */
    public function getEmployees()
    {
        return $this->hasMany(TlAgentEmployees::className(), ['tl_agent_id'=>'id']);
    }

    /*
     * @return array NDS values
     */
    public static function getNdsFlagArray()
    {
        $data =  [
            self::FLAG_NDS_FALSE => Yii::t('transportLogistics/titles', 'No NDS'),
            self::FLAG_NDS_TRUE => Yii::t('transportLogistics/titles', 'NDS'),
        ];

        return $data;
    }

    /*
   * @return string NDS value
   */
    public function getNdsFlagValue($nds = null)
    {
        if(is_null($nds)){
            $nds = $this->flag_nds;
        }

        return ArrayHelper::getValue(self::getNdsFlagArray(), $nds);
    }

    /*
     * @return array Payment period values
     */
    public static function getPaymentPeriodArray()
    {
        $data =  [
            self::MONTH_PAYMENT_PERIOD => Yii::t('transportLogistics/titles', 'One month'),
            self::WEEK_PAYMENT_PERIOD => Yii::t('transportLogistics/titles', 'One week'),
            self::TWO_IN_MONTH_PAYMENT_PERIOD => Yii::t('transportLogistics/titles', 'TWO_IN_MONTH'),
        ];

        return $data;
    }

    /*
   * @return string Payment period value
   */
    public function getPaymentPeriodValue($payment_period = null)
    {
        if(is_null($payment_period)){
            $payment_period = $this->payment_period;
        }

        return ArrayHelper::getValue(self::getPaymentPeriodArray(), $payment_period);
    }
}