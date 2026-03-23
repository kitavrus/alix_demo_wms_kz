<?php

namespace common\modules\outbound\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\outbound\models\OutboundBoxLabels;

/**
 * TlDeliveryProposalBillingSearch represents the model behind the search form about `common\modules\billing\models\TlDeliveryProposalBilling`.
 */
class OutboundBoxLabelsSearch extends OutboundBoxLabels
{
    public $to_point_title;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','client_id', 'outbound_order_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['box_label_url', 'outbound_order_number'], 'string', 'max' => 255]
        ];
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
        $query = OutboundBoxLabels::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
//            'outbound_order_id' => $this->outbound_order_id,
//            'created_user_id' => $this->created_user_id,
//            'updated_user_id' => $this->updated_user_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'outbound_order_number', $this->outbound_order_number]);

        return $dataProvider;

    }
}
