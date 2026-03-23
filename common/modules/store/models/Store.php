<?php

namespace common\modules\store\models;

use common\modules\client\models\ClientEmployees;
use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\ActiveRecord;
use common\modules\client\models\Client;
use common\modules\city\models\City;
use common\modules\city\models\Region;
use common\modules\city\models\Country;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "store".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $country_id
 * @property integer $region_id
 * @property integer $city_id
 * @property string $city_lat
 * @property integer $type_use
 * @property integer $owner_type
 * @property string $name
 * @property string $legal_point_name
 * @property string $shopping_center_name
 * @property string $shopping_center_name_lat
 * @property string $contact_first_name
 * @property string $contact_full_name
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
 * @property string $shop_code2
 * @property string $shop_code3
 * @property integer $internal_code
 * @property string $city_prefix
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
    const TYPE_USE_POINT = 5;

    /*
    * @var integer type use
    * */
    const OWNER_TYPE_UNDEFINED = 0;
    const OWNER_TYPE_PERSONAL = 1;
    const OWNER_TYPE_CORPORATE = 2;
    const OWNER_TYPE_CORPORATE_CONTRACT = 3;

    const NOMADEX_MAIN_WAREHOUSE = 4;

    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'timestampBehavior' => [
//                'class' => TimestampBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
//                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
//                ],
//            ],
//        ];
//    }

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
            [['type_use','client_id', 'region_id','country_id', 'city_id', 'street', 'house'], 'required'],
            [['internal_code','type_use', 'owner_type', 'city_id','region_id','country_id','client_id', 'address_type', 'status', 'intercom', 'floor', 'elevator', 'created_at', 'updated_at'], 'integer'],
            [['description', 'comment'], 'string'],
            [['name', 'street','legal_point_name','city_lat'], 'string', 'max' => 128],
            [['contact_first_name', 'contact_full_name', 'contact_middle_name', 'contact_last_name', 'contact_first_name2', 'contact_middle_name2', 'contact_last_name2', 'email', 'phone', 'phone_mobile', 'legal_point_name'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['shop_code','shop_code2','shop_code3','shopping_center_name', 'shopping_center_name_lat'], 'string', 'max' => 128],
            [['zip_code'], 'string', 'max' => 9],
            [['city_prefix'], 'string', 'max' => 4],
            [['house', 'entrance', 'flat'], 'string', 'max' => 6],
            [['email','city_lat'], 'trim'],
            [['internal_code'], 'validateInternalCode']
        ];
    }

    /**
     * Validate Internal code
     */
    public function validateInternalCode($attribute, $params)
    {
       $validInternalCodes = Store::find()->select('internal_code')->andWhere(['client_id'=>$this->client_id, 'type_use'=>self::TYPE_USE_STORE])->asArray()->column();

        if(!in_array($this->internal_code, $validInternalCodes) && !$this->isNewRecord) {
            $this->addError($attribute, Yii::t('outbound/errors','Вы должны использовать уже существующий номер магазина'));
        }
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
            'owner_type' => Yii::t('forms', 'Owner type'),
            'legal_point_name' => Yii::t('forms', 'Legal point name'),
            'city_lat' => Yii::t('forms', 'City (r.a.)'),
            'shopping_center_name' => Yii::t('forms', 'Shopping center name'),
            'shopping_center_name_lat' => Yii::t('forms', 'Shopping center name (r.a.)'),
            'contact_full_name' => Yii::t('forms', 'Contact full name'),
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
            'shop_code2' => Yii::t('forms', 'External store code 2'),
            'shop_code3' => Yii::t('forms', 'External store code 3'),
            'internal_code' => Yii::t('forms', 'Internal code'),
            'city_prefix' => Yii::t('forms', 'Prefix'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['address-book-update'] = ['city_id','street', 'house', 'floor', 'flat', 'contact_full_name', 'phone_mobile', 'description', 'comment'];
        $scenarios['address-book-create'] = ['city_id','street', 'house', 'floor', 'flat', 'contact_full_name', 'phone_mobile', 'description', 'comment'];

        return $scenarios;
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
    public function getStatus($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getStatusArray(),$status);
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getOwnerTypeArray($key = null)
    {
        $data = [
            self::OWNER_TYPE_UNDEFINED => Yii::t('titles', 'Undefined'),
            self::OWNER_TYPE_PERSONAL => Yii::t('titles', 'Personal'),
            self::OWNER_TYPE_CORPORATE => Yii::t('titles', 'Corporate'),
            self::OWNER_TYPE_CORPORATE_CONTRACT => Yii::t('titles', 'Corporate by contract'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getOwnerTypeValue($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue($this->getOwnerTypeArray(),$status);
    }

	public function beforeSave($insert)
    {
		if (parent::beforeSave($insert)) {
            if($this->owner_type == self::OWNER_TYPE_PERSONAL){
                $this->title = $this->getPointTitleByPattern('personal');
                $this->name = $this->title;
                $this->legal_point_name = $this->title;
                return true;
            }
			$clientName = '';
			if($client = Client::findOne($this->client_id)) {
				$clientName = $client->title;
			}
            $cityName = '';
            if($city = City::findOne($this->city_id)) {
				$cityName = $city->name;
			}

		  	$this->title = $cityName . ' '.$this->shopping_center_name.' / '.$this->name.' '.$clientName;
            if(!$insert){
                $dirtyAttributes = $this->dirtyAttributes;
                $oldAttributes = $this->oldAttributes;
                // При изменении кода, ищем магазин с новым кодом и присваеваем ему старый
                if(isset($dirtyAttributes['internal_code']) && isset($oldAttributes['internal_code']) && $dirtyAttributes['internal_code'] != $oldAttributes['internal_code']){
                    $oldStore = Store::find()->andWhere([
                        'client_id' => $this->client_id,
                        'type_use' => self::TYPE_USE_STORE,
                        'internal_code' => $dirtyAttributes['internal_code']
                    ])->one();

                    if($oldStore){
                        $oldStore->updateAttributes(['internal_code' => $oldAttributes['internal_code']]);
                    }
                }
            }
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

    /*
    * Relation has one with Client Employees
    * */
    public function getEmployees()
    {
        return $this->hasMany(ClientEmployees::className(), ['store_id' => 'id']);
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
            self::TYPE_USE_POINT => Yii::t('forms', 'Point'),
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * @return string Читабельный статус поста.
     */
    public function getTypeUseValue($type_use=null)
    {
        if(is_null($type_use)){
            $type_use = $this->type_use;
        }
        return ArrayHelper::getValue($this->getTypeUseArray(),$type_use);
    }

    /*
     *
     * */
    public function getDisplayFullTitle()
    {
        return $this->getPointTitleByPattern('full');
    }

    /*
     * Get Point Title
     * @param integer Point id
     * @return string Pount formatted title
     * */
//    public static function getPointTitle($pintId)
    public static function getPointTitle($pintId)
    {
        $out = '-';

        if($point = self::findOne($pintId)) {

//            $city = (($cityObj = $point->city) ? $cityObj->name : '');
//
//            $legalPointName = !empty($point->legal_point_name) ? $point->legal_point_name : $point->name;
//
//            $shopCode = (!empty($point->shop_code) ? ' '.$point->shop_code : '');
//
//            $shoppingCenterName = ( !empty($point->shopping_center_name) && $point->shopping_center_name != '-' ? ' ' . $point->shopping_center_name : '');
//            $out = $city . ' / ' . $legalPointName . ' ' . $shopCode . ' ' . $shoppingCenterName;
            if(!empty($point->shopping_center_name_lat)){
                $out = $point->getPointTitleByPattern('{city_name} / {legal_point_name} {shop_code} {shopping_center_name_lat} / {shopping_center_name}');
            } else {
                $out = $point->getPointTitleByPattern('{city_name} / {legal_point_name} {shop_code} {shopping_center_name}');
            }


        }

        return $out;
    }
    /*
    * Get Point Title
    * @param integer Point id
    * @return string Pount formatted title
    * */
    public static function getAddressTitle($point_id)
    {
        $out = '-';

        if($point = self::findOne($point_id)) {

            $out = $point->getPointTitleByPattern('personal');
        }

        return $out;
    }

    /**
     * @return string country name
     */
    public function getCountryName($country_id=null){
        if(!is_null($country_id)){
            $country = Country::findOne($country_id);
            if(!empty($country))
                return $country->name;
        }
        return 'Не задан';
    }

    /**
     * @return string region name
     */
    public function getRegionName($region_id=null){
        if(!is_null($region_id)){
            $region = Region::findOne($region_id);
            if(!empty($region))
                return $region->name;
        }
        return 'Не задан';
    }

    /**
     * @return string country name
     */
    public function getCityName($city_id=null){
        if(!is_null($city_id)){
            $city = City::findOne($city_id);
            if(!empty($city))
                return $city->name;
        }
        return 'Не задан';
    }

    /**
     * @return string country name
     */
    public function getClientName($client_id=null){
        if(!is_null($client_id)){
            $client = Client::findOne($client_id);
            if(!empty($client))
                return $client->username;
        }
        return 'Не задан';
    }

    /**
     * Set internal code for Store
     */
    public function setInternalCode(){

            if(empty($this->internal_code) && $this->type_use == Store::TYPE_USE_STORE) {
                if($client = Client::findOne($this->client_id)) {
                    $client->internal_code_count += 1;
                    $this->internal_code = $client->internal_code_count;
                    $this->save(false);
                    $client->save(false);
                }
            }
    }

    /*
      * Array with attribute values functions mapping
      * @return array
      **/
    public function getAttributesValuesMap($attribute)
    {
        $data = [
            'client_id'=>'getClientName',
            'country_id'=>'getCountryName',
            'region_id'=>'getRegionName',
            'city_id'=>'getCityName',
            'type_use'=>'getTypeUseValue',
            'status'=>'getStatus',

        ];

        return ArrayHelper::getValue($data, $attribute);
    }

    /*
     * Формирует адрес точки по указанному шаблону
     * @return string
     **/
    public function getPointTitleByPattern($pattern = null)
    {
        $preset = [];
        $preset['personal'] = '{city_name}, {street} {house}, эт. {floor}, кв. {flat}';
        $preset['stock'] = '<{client_name}> {city_name} / {shop_code} {name}, {shopping_center_name_lat} {shopping_center_name} {street} {house}';
        $preset['full'] = '{city_name} / {name} {shop_code} {shopping_center_name} {street} {house}';
        $preset['default'] = '{city_name} / {shopping_center_name}';
        $preset['small'] = '{city_name} / {name} / {shopping_center_name}';
        $preset['default-1'] = '{city_name} / {name}';
        $preset['default-2'] = '{shopping-center-name}';
		
        if (isset($preset[$pattern])) {
            $string = $preset[$pattern];
        } elseif ($pattern) {
            $string = $pattern;
        } else {
            $string = $preset['default'];
        }

        $string = preg_replace('/{city_name}/', is_object($this->city) ? $this->city->name : '', $string);
        $string = preg_replace('/{client_name}/', is_object($this->client) ? $this->client->title : '', $string);
        $string = preg_replace('/{street}/', $this->street, $string);
        $string = preg_replace('/{house}/', $this->house, $string);
        $string = preg_replace('/{floor}/', $this->floor, $string);
        $string = preg_replace('/{flat}/', $this->flat, $string);
        $string = preg_replace('/{shopping_center_name}/', !empty ($this->shopping_center_name) && $this->shopping_center_name != '-' ? '[ ТЦ ' . $this->shopping_center_name . ' ]': '', $string);
        $string = preg_replace('/{legal_point_name}/', !empty($this->legal_point_name) ? $this->legal_point_name : '', $string);
        $string = preg_replace('/{shopping_center_name_lat}/', !empty($this->shopping_center_name_lat) ? $this->shopping_center_name_lat : '', $string);
        $string = preg_replace('/{shop_code}/', !empty($this->shop_code) ? $this->shop_code : '', $string);
        $string = preg_replace('/{name}/', $this->name, $string);
        $string = preg_replace('/{city_name_lat}/', !empty($this->city_lat) ? $this->city_lat : '', $string);
        $string = preg_replace('/{internal_code}/', !empty($this->internal_code) ? $this->internal_code : '', $string);
		$string = preg_replace('/{shopping-center-name}/', !empty ($this->shopping_center_name) && $this->shopping_center_name != '-' ? $this->shopping_center_name : '', $string);

        return $string;
    }

    public static function findClientStoreByShopCode($client_id, $shop_code)
    {
        $shopCodeFind = mb_strtoupper($shop_code,'utf-8');
        file_put_contents('findClientStoreByShopCode.log',$shopCodeFind."\n",FILE_APPEND);
        if($shop = Store::find()->andWhere(['client_id' => $client_id,'shop_code'=>$shopCodeFind])->one()){
            return $shop->id;
        }
        return false;
    }

	public static function findClientStoreByShopCodeForECom($shop_code)
	{
		if($shop = Store::find()->andWhere(['client_id' => [2,3],'shop_code2'=>intval($shop_code)])->one()){
			return $shop->getPointTitleByPattern("full");
		}
		return "магазин не найден";
	}

	public static function getStoreByShopCodeForECom($shop_code)
	{
		return Store::find()->andWhere(['client_id' => [2,3],'shop_code2'=>intval($shop_code)])->one();
	}
}
