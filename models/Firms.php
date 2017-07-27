<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "firms".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 *
 * @property OtherManager[] $otherManagers
 */
class Firms extends \yii\db\ActiveRecord
{
    private $firmTypes;
    public $searchtype='';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'firms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required','message'=>'Название должно быть заполнено.'],
            [['name'], 'string'],
            [['type'], 'integer'],
            [['productsTypes'],'string'],
            [['fone'], 'string', 'max' => 1024],
            [['addres1','addres2'],'string'],
            [['firmTypes'],'safe'],
            [['firmTypes'],'required','message'=>'Необходимо указать.']
        ];
    }
//    public function aL (){
//        $rVal=[];
//        foreach($this->attributes() as $attr){
//            $rVal[$attr]=$this->getTableSchema()->getColumn($attr)->comment;
//        }
//        return $rVal;
//    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        //return $this->aL();
        return [
            'id' => 'ID',
            'name' => 'Название фирмы',
            'searchtype' => 'Заказчик/Исполнитель/Поставщик',
            'typename' => 'Заказчик/Исполнитель',
            'addres1'=>'Юридический адрес',
            'addres2'=>'Адрес производства',
            'productsTypes'=>'Типы продукции',
            'firmTypes'=>'Род деятельности'
            ];
    }
    public function getFoneCount(){
        $tmp=\yii\helpers\Json::decode($this->fone);
        return count($tmp);
    }
    public function getMateralsNames(){
        $qr=MaterialsOpt::find()->select(['name','rem'])->asArray()->all();
        return array_merge([''=>'Невыбрано'],ArrayHelper::map($qr,'name','rem'));
    }
    public function getTypename(){
        $rVal='';
        foreach($this->getFirmTypes() as $el){
            if (strlen($rVal))$rVal.='/';
            switch ((integer)$el){
                case 0:
                    $rVal.='Заказчик';
                    break;
                case 1:
                    $rVal.='Исполнитель';
                    break;
                case 2:
                    $rVal.='Поставщик';
                    break;
                default:
                    $rVal.='Ошибка';
            }
        }
        return $rVal;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagers()
    {
        $aq=$this->hasMany(Manager::className(), ['firm_id' => 'id'])->select(['id','name','middle_name','surname','fone'])->asArray()->all();
        $rVal = [];
        foreach ( $aq as $el){
            $tmp='';
            $tmp.=$el['name'];
            if ($el['middle_name'])
                $tmp.=' '.$el['middle_name'];
            if ($el['surname']){
                $tmp.=' '.$el['surname'];
            }
            $rVal[$el['id']]=[
                'label'=>$tmp,
                'control'=>[
                    'changeButton'=>[
                        'url'=>yii\helpers\Url::to(['admin/manager/ajaxchange','id'=>$el['id']]),
                        ],
                    'removeButton'=>['url'=>yii\helpers\Url::to(['admin/manager/remove','id'=>$el['id']])]
                ],
                'title'=>\yii\widgets\DetailView::widget([
                        'model' => $el,
                        'attributes' => Manager::createDetailAttr($el),
                        'options'=>['class' => 'table table-striped table-condensed detail-view']
                    ]),
//                'linkOptions'=>[
//                    'data-toggle'=>'popover',
//                ]
            ];
        }
        return $rVal;
    }
    public function getFirmTypes(){
        return Json::decode($this['firmtype']);;
    }
    public function setFirmTypes($val){
        $this['firmtype']=Json::encode($val);
    }
}
