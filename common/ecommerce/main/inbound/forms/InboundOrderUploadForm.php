<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 12:23
 */

namespace common\ecommerce\main\inbound\forms;

use common\ecommerce\main\inbound\repository\InboundRepository;
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
    public $comment;

    private $clientID;
    private $validation;
    private $inboundRepository;

    //
    public function __construct($config = [],$params = []) {
        parent::__construct($config);
        $this->validation = new \common\ecommerce\main\inbound\validation\InboundOrderUploadValidation($config,$params);
        $this->inboundRepository = new InboundRepository($params);
        $this->clientID =  $this->inboundRepository->getClientID();
    }


    public function attributeLabels()
    {
        return [
            'originalOrderFile' => Yii::t('inbound/forms', 'Оригинальный файл в формате эксель'),
            'preparedOrderFile' => Yii::t('inbound/forms', 'Подготовленный файл в формате эксель'),
            'orderNumber' => Yii::t('inbound/forms', 'Номер приходной накладной'),
            'supplierId' => Yii::t('inbound/forms', 'Поставщик'),
            'comment' => Yii::t('inbound/forms', 'Комментарий'),
        ];
    }

    public function rules()
    {
        return [
            ['originalOrderFile', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => 'xls, xlsx, csv','on'=>'onCreate'],
            ['preparedOrderFile', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => 'xls, xlsx','on'=>'onCreate'],

            [['orderNumber'], 'required','on'=>'onCreate'],
            [['orderNumber'], 'string','on'=>'onCreate'],
            [['orderNumber'], 'validateIsOrderExist','on'=>'onCreate'],

            [['comment'], 'trim','on'=>'onCreate'],
            [['comment'], 'string','on'=>'onCreate'],

//            [['supplierId'], 'required','on'=>'onCreate'],
//            [['supplierId'], 'integer','on'=>'onCreate'],
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
        $dto->orderNumber = $this->orderNumber;
        $dto->supplierId = 1;
        $dto->comment = $this->comment;
        return $dto;
    }
}