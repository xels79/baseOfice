<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Description of ZakazSearch
 *
 * @author Александр
 */
class ZakazSearch extends Zakaz{
    public $searchManagerId=null;
    public $searchStageId=null;
    public $searchFirm=null;
    public $searchOrderType=null;
    public $searchPayment=null;
    public $searchAdmission=null;
    public $searchDeadline=null;
    public $searchisMOrdered=null;

    public function scenarios()
    {
        return Model::scenarios();
    }
    public function rules(){
        return [
            [
                [
                    'searchManagerId',
                    'searchStageId',
                    'searchFirm',
                    'searchOrderType',
                    'searchPayment',
                    'searchisMOrdered'
                ],
                'safe'
            ],
            [['searchAdmission','searchDeadline'],'safe'],
            [['managerId'],'safe']
        ];
    }
    public function search($params)
    {
        $query = Zakaz::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query, 
        ]);

    $dataProvider->setSort([
        'attributes' => [
            'id',
            'name',
            'searchAdmission'=>[
                'asc'=>[
                    'dateOfAdmission' => SORT_ASC,
                ],
                'desc'=>[
                    'dateOfAdmission' => SORT_DESC,
                ]                
            ],
            'searchDeadline'=>[
                'asc'=>[
                    'deadline' => SORT_ASC,
                ],
                'desc'=>[
                    'deadline' => SORT_DESC,
                ]                
            ]
        ]
    ]);
    if (!($this->load($params) && $this->validate())) {
        return $dataProvider;
    }
    if ($this->searchAdmission=='') $this->searchAdmission=null;
    if ($this->searchDeadline=='') $this->searchDeadline=null;
    $query->andFilterWhere([
        'id' => $this->id,
        'managerId' => $this->searchManagerId,
    ]);
    $query->andFilterWhere(['stage'=>$this->searchStageId]);
    $query->andFilterWhere(['orderType'=>$this->searchOrderType]);
    $query->andFilterWhere(['payment'=>$this->searchPayment]);
    if ($this->searchisMOrdered==='on'){
        $query->andWhere(['is_material_ordered'=>null]);
    }
    if ($this->searchAdmission) $query->andFilterWhere(['like','dateOfAdmission',\yii::$app->formatter->asDate($this->searchAdmission,'php:Y-m-d')]);
    if ($this->searchDeadline) $query->andFilterWhere(['like','deadline',\yii::$app->formatter->asDate($this->searchDeadline,'php:Y-m-d')]);
    $query->joinWith(['manager' => function ($q) {
        $q->andFilterWhere(['manager.firm_id'=>$this->searchFirm]);
    }]);
    return $dataProvider;
    }
}
