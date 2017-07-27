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
class ZakazSearchDes extends ZakazSearch{
    public $isProizv=false;
    public function search($params)
    {
    //$query = Zakaz::find();
    $query=ZakazMaterialOrder::find();
    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);
    //$dataProvider=  parent::search($params);
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
            ]
        ]
    ]);
    if ($this->searchAdmission=='') $this->searchAdmission=null;
    if (\yii::$app->user->identity->role!=='proizvodstvo'&&!$this->isProizv)
        $query->andFilterWhere(['stage'=>[0,1,2]]);
    else{
        $query->andFilterWhere(['stage'=>2]);
//        $query->andWhere('is_material_ordered!=null OR all_material_recived!=0');
    }
    if (!($this->load($params) && $this->validate())) {
        return $dataProvider;
    }
    if ($this->searchAdmission) $query->andFilterWhere(['like','dateOfAdmission',\yii::$app->formatter->asDate($this->searchAdmission,'php:Y-m-d')]);

    $query->andFilterWhere(['orderType'=>$this->searchOrderType]);
    $query->andFilterWhere([
        'id' => $this->id,
        'managerId' => $this->searchManagerId,
    ]);
    $query->joinWith(['manager' => function ($q) {
        $q->andFilterWhere(['manager.firm_id'=>$this->searchFirm]);
    }]);
    return $dataProvider;
    }

    
}
