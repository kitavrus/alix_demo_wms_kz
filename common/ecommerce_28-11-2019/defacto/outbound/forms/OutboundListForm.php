<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.09.14
 * Time: 10:54
 */

namespace common\ecommerce\defacto\outbound\forms;

//use common\ecommerce\defacto\employee\repository\EmployeeRepository;
//use  common\ecommerce\defacto\outbound\validation\ValidationOutbound;
//use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\defacto\outbound\service\OutboundListService;
use yii\base\Model;
use Yii;

class OutboundListForm extends Model
{
    public $title;
    public $barcode;

    const SCENARIO_ADD = 'ADD';
    const SCENARIO_PRINT = 'PRINT';

    private $validation;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->validation = new OutboundListService();
    }

    /*
     * */
    public function rules()
    {
        return [
            [['title', 'barcode'], 'trim', 'on' => self::SCENARIO_ADD],
            [['title', 'barcode'], 'string', 'on' => self::SCENARIO_ADD],
            [['title', 'barcode'], 'required', 'on' => self::SCENARIO_ADD],
            ['barcode', 'Barcode', 'on' => self::SCENARIO_ADD],

            [['title'], 'required', 'on' => self::SCENARIO_PRINT],
        ];
    }

    /*
    * Validate barcode employee
    * */
    public function Barcode($attribute, $params)
    {
        $title = $this->title;
        $barcode = $this->barcode;

        if ($this->validation->isListNotPrinted($title)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот лист отгрузки уже распечатан'));
            return;
        }

        if ($this->validation->isPackageBarcodeExistInOtherList($title,$barcode)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот заказ уже отсканирован в другой отгрузочный лист'));
            return;
        }

        if ($this->validation->isExistPackageBarcode($title,$barcode)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот шк уже отсканирован'));
            return;
        }

        if (!$this->validation->isOrderPackaged($barcode)) {
            $this->addError($attribute, '<b>[' . $barcode . ']</b> ' . Yii::t('outbound/errors', 'Этот заказ еще не собран'));
            return;
        }
    }

    /*
    *
    * */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('outbound/forms', 'Название листа отгрузки'),
            'barcode' => Yii::t('outbound/forms', 'ШК места'),
        ];
    }

    public function getDTO()
    {
        $dto = new \stdClass();
        $dto->title = $this->title;
        $dto->barcode = $this->barcode;
        return $dto;
    }
}