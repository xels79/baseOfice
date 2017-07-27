<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
$shippingOplata=$model->shippingOplata;
$shippingOtgruzka=$model->shippingOtgruzka;
$summ=true;
$pcs=true;
$totalCost=$model->totalCost;
$totalPcs=eval("return $model->numberOfCopies;");
Yii::trace($totalPcs,'z-otgruzki::$totalPcs');
?>
<div class="row">
    <div class="col-lg-4">
        <table class="table table-bordered">
            <tbody>
                <tr><td colspan="3"><h2>Оплата</h2></td></tr>
                <tr>
                    <td></td>
                    <th>Дата:</th>
                    <th>Сумма:</th>
                </tr>
                <?php for ($num=0;$num<(count($shippingOplata)+1)&&$summ&&($totalCost===null||$totalCost>0);$num++):?>
                <?php $element=isset($shippingOplata[$num])?$shippingOplata[$num]:[];?>
                <tr>
                    <td></td>
                    <td>
                        <?= \yii\jui\DatePicker::widget([
                            'language' => 'ru',
                            'id'=>'opl'.$num,
                            'dateFormat' => 'dd.MM.yyyy',
                            'value'=>isset($element['date'])?$element['date']:'',
                            'options'=>[
                                'class'=>'form-control',
                                'min-date'=>\yii::$app->formatter->asDate($model->dateOfAdmission,'dd.MM.yyyy')
                                ],
                            'clientOptions'=>  yii\helpers\ArrayHelper::merge($model::_defDatePickerOption,[
                                'minDate'=>\yii::$app->formatter->asDate($model->dateOfAdmission,'dd.MM.yyyy'),
                                'maxDate'=>0
                            ]),
                            'name'=>'Zakaz[shipping][oplata]['.$num.'][date]'
                             ])
                        ?>
                    </td>
                    <td>
                        <?php $summ=isset($element['summ'])?$element['summ']:null;
                        $totalCost=$summ?($totalCost-$summ):$totalCost?>
                        <?=Html::textInput('Zakaz[shipping][oplata]['.$num.'][summ]',$summ,[
                            'class'=>'form-control',
                            'role'=>'oplata',
                            'readonly'=>$summ!=null
                            ])?>
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-4">
        <table class="table table-bordered">
            <tbody>
                <tr><td colspan="3"><h2>Отгрузка</h2></td></tr>
                <tr>
                    <th>Дата:</th>
                    <th>Количество:</th>
                    <th>Фамилия</th>
                </tr>
                <?php for ($num=0;$num<(count($shippingOtgruzka)+1)&&$pcs&&($totalPcs===null||$totalPcs>0);$num++):?>
                <?php $element=isset($shippingOtgruzka[$num])?$shippingOtgruzka[$num]:[];?>
                <tr>
                    <td>
                        <?= \yii\jui\DatePicker::widget([
                            'language' => 'ru',
                            'id'=>'otgr'.$num,
                            'dateFormat' => 'dd.MM.yyyy',
                            'options'=>['class'=>'form-control'],
                            'name'=>'Zakaz[shipping][otgruzka]['.$num.'][date]',
                            'value'=>isset($element['date'])?$element['date']:'',
                            'clientOptions'=>  yii\helpers\ArrayHelper::merge($model::_defDatePickerOption,[
                                'minDate'=>\yii::$app->formatter->asDate($model->dateOfAdmission,'dd.MM.yyyy'),
                                'maxDate'=>0
                            ]),
                             ])
                        ?>
                    </td>
                    <td>
                        <?php $pcs=isset($element['pcs'])?$element['pcs']:null;
                        $totalPcs=$pcs?($totalPcs-$pcs):$totalPcs?>

                        <?=Html::textInput('Zakaz[shipping][otgruzka]['.$num.'][pcs]',$pcs,[
                                'class'=>'form-control',
                                'role'=>'otgruzka',
                                'readonly'=>$pcs!=null,
                            ])?>
                    </td>
                    <td><?=Html::textInput('Zakaz[shipping][otgruzka]['.$num.'][name]',isset($element['name'])?$element['name']:null,['class'=>'form-control'])?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>