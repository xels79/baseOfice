<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\widgets\JSRegister;
use yii\helpers\Url;
if ($model->isNewRecord) {
    echo yii\helpers\Html::tag('h3','Сначало нужно сохранить заказ!!!!');
    return;
}
?>
 <?php JSRegister::begin([
    'key' => 'zakazUploder',
    'position' => \yii\web\View::POS_READY
]); ?>
    <script>
        $('#drag-and-drop-zone').upLoader({
            url:"<?=Url::to(['upload'])?>",
            otherData:{id:<?=$model->isNewRecord?0:$model->id?>},
            fileManagerId:'#fileManager1_LV',
            loggerId:'#debug',
            fileVarName:'fileInputUpLoad',
            loaderId:'#files',
            onCompletePjaxReload:'#hddPjaxInfo',
            fileManagerPjaxId:'#fileZPjax1'
    
        });
    </script>
<?php JSRegister::end();?>
    <div class="container wrapper">
      <div class="page-header">
        <h1>Файлы<small> заказчика</small></h1>
      </div>
    
      <div class="row">
        <?php if (!$isDes):?>
        <div class="col-md-6">
          <!-- D&D Zone-->
          <div id="drag-and-drop-zone" class="uploader">
            <div>Перетаскивайте файлы сюда</div>
            <div class="or">-или-</div>
            <div class="browser">
              <label>
                <span>Откройте в проводнике</span>
                <input type="file" name="files[]"  multiple="multiple" title='Click to add Images'>
              </label>
            </div>
          </div>
          <!-- /D&D Zone -->
          <!-- / Загрузки -->
          <div class="panel panel-default loading">
            <div class="panel-heading">
              <h3 class="panel-title">Загрузка</h3>
            </div>
            <div class="panel-body panel-files" id='files'>
              <span class="note"></span>
            </div>
          </div>
          <!-- / Загрузки -->
           <!-- Debug box -->
          <div class="panel-group" id="accordion">
            <div class="panel panel-default debug">
              <div class="panel-heading">
                  <a class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Консоль отладки</a>
              </div>
              <div id="collapseOne" class="panel-collapse collapse">
                <div class="panel-body">
                  <ul id="debug" class="list-group">
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <!-- /Debug box -->
      </div>
          <?php  endif;?>
        <!-- / Left column -->
        <div class="col-xs-8 col-sm-8 col-md-6">
           <!-- Список файлов -->
          
          <?php \yii\widgets\Pjax::begin([
              'id'=>'fileZPjax1',
              'timeout'=>300000
          ])?>
          <?=app\widgets\FileList::widget([
              'id'=>'fileManager1',
              'removeAction'=>'remfile',
              'headerContent'=>'Список загруженных файлов',
              'options'=>['class'=>'file-loaded'],
              'zakazId'=>$model->id,
              'downloadAction'=>'download',
              'pageSize'=>12
          ])?>
          <?php \yii\widgets\Pjax::end()?>
          
          <!-- /Список файлов -->

        </div>
        <!-- / Right column -->
      </div>

      <div class="footer">
          <p>&copy; <a href="https://github.com/danielm/uploader" target="_blank">Daniel Morales 2014</a></p>
      </div>
    </div>