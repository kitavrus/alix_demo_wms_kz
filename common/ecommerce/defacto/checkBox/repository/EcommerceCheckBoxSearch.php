<?php
namespace common\ecommerce\defacto\checkBox\repository;

use common\ecommerce\entities\EcommerceCheckBox;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

/**
 * CheckBoxSearch represents the model behind the search form of `common\ecommerce\entities\EcommerceCheckBox`.
 */
class EcommerceCheckBoxSearch extends EcommerceCheckBox
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inventory_id'], 'integer'],
            [['box_barcode'], 'string'],
            [['place_address'], 'string'],
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
        $query = EcommerceCheckBox::find();

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
            'inventory_id' => $this->inventory_id,
        ]);

        $query->andFilterWhere(['like', 'box_barcode', $this->box_barcode]);
        $query->andFilterWhere(['like', 'place_address', $this->place_address]);

        return $dataProvider;
    }
}
