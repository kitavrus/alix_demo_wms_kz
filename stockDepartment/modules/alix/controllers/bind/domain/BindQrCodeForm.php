<?php

namespace stockDepartment\modules\alix\controllers\bind\domain;

use Yii;
use yii\base\Model;
use common\modules\stock\models\Stock;
use common\modules\codebook\models\Codebook;


class BindQrCodeForm extends Model
{
    public $box_barcode;
    public $product_barcode;
    public $our_product_barcode;
    public $bind_qr_code;

    /**
     * @var integer
     */
    const INBOUND_BOX_BARCODE_PREFIX = '5000';
    const INBOUND_BOX_BARCODE_LENGTH = 12;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['box_barcode'], 'required'],
            [['box_barcode', 'product_barcode', 'our_product_barcode', 'bind_qr_code'], 'string'],
            [['box_barcode', 'product_barcode', 'our_product_barcode', 'bind_qr_code'], 'trim'],

            [['box_barcode', 'product_barcode'], 'validateBoxBarcode', 'on' => 'ScannedBox'],

            [['box_barcode', 'product_barcode'], 'required', 'on' => 'ScannedProduct'],
            [['product_barcode'], 'validateProductBarcode'],
            [['product_barcode'], 'validateProductBarcode', 'on' => 'ScannedProduct'],

            [['box_barcode', 'product_barcode', 'our_product_barcode'], 'required', 'on' => 'ScanOurProduct'],
            [['our_product_barcode'], 'validateOurProductBarcode', 'on' => 'ScanOurProduct'],

            [['box_barcode', 'product_barcode', 'our_product_barcode', 'bind_qr_code'], 'required', 'on' => 'BindQrCode'],
            ['bind_qr_code', 'validateBindQrCode', 'on' => 'BindQrCode'],
        ];
    }

    public function validateBoxBarcode($attribute, $params)
    {
        $boxBarcode = $this->$attribute;

        // Проверка на то, что строка состоит только из цифр
        if (!ctype_digit($boxBarcode)) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', 'ШК короба должен содержать только цифры'));
            return;
        }

        // Проверка длины
        if (strlen($boxBarcode) != self::INBOUND_BOX_BARCODE_LENGTH) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', 'ШК короба должен быть ровно 12 символов длиной'));
            return;
        }

        // Проверка на то, что строка начинается с "5000"
        if (substr($boxBarcode, 0, 4) != self::INBOUND_BOX_BARCODE_PREFIX) {
            $this->addError($attribute, '<b>[' . $boxBarcode . ']</b> ' . Yii::t('outbound/errors', 'ШК короба должен начинаться с 5000'));
            return;
        }
    }

    public function validateProductBarcode($attribute, $params)
    {
        $productBarcode = $this->$attribute;
        $box_barcode = $this->box_barcode;

        if (!self::checkProductBarcode($productBarcode)) {
            $this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t('inbound/errors', 'Такого ШК товара не существует'));
            return;
        }

        if (!self::checkProductInBox($productBarcode, $box_barcode)) {
            $this->addError($attribute, '<b> [ ' . $productBarcode . ' ] </b> ' . Yii::t('inbound/errors', 'Этого товара нет в указанном коробе'));
            return;
        }
    }

    public function validateOurProductBarcode($attribute, $params)  {
        $ourProductBarcode = $this->our_product_barcode;
        $productBarcode = $this->product_barcode;
        $box_barcode = $this->box_barcode;

        if (!$this->isOurProduct($ourProductBarcode)) {
            $this->addError($attribute, '<b>[' . $ourProductBarcode . ']</b> ' . Yii::t('inbound/errors', 'ШК товара не соответствует формату нашего товара, должен начинаться с префикса товаров b'));
            return;
        }

        if($this->isOurProductBarcodeIsTaken($box_barcode, $productBarcode, $ourProductBarcode)) {
			$this->addError($attribute, '<b>[' . $ourProductBarcode . ']</b> ' . Yii::t('inbound/errors', 'Этот ШК товара уже привязан к другому товару'));
            return;
        }
    }

    public function validateBindQrCode($attribute, $params)
    {
        // $bindQrCode = $this->bind_qr_code;

        // if ($this->isBindQrCodeIsTaken($bindQrCode)) {
        //    $this->addError($attribute, '<b>[' . $bindQrCode . ']</b> ' . Yii::t('inbound/errors', 'Этот QR код уже привязан к другому товару'));
        //    return;
        // }

        if (empty($this->box_barcode) || empty($this->product_barcode) || empty($this->our_product_barcode)) {
            $this->addError($attribute, 'Сначала отсканируйте коробку, товар и наш ШК товара.');
            return;
        }
    }

    /*
     * Check exist product in box
     * @param string $productBarcode
     * @return boolean
     * */
    private function checkProductInBox($productBarcode, $box_barcode)
    {
        return Stock::find()
            ->andWhere(
                [
                    'primary_address' => $box_barcode,
                    'product_barcode' => $productBarcode,
                    'status_availability' => Stock::STATUS_AVAILABILITY_YES,
                ]
            )
            ->exists();
    }

    /*
     * Проверяет существует ли отсканированный товар
     * @param string $productBarcode
     * @return
     * */
    private function checkProductBarcode($productBarcode)
    {
        return Stock::find()->andWhere(['product_barcode' => $productBarcode])->exists();
    }

    public static function getScannedProductInBox($boxBarcode)
    {
        return (int) Stock::find()
            ->andWhere(
                [
                    'primary_address' => $boxBarcode,
					'status_availability' => Stock::STATUS_AVAILABILITY_YES,
                ]
            )
            ->count();
    }

    /*
    * Is box
    * @param string $barcode
    * @return boolean
    * */
    public static function isOurProduct($barcode)
    {
        $barcode = trim($barcode);
        $prefix = substr($barcode, 0, 3);

        if (!empty($prefix)) {
            $result = Codebook::find()
                ->andWhere(['cod_prefix' => $prefix, 'base_type' => Codebook::BASE_TYPE_BOX])
                ->exists();

            if ($result || $prefix == 'eff' || $prefix == 'EFF') {
                return true;
            }
        }
        return false;
    }

    private function isOurProductBarcodeIsTaken($boxBarcode, $productBarcode, $ourProductBarcode)
    {
        $query = Stock::find()
            ->andWhere([
                'our_product_barcode' => $ourProductBarcode
            ]);

        return $query->exists();
    }

    private function isBindQrCodeIsTaken($bindQrCode)
    {
        $query = Stock::find()
            ->andWhere([
                'bind_qr_code' => $bindQrCode
            ]);

        return $query->exists();
    }

    /*
     * Get prefix barcode
     * @param string $barcode
     * @return string Prefix
     * */
    private function _getPrefixBarcode($barcode)
    {
        $barcode = trim($barcode);
        return !empty($barcode) ? substr($barcode, 0, 2) : '';
    }
}