<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 12:23
 */
namespace common\ecommerce\main\outbound\forms;


use common\ecommerce\main\outbound\repository\OutboundRepository;
use Yii;
use yii\base\Model;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

class OutboundOrderUploadForm extends Model
{
    /**
     * @var $originalOrderFile
     */
    public $originalOrderFile;
    public $pathToOriginalOrderFile;
    /**
     * @var $preparedOrderFile
     */
    public $preparedOrderFile;
    public $pathToPreparedOrderFile;

    public $orderNumber;
    public $storeId;
    public $comment;

    private $clientID;
    private $validation;
    private $outboundRepository;

    //
    public function __construct($config = [],$params = []) {
        parent::__construct($config);
        $this->validation = new \common\ecommerce\main\outbound\validation\OutboundOrderUploadValidation($config,$params);
        $this->outboundRepository = new OutboundRepository($params);
        $this->clientID =  $this->outboundRepository->getClientID();
    }


    public function attributeLabels()
    {
        return [
            'originalOrderFile' => Yii::t('outbound/forms', 'Оригинальный файл в формате эксель'),
            'preparedOrderFile' => Yii::t('outbound/forms', 'Подготовленный файл в формате эксель'),
            'orderNumber' => Yii::t('outbound/forms', 'Номер накладной'),
            'storeId' => Yii::t('outbound/forms', 'Получатель'),
            'comment' => Yii::t('inbound/forms', 'Комментарий'),
        ];
    }

    public function rules()
    {
        return [
            ['originalOrderFile', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => 'xlsx, xls','on'=>'onCreate'],

            ['preparedOrderFile', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => 'xlsx, xls','on'=>'onCreate'],

            [['orderNumber'], 'required','on'=>'onCreate'],
            [['orderNumber'], 'string','on'=>'onCreate'],
            [['orderNumber'], 'validateIsOrderExist','on'=>'onCreate'],

            [['storeId'], 'required','on'=>'onCreate'],
            [['storeId'], 'integer','on'=>'onCreate'],

            [['comment'], 'trim','on'=>'onCreate'],
            [['comment'], 'string','on'=>'onCreate'],
        ];
    }

    public function validateIsOrderExist($attribute,$params) {
        $orderNumber = $this->orderNumber;
        if($this->validation->isOrderExist($orderNumber)) {
            $this->addError($attribute, '<b> ['.$orderNumber.'] </b> ' . Yii::t('outbound/errors','Накладная с таким номером уже существует') );
        }
    }

    public function saveFileAndPreparedData() {
        $this->originalOrderFile = UploadedFile::getInstance($this, 'originalOrderFile');
        $this->preparedOrderFile = UploadedFile::getInstance($this, 'preparedOrderFile');

        $this->pathToOriginalOrderFile = $this->makeUploadPathOriginal();
        $this->pathToPreparedOrderFile = $this->makeUploadPathPrepared();

        $this->originalOrderFile->saveAs($this->pathToOriginalOrderFile);
        $this->preparedOrderFile->saveAs($this->pathToPreparedOrderFile);

        return true;
    }

    private function makeUploadPathOriginal() {
        return $this->createUploadDir().'/'.$this->originalOrderFile->getBaseName() . '-orig.' . $this->originalOrderFile->getExtension();
    }

    private function makeUploadPathPrepared() {
        return  $this->createUploadDir().'/'.$this->preparedOrderFile->getBaseName() . '-prep.' . $this->preparedOrderFile->getExtension();
    }

    private function createUploadDir() {
        $dateUnique = date('Ymd');
        $timeUnique = date('Ymd').date('His');
        $dirPath = 'uploads/' .$this->clientID. '/outbound-order/'.$dateUnique.'/'.$timeUnique;
        BaseFileHelper::createDirectory($dirPath);
        return $dirPath;
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->pathToOriginalOrderFile = $this->pathToOriginalOrderFile;
        $dto->pathToPreparedOrderFile = $this->pathToPreparedOrderFile;
        $dto->orderNumber = $this->orderNumber;
        $dto->storeId = $this->storeId;
        $dto->comment = $this->comment;
        return $dto;
    }

    public function getClientID() {
        return $this->clientID;
    }
}