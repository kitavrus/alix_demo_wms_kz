<?php

namespace stockDepartmentartment\modules\storestockDepartmenttockDepartmentrontend\modulestockDepartment\modelstockDepartment;
use frontend\mostockDepartmentansportLogiststockDepartmentls\City;
usestockDepartmentd\modules\transportLstockDepartment\models\Region;
use frontend\modules\transportLogistics\models\Country;
use Yii;
use common\models\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "store".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $country_d
 * @property integer $region_id
 * @property integer $city_id
 * @property integer $type_use
 * @property string $name
 * @property string $shopping_center_name
 * @property string $contact_first_name
 * @property string $contact_middle_name
 * @property string $contact_last_name
 * @property string $contact_first_name2
 * @property string $contact_middle_name2
 * @property string $contact_last_name2
 * @property string $email
 * @property string $phone
 * @property string $phone_mobile
 * @property string $title
 * @property string $description
 * @property integer $address_type
 * @property integer $status
 * @property string $zip_code
 * @property string $street
 * @property string $house
 * @property string $entrance
 * @property string $flat
 * @property integer $intercom
 * @property integer $floor
 * @property integer $elevator
 * @property string $comment
 * @property string $shop_code
 * @property integer $created_at
 * @property integer $updated_at
 */
class Store extends ActiveRecord
{
    /*
     * @var integer status
     * */
    const STATUS_NOT_DEFINED = 0;
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    /*
     * @var integer type use
     * */
    const TYPE_USE_UNDEFINED = 0;
    const TYPE_USE_STORE = 1;
    const TYPE_USE_STOCK = 2;
    const TYPE_USE_AIRPORT = 3;
    const TYPE_USE_TRAIN_STATION = 4;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id','country_id','type_use','client_id', 'name', 'contact_first_name', 'contact_middle_name', 'contact_last_name', 'country', 'city_id', 'street', 'house' ], 'required'],
            [['type_use','city_id','region_id','country_id','client_id', 'address_type', 'status', 'intercom', 'floor', 'elevator', 'created_at', 'updated_at'], 'integer'],
            [['description', 'comment'], 'string'],
            [['name', 'street'], 'string', 'max' => 128],
            [['contact_first_name', 'contact_middle_name', 'contact_last_name', 'contact_first_name2', 'contact_middle_name2', 'contact_last_name2', 'email', 'phone', 'phone_mobile', 'shop_code'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['shopping_center_name'], 'string', 'max' => 128],
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
            'id' => Yii::t('forms', 'ID'),
            'client_id' => Yii::t('forms', 'Client ID'),
            'name' => Yii::t('forms', 'Name'),
            'type_use' => Yii::t('forms', 'Type use'),
            'shopping_center_name' => Yii::t('forms', 'Shopping center name'),
            'contact_first_name' => Yii::t('forms', 'Contact first name'),
            'contact_middle_name' => Yii::t('forms', 'Contact middle name'),
            'contact_last_name' => Yii::t('forms', 'Contact last name'),
            'contact_first_name2' => Yii::t('forms', 'Add. contact first name'),
            'contact_middle_name2' => Yii::t('forms', 'Add. contact middle name'),
            'contact_last_name2' => Yii::t('forms', 'Add. contact last name'),
            'email' => Yii::t('forms', 'Email'),
            'phone' => Yii::t('forms', 'Store phone'),
            'phone_mobile' => Yii::t('forms', 'Store phone mobile'),
            'title' => Yii::t('forms', 'Title'),
            'description' => Yii::t('forms', 'Description'),
            'address_type' => Yii::t('forms', 'Address Type'),
            'status' => Yii::t('forms', 'Status'),
            'country_id' => Yii::t('forms', 'Country'),
            'region_id' => Yii::t('forms', 'Region'),
            'city_id' => Yii::t('forms', 'City'),
            'zip_code' => Yii::t('forms', 'Zip Code'),
            'street' => Yii::t('forms', 'Street'),
            'house' => Yii::t('forms', 'House'),
            'entrance' => Yii::t('forms', 'Entrance'),
            'flat' => Yii::t('forms', 'Flat'),
            'intercom' => Yii::t('forms', 'Intercom'),
            'floor' => Yii::t('forms', 'Floor'),
            'elevator' => Yii::t('forms', 'Elevator'),
            'comment' => Yii::t('forms', 'Comment'),
            'shop_code' => Yii::t('forms', 'External store code'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray($key = null)
    {
        $data = [
            self::STATUS_NOT_DEFINED => Yii::t('forms', 'Undefined'),
            self::STATUS_ACTIVE => Yii::t('forms', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('forms', 'Not active'),
            self::STATUS_DELETED => Yii::t('forms', 'Deleted'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getStatus()
    {
        $status = self::getStatusArray();
        return $status[$this->status];
    }

	public function beforeSave($insert)
    {
		if (parent::beforeSave($insert)) {
			$clientName = '';
			if($client = Client::findOne($this->client_id)) {
				$clientName = $client->username;
			}
            $cityName = '';
            if($city = City::findOne($this->city_id)) {
				$cityName = $city->name;
			}

		  	$this->title = $cityName . ' '.$this->shopping_center_name.' / '.$this->name.' '.$clientName;

			return true;
		} else {
		 	return false;
		}
	}

    /*
    * Relation with Client table
    * */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
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
        return $this->hasOne(City::className(), ['id' => 'city_id'])->orderBy('name');
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getTypeUseArray($key = null)
    {
        $data = [
            self::TYPE_USE_UNDEFINED => Yii::t('forms', 'Undefined'),
            self::TYPE_USE_STORE => Yii::t('forms', 'Store'),
            self::TYPE_USE_STOCK => Yii::t('forms', 'Stock'),
            self::TYPE_USE_AIRPORT => Yii::t('forms', 'Airport'),
            self::TYPE_USE_TRAIN_STATION => Yii::t('forms', 'Train station'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /*
     *
     * */
    public function getDisplayFullTitle()
    {
        return $this->city->name. ' / ' . $this->name . ' '.(!empty($this->shop_code) ? $this->shop_code : '').' '. ((!empty($this->shopping_center_name) && $this->shopping_center_name != '-')  ? '  [ ТЦ ' . $this->shopping_center_name . ' ] ' : '') . ' / '.$this->street;
    }
}
