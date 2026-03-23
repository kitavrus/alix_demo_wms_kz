<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\clientObject\main\inbound\forms;

use common\clientObject\main\inbound\validation\BoxToBoxValidation;
use Yii;
use yii\base\Model;

class BoxToBoxForm extends Model
{
    private $validation;

    public $fromBox;
    public $productBarcode;
    public $toBox;

    //
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->validation = new BoxToBoxValidation();
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
            // Product barcode
            [['fromBox', 'productBarcode'], 'required', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'string', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'trim', 'on' => 'onProductBarcode'],
            [['productBarcode'], 'validateProductBarcode', 'on' => 'onProductBarcode'],
            // To box
            [['fromBox','productBarcode', 'toBox'], 'required', 'on' => 'onToBox'],
            [['toBox'], 'string', 'on' => 'onToBox'],
            [['toBox'], 'trim', 'on' => 'onToBox'],
            [['toBox'], 'validateToBox', 'on' => 'onToBox'],
        ];
    }
    //
    public function validateFromBox($attribute,$params)
    {
        $fromBox = $this->fromBox;

        if(!$this->validation->isBoxNotEmpty($fromBox)) {
            $this->addError($attribute, '<b>[' . $fromBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб пуст'));
        }
    }
    //
    public function validateProductBarcode($attribute, $params)
    {
        $fromBox = $this->fromBox;
        $productBarcode = $this->productBarcode;

        if(!$this->validation->isProductExistInBox($productBarcode,$fromBox)) {
            $this->addError($attribute, '<b>[' . $fromBox . ']</b> ' . Yii::t('inbound/errors', 'Этого товара нет в этом коробе'));
        }
    }
    //
    public function validateToBox($attribute,$params)
    {
        $toBox = $this->toBox;
        if(!$this->validation->isBoxOnPlace($toBox)) {
            $this->addError($attribute, '<b>[' . $toBox . ']</b> ' . Yii::t('inbound/errors', 'Этот короб не размещен'));
        }
    }
    //
    public function attributeLabels()
    {
        return [
            'fromBox' => Yii::t('inbound/forms', 'Из короба'),
            'productBarcode' => Yii::t('inbound/forms', 'ШК товара'),
            'toBox' => Yii::t('inbound/forms', 'В короб'),
        ];
    }

    public function getDTO() {
        $dto = new \stdClass();
        $dto->fromBox = $this->fromBox;
        $dto->productBarcode = $this->productBarcode;
        $dto->toBox = $this->toBox;
        return $dto;
    }
}