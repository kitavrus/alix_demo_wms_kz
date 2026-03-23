<?php

namespace common\modules\broker\models;
use common\modules\client\models\Client;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\helpers\ArrayHelper;
use common\modules\broker\models\CustomsAccount;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\modules\broker\models\CustomsOrderDocument;
use common\helpers\iHelper;

/**
 * This is the model class for table "customs_orders".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $tl_delivery_proposals_id
 * @property integer $customs_accounts_id
 * @property string $order_number
 * @property string $from
 * @property string $to
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CustomsOrder extends \common\models\ActiveRecord
{
    /*
    * @var integer status
    * */
    const STATUS_UNDEFINED = 0; //не указан
    const STATUS_NEW = 1; //новый
    const STATUS_DOCUMENT_WAIT = 2; //ждем документы
    const STATUS_DOCUMENT_ADDED = 3; //документы добавлены
    const STATUS_DOCUMENT_WRONG = 4; //ошибка в документах
    const STATUS_DOCUMENT_CONFIRMED = 5; //документы в порядке
    const STATUS_COMPLETE = 6; //выполнен

    public $files;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customs_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'tl_delivery_proposals_id', 'customs_accounts_id', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['order_number', 'from', 'to'], 'string', 'max' => 255],
            [['files'], 'file','maxFiles' => 10],
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
            'from' => Yii::t('customs/forms', 'From'),
            'to' => Yii::t('customs/forms', 'To'),
            'tl_delivery_proposals_id' => Yii::t('customs/forms', 'Tl Delivery Proposals ID'),
            'customs_accounts_id' => Yii::t('customs/forms', 'Customs Accounts ID'),
            'order_number' => Yii::t('customs/forms', 'Order Number'),
            'status' => Yii::t('forms', 'Status'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
            'files' => Yii::t('forms', Yii::t('customs/forms', 'Attached files')),

        ];
    }

    /**
     * @return array Массив с статусами.
     */
    public static function getStatusArray()
    {
        $data = [
            self::STATUS_NEW => Yii::t('customs/titles', 'New'), //Новый
            self::STATUS_DOCUMENT_WAIT => Yii::t('customs/titles', 'Wait for documents'),
            self::STATUS_DOCUMENT_ADDED => Yii::t('customs/titles', 'Documents added'),
            self::STATUS_DOCUMENT_WRONG => Yii::t('customs/titles', 'Documents wrong'),
            self::STATUS_DOCUMENT_CONFIRMED => Yii::t('customs/titles', 'Documents confirmed'),
            self::STATUS_COMPLETE => Yii::t('customs/titles', 'Completed'),

        ];

        return  $data;
    }

    /**
     * @return string Читабельный статус.
     */
    public function getStatusValue($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }
        return ArrayHelper::getValue(self::getStatusArray(),$status);
    }

    /**
     * Relation with client
     **/
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * Relation has one with DeliveryProposal
     **/
    public function getDeliveryProposal()
    {
        return $this->hasOne(TlDeliveryProposal::className(), ['id' => 'tl_delivery_proposals_id']);
    }

    /**
     * Relation has one with CustomsAccount
     **/
    public function getCustomsAccount()
    {
        return $this->hasOne(CustomsAccount::className(), ['id' => 'customs_accounts_id']);
    }

    /*
     * Relation has many with CustomsOrderDocuments
     **/
    public function getDocuments()
    {
        return $this->hasMany(CustomsOrderDocument::className(), ['customs_orders_id' => 'id']);
    }

    /**
     * @return string Display value
     */
    public function saveFiles($formModel=null)
    {
        if(is_null($formModel)){
            $formModel = $this;
        }
        $this->files = UploadedFile::getInstances($formModel, 'files');
        $dirPath = 'uploads/attached-files/customs-orders/'.$this->id.'/' . date('Ymd'). '/' . date('H-i');

        if($this->files){
            BaseFileHelper::createDirectory($dirPath);

            foreach ($this->files as $file) {
                $fileToPath = $dirPath . '/' . iHelper::transliterate($file->name);
                $file->saveAs($fileToPath);

                if(file_exists($fileToPath)){
                    $customsDocument = new CustomsOrderDocument();
                    $customsDocument->customs_orders_id = $this->id;
                    $customsDocument->filename = $fileToPath;
                    $customsDocument->save(false);
                }
            }

        }

    }
}
