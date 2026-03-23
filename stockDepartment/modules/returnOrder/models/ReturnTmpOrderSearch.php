<?php

namespace app\modules\returnOrder\models;
use common\modules\inbound\models\InboundOrder;
use common\modules\returnOrder\models\ReturnTmpOrders;
use common\modules\stock\models\Stock;
use stockDepartment\modules\returnOrder\entities\TmpOrder\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\returnOrder\models\ReturnOrder;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;

/**
 * InboundOrderSearch represents the model behind the search form about `common\modules\inbound\models\InboundOrder`.
 */
class ReturnTmpOrderSearch extends ReturnTmpOrders
{
    public $countWithoutSecondaryAddress;
    public $countWithSecondaryAddress;
    public $countSendByAPI;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ttn','our_box_to_stock_barcode','client_box_barcode','secondary_address'], 'string'],
            [['countWithoutSecondaryAddress','countWithSecondaryAddress','countSendByAPI'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ReturnTmpOrders::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ttn' => $this->ttn,
            'our_box_to_stock_barcode' => $this->our_box_to_stock_barcode,
            'client_box_barcode' => $this->client_box_barcode,
        ]);

        if(!empty($this->countWithoutSecondaryAddress)) {
            $query->andWhere(['status'=>Status::SCANNED]);
            $query->andWhere("secondary_address = ''");
        }

        if(!empty($this->countWithSecondaryAddress)) {
            $query->andWhere(['status'=>Status::SCANNED]);
            $query->andWhere("secondary_address != ''");
        }

        if(!empty($this->countSendByAPI)) {
            $query->andWhere(['status'=>Status::COMPLETE]);
        }

        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
//    public function attributeLabels()
//    {
//        return [
//            'ttn' => Yii::t('app', 'ТТН'),
//            'party_number' => Yii::t('app', 'Party number'),
//            'order_number' => Yii::t('app', 'Order number'),
//            'our_box_inbound_barcode' => Yii::t('app', 'Our box inbound barcode'),
//            'our_box_to_stock_barcode' => Yii::t('app', 'Наш короб'),
//            'client_box_barcode' => Yii::t('app', 'Короб клиента'),
//            'primary_address' => Yii::t('app', 'Адрес короба'),
//            'secondary_address' => Yii::t('app', 'Адрес полки'),
//        ];
//    }
}
