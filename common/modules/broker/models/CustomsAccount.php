<?php

namespace common\modules\broker\models;

use Yii;
use common\models\ActiveRecord;
use common\modules\broker\models\CustomsAccountCost;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\helpers\iHelper;

/**
 * This is the model class for table "customs_accounts".
 *
 * @property integer $id
 * @property integer $currency
 * @property string $kg_netto
 * @property string $kg_brutto
 * @property string $invoice_number
 * @property integer $qty_place
 * @property integer $qty_tnv_codes
 * @property string $price
 * @property string $price_nds
 * @property string $price_expenses_total
 * @property string $price_expenses_cache
 * @property string $price_expenses_nds
 * @property string $price_profit
 * @property string $comments
 * @property integer $status
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CustomsAccount extends ActiveRecord
{
    public $files;

    /*
     * @var integer status
     **/
    const STATUS_ACCOUNT_UNDEFINED = 0; //не указан
    const STATUS_ACCOUNT_NEW = 1; //новый
    const STATUS_ACCOUNT_SET = 2; //выставлен
    const STATUS_ACCOUNT_PAID = 3; //оплачен

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customs_accounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['qty_place', 'qty_tnv_codes', 'currency', 'status', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['price', 'price_nds', 'price_expenses_total', 'price_expenses_cache', 'price_expenses_nds', 'price_profit', 'kg_netto', 'kg_brutto', ], 'number'],
            [['comments'], 'string'],
            [['invoice_number'], 'string', 'max' => 128],
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
            'currency' => Yii::t('forms', 'Currency'),
            'kg_netto' => Yii::t('customs/forms', 'Net weight'),
            'kg_brutto' => Yii::t('customs/forms', 'Gross weight'),
            'invoice_number' => Yii::t('customs/forms', 'Invoice'),
            'qty_place' => Yii::t('customs/forms', 'Places qty'),
            'qty_tnv_codes' => Yii::t('customs/forms', 'ТНВ коды, общее кол-во'),
            'price' => Yii::t('customs/forms', 'Cost'),
            'price_nds' => Yii::t('customs/forms', 'Cost VAT'),
            'price_expenses_total' => Yii::t('customs/forms', 'Expenses cost total'),
            'price_expenses_cache' => Yii::t('customs/forms', 'Expenses cost cache'),
            'price_expenses_nds' => Yii::t('customs/forms', 'Expenses cost VAT'),
            'price_profit' => Yii::t('customs/forms', 'Revenue'),
            'comments' => Yii::t('customs/forms', 'Comment'),
            'status' => Yii::t('forms', 'Status'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
            'files' => Yii::t('customs/forms', 'Attached files'),
        ];
    }

    /* Array with currencies
     *@return array $data
     **/
    public static function getCurrencyArray()
    {
        $data = [
            self::CURRENCY_EUR => Yii::t('titles', 'EUR'),
            self::CURRENCY_USD => Yii::t('titles', 'USD'),
        ];

        return $data;
    }

    /* Currency value
    * @return string
    **/
    public function getCurrencyValue($currency=null)
    {
        if(is_null($currency)){
            $currency = $this->currency;
        }

        return ArrayHelper::getValue(self::getCurrencyArray(), $currency);
    }

    /*
     * Relation has many with CustomsAccountCost
     **/
    public function getCosts()
    {
        return $this->hasMany(CustomsAccountCost::className(), ['customs_accounts_id' => 'id']);
    }

    /* Array with statuses
     *@return array $data
     **/
    public static function getStatusArray()
    {
        $data = [
            self::STATUS_ACCOUNT_NEW => Yii::t('customs/titles', 'New'),
            self::STATUS_ACCOUNT_SET => Yii::t('customs/titles', 'Set'),
            self::STATUS_ACCOUNT_PAID => Yii::t('customs/titles', 'Paid'),
        ];

        return $data;
    }

    /* Status value
     * @return string
     **/
    public function getStatusValue($status=null)
    {
        if(is_null($status)){
            $status = $this->status;
        }

        return ArrayHelper::getValue(self::getStatusArray(), $status);
    }

    /*
     * Relation has many with CustomsDocuments
     **/
    public function getDocuments()
    {
        return $this->hasMany(CustomsDocument::className(), ['customs_account_id' => 'id']);
    }

    /**
     * @return string Display value
     */
    public function saveFiles()
    {
        $this->files = UploadedFile::getInstances($this, 'files');
        $dirPath = 'uploads/attached-files/customs-account/'.$this->id.'/' . date('Ymd'). '/' . date('H-i');

        if($this->files){
            BaseFileHelper::createDirectory($dirPath);

            foreach ($this->files as $file) {
               // $fileToPath = $dirPath . '/' . iHelper::transliterate($file->baseName) . '.' . $file->extension;
                $fileToPath = $dirPath . '/' . iHelper::transliterate($file->name);
                $file->saveAs($fileToPath);

                if(file_exists($fileToPath)){
                    $customsDocument = new CustomsDocument();
                    $customsDocument->customs_account_id = $this->id;
                    $customsDocument->filename = $fileToPath;
                    $customsDocument->save(false);
                }
            }

        }

    }
}
