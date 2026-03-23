<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */
namespace common\ecommerce\defacto\changeAddressPlace\forms;

use common\ecommerce\defacto\changeAddressPlace\validation\Validation;
use Yii;
use yii\base\Model;

class BoxToBoxForm extends Model
{
    private $validation;

    public $fromBox;
    public $toBox;

    //
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new Validation();
    }
    //
    public function rules()
    {
        return [
            // From Box
            [['fromBox'], 'required', 'on' => 'onFromBox'],
            [['fromBox'], 'string', 'on' => 'onFromBox'],
            [['fromBox'], 'trim', 'on' => 'onFromBox'],
            [['fromBox'], 'validateFromBox', 'on' => 'onFromBox'],
            // To box
            [['fromBox','toBox'], 'required', 'on' => 'onToBox'],
            [['toBox'], 'string', 'on' => 'onToBox'],
            [['toBox'], 'trim', 'on' => 'onToBox'],
            [['toBox'], 'validateToBox', 'on' => 'onToBox'],
        ];
    }
    //
    public function validateFromBox($attribute,$params)
    {
        $fromBox = $this->fromBox;
		
		        if(!$this->validation->ourBoxBarcode($fromBox)) {
            $this->addError($attribute, '<b>[' . $fromBox . ']</b> ' . Yii::t('inbound/errors', 'Это не наш короб'));
            return;
        }

        if(!$this->validation->isBoxNotEmpty($fromBox)) {
            $this->addError($attribute, '<b>[' . $fromBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст'));
        }
    }

    //
    public function validateToBox($attribute,$params)
    {
        $toBox = $this->toBox;
		
		
		if(!$this->validation->ourBoxBarcode($toBox)) {
            $this->addError($attribute, '<b>[' . $toBox . ']</b> ' . Yii::t('inbound/errors', 'Это не наш короб'));
            return;
        }
		
        if(!$this->validation->isBoxOnPlace($toBox)) {
            //$this->addError($attribute, '<b>[' . $toBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб не размещен'));
        }
    }
    //
    public function attributeLabels()
    {
        return [
            'fromBox' => Yii::t('inbound/forms', 'Из короба'),
            'toBox' => Yii::t('inbound/forms', 'В короб'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->fromBox = $this->fromBox;
        $dto->toBox = $this->toBox;
        return $dto;
    }
}