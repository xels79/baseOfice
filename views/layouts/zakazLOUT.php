<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\widgets\AList;
$flag=$this->context->action->id==='add'||$this->context->action->id==='change';
if ($flag)
    $this->context->contSz=1600;
elseif($this->context->action->id==='deslist')
    $this->context->contSz=1200;
else    
    $this->context->contSz=1300;
?>
<div class="zakaz"<?=$this->context->contSz?(' style="width:'.$this->context->contSz.'px;"'):''?>>
<?=$flag?yii\helpers\Html::tag('div',$content,[
        'class'=>'col-lg-10 col-md-10 col-sm-10 col-xs-10'
    ]):$content?>
</div>
