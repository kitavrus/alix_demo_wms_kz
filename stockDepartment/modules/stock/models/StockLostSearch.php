<?php

namespace app\modules\stock\models;

use common\components\BarcodeManager;
use common\modules\inbound\models\InboundOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\stock\models\Stock;
use yii\db\Query;
use yii\helpers\VarDumper;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ArrayDataProvider;

/**
 * StockSearch represents the model behind the search form about `common\modules\stock\models\Stock`.
 */
class StockLostSearch extends Stock
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_barcode','primary_address'], 'required'],
            ['primary_address', 'isBox'],
        ];
    }

    public function isBox($attribute, $params) {
        $box = $this->$attribute;

        if (!BarcodeManager::isBox($box)){
           $this->addError($attribute, 'Неправильный ШК короба');
        }
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Stock::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 1001,
            ],
            'sort' => [
                // Set the default sort by name ASC and created_at DESC.
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'product_barcode', $this->product_barcode]);

        return $dataProvider;
    }
}