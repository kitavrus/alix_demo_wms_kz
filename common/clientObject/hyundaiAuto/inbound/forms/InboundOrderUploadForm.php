<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 12:23
 */

namespace common\clientObject\hyundaiAuto\inbound\forms;

use common\clientObject\hyundaiAuto\inbound\repository\InboundRepository;
use Yii;
use yii\base\Model;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class InboundOrderUploadForm extends Model
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
    public $supplierId;

    private $clientID;
    private $validation;
    private $inboundRepository;

    //
    public function __construct($config = []) {
        parent::__construct($config);
        $this->validation = new \common\clientObject\hyundaiAuto\inbound\validation\InboundOrderUploadValidation();
        $this->inboundRepository = new InboundRepository();
        $this->clientID =  $this->inboundRepository->getClientID();
    }


    public function attributeLabels()
    {
        return [
            'originalOrderFile' => Yii::t('inbound/forms', 'Оригинальный файл в формате эксель'),
            'preparedOrderFile' => Yii::t('inbound/forms', 'Подготовленный файл в формате эксель'),
            'orderNumber' => Yii::t('inbound/forms', 'Номер приходной накладной'),
            'supplierId' => Yii::t('inbound/forms', 'Поставщик'),
        ];
    }

    public function rules()
    {
        return [
            ['originalOrderFile', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => 'xls, xlsx, csv','on'=>'onCreate'],
            ['preparedOrderFile', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => 'xlsx, xls','on'=>'onCreate'],

            [['orderNumber'], 'required','on'=>'onCreate'],
            [['orderNumber'], 'string','on'=>'onCreate'],
            [['orderNumber'], 'trim','on'=>'onCreate'],
            [['orderNumber'], 'validateIsOrderExist','on'=>'onCreate'],

            [['supplierId'], 'required','on'=>'onCreate'],
            [['supplierId'], 'integer','on'=>'onCreate'],
        ];
    }

    public function validateIsOrderExist($attribute,$params) {
        $orderNumber = $this->orderNumber;
        if($this->validation->isOrderExist($orderNumber)) {
            $this->addError($attribute, '<b> ['.$orderNumber.'] </b> ' . Yii::t('inbound/errors','Накладная с таким номером уже существует') );
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
        $dirPath = 'uploads/' .$this->clientID. '/inbound-order/'.$dateUnique.'/'.$timeUnique;
        BaseFileHelper::createDirectory($dirPath);
        return $dirPath;
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->pathToOriginalOrderFile = $this->pathToOriginalOrderFile;
        $dto->pathToPreparedOrderFile = $this->pathToPreparedOrderFile;
        $dto->orderNumber = trim($this->orderNumber);
        $dto->supplierId = $this->supplierId;
        return $dto;
    }
}