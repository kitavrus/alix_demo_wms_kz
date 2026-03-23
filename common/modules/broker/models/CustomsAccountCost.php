<?php

namespace common\modules\broker\models;

use app\modules\custom\Custom;
use Yii;
use common\models\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\modules\broker\models\CustomsDocument;
use common\helpers\iHelper;

/**
 * This is the model class for table "customs_account_costs".
 *
 * @property integer $id
 * @property integer $cost_type
 * @property integer $customs_accounts_id
 * @property string $title
 * @property string $price_cost_our
 * @property string $price_nds_cost_our
 * @property string $price_cost_client
 * @property string $price_nds_cost_client
 * @property integer $payment_status
 * @property integer $who_pay
 * @property string $comments
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 */
class CustomsAccountCost extends ActiveRecord
{
    //Who pays values
    const WHO_PAY_THEY = 0;
    const WHO_PAY_WE = 1;

    const COST_TYPE_ACCOUNTABLE = 1;
    const COST_TYPE_NOT_ACCOUNTABLE = 2;

    public $files;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customs_account_costs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_cost_our', 'price_nds_cost_our', 'price_cost_client', 'price_nds_cost_client'], 'number'],
            [['payment_status', 'cost_type', 'who_pay', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted', 'customs_accounts_id'], 'integer'],
            [['comments'], 'string'],
            [['title'], 'string', 'max' => 128],
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
            'title' => Yii::t('customs/forms', 'Title'),
            'cost_type' => Yii::t('customs/forms', 'Cost type'),
            'price_cost_our' => Yii::t('customs/forms', 'Cost for us'),
            'price_nds_cost_our' => Yii::t('customs/forms', 'Cost for us VAT'),
            'price_cost_client' => Yii::t('customs/forms', 'Cost for client'),
            'price_nds_cost_client' => Yii::t('customs/forms', 'Cost for client VAT'),
            'payment_status' => Yii::t('customs/forms', 'Payment status'),
            'who_pay' => Yii::t('customs/forms', 'Who pay'),
            'comments' => Yii::t('customs/forms', 'Comment'),
            'created_user_id' => Yii::t('forms', 'Created User ID'),
            'updated_user_id' => Yii::t('forms', 'Updated User ID'),
            'created_at' => Yii::t('forms', 'Created At'),
            'updated_at' => Yii::t('forms', 'Updated At'),
            'deleted' => Yii::t('forms', 'Deleted'),
            'files' => Yii::t('customs/forms', 'Attached files'),
        ];
    }

    /**
     * Array with cost types
     * @return array $data
     **/
    public static function getCostTypeArray()
    {
        $data = [
            self::COST_TYPE_ACCOUNTABLE => Yii::t('customs/titles', 'Accountable'),
            self::COST_TYPE_NOT_ACCOUNTABLE => Yii::t('customs/titles', 'Not Accountable'),
        ];

        return $data;
    }

    /* Cost type value
    * @return string
    **/
    public function getCostTypeValue($cost_type=null)
    {
        if(is_null($cost_type)){
            $cost_type = $this->cost_type;
        }

        return ArrayHelper::getValue(self::getCostTypeArray(), $cost_type);
    }

    /**
     * @return array Массив со статусами оплаты.
     */
    public static function getPaymentStatusArray()
    {
        $data = [
            //self::INVOICE_UNDEFINED => Yii::t('forms', 'Undefined'), //Не определен
            self::INVOICE_NOT_SET => Yii::t('forms', 'Not set'), //Не выставлен
            self::INVOICE_SET => Yii::t('forms', 'Set'), //Выставлен
            self::INVOICE_PAID => Yii::t('forms', 'Paid'), //Оплачен
        ];
        return $data;
    }

    /**
     * @return array Значение со статусом оплаты.
     */
    public function getPaymentStatusValue($status_invoice=null)
    {
        if(is_null($status_invoice)){
            $status_invoice = $this->payment_status;
        }
        return ArrayHelper::getValue(self::getPaymentStatusArray(), $status_invoice);
    }

    /**
     * @return array With who pays.
     */
    public static function getWhoPaysArray()
    {
        return [
            self::WHO_PAY_THEY => Yii::t('customs/forms', 'Client'),
            self::WHO_PAY_WE => Yii::t('customs/forms', 'Nomadex'),
        ];
    }

    /**
     * @return string Display value
     */
    public function getWhoPayValue()
    {
        return ArrayHelper::getValue($this->getWhoPaysArray(),$this->who_pay);
    }

    /**
     * @return string Display value
     */
    public function saveFiles()
    {
        $this->files = UploadedFile::getInstances($this, 'files');
        $dirPath = 'uploads/attached-files/customs-account-cost/'.$this->customs_accounts_id.'/' . date('Ymd'). '/' . date('H-i');

        if($this->files){
            BaseFileHelper::createDirectory($dirPath);

            foreach ($this->files as $file) {
                $fileToPath = $dirPath . '/' . iHelper::transliterate($file->name);
                $file->saveAs($fileToPath);

                if(file_exists($fileToPath)){
                    $customsDocument = new CustomsDocument();
                    $customsDocument->customs_account_cost_id = $this->id;
                    $customsDocument->filename = $fileToPath;
                    $customsDocument->save(false);
                }
            }

        }

    }

    /*
     * Relation has many with CustomsDocuments
     **/
    public function getDocuments()
    {
        return $this->hasMany(CustomsDocument::className(), ['customs_account_cost_id' => 'id']);
    }
}
