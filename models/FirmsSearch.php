<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Firms;

/**
 * FirmsSearch represents the model behind the search form about `app\models\Firms`.
 */
class FirmsSearch extends Firms
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type','searchtype'], 'integer'],
            [['name'], 'safe'],
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
        $query = Firms::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        //\yii\helpers\VarDumper::dumpAsString($query->firmtype,10,true);
        //\yii::$app->end();
        $query->andFilterWhere(['id' => $this->id,]);
        $query->andFilterWhere(['like','firmtype' , $this->searchtype]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
