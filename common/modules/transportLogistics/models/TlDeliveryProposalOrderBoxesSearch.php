<?php

namespace common\modules\transportLogistics\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\transportLogistics\models\TlDeliveryProposalOrderBoxes;

/**
 * TlDeliveryProposalOrderBoxesSearch represents the model behind the search form of `common\modules\transportLogistics\models\TlDeliveryProposalOrderBoxes`.
 */
class TlDeliveryProposalOrderBoxesSearch extends TlDeliveryProposalOrderBoxes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tl_delivery_proposal_id', 'created_user_id', 'updated_user_id', 'created_at', 'updated_at'], 'integer'],
            [['box_barcode'], 'safe'],
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
        $query = TlDeliveryProposalOrderBoxes::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'tl_delivery_proposal_id' => $this->tl_delivery_proposal_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'box_barcode', $this->box_barcode]);

        return $dataProvider;
    }
}
