<?php

namespace app\modules\returnOrder\models;
use common\modules\inbound\models\InboundOrder;
use common\modules\returnOrder\models\ReturnTmpOrders;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use stockDepartment\modules\returnOrder\entities\TmpOrder\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\returnOrder\models\ReturnOrder;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * InboundOrderSearch represents the model behind the search form about `common\modules\inbound\models\InboundOrder`.
 */
class ReturnTmpOrderTTNSearch extends ReturnTmpOrders
{
    public $countWithoutSecondaryAddress;
    public $countWithSecondaryAddress;
    public $countSendByAPI;
//    public $qty;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ttn','our_box_to_stock_barcode','client_box_barcode','secondary_address'], 'string'],
            [['countWithoutSecondaryAddress','countWithSecondaryAddress','countSendByAPI','qty'], 'integer'],
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
        $query->select("count(*) as qty, id, ttn, our_box_to_stock_barcode, client_box_barcode");
        $query->groupBy("ttn");

        $diffTTNs = (new Query())->select("rtm.ttn, count(rtm.id) as rtmQty, dp.number_places")
            ->from(ReturnTmpOrders::tableName() . ' as rtm')
            ->leftJoin(TlDeliveryProposal::tableName() . ' as dp', 'rtm.ttn = dp.id')
            ->groupBy('rtm.ttn')
            ->having('rtmQty != dp.number_places')
            ->column();

        if (!empty($diffTTNs) && is_array($diffTTNs)) {
            $query->addOrderBy(new \yii\db\Expression("FIELD (ttn," . implode(',', $diffTTNs) . ") DESC"));
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ttn' => $this->ttn,
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
