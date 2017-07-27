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
    'key' => 'managerUploder',
    'position' => \yii\web\View::POS_READY
]); ?>
    <script>
        $('#drag-and-drop-zone2').upLoader({
            url:"<?=Url::to(['uploaddesigner'])?>",
            otherData:{id:<?=$model->isNewRecord?0:$model->id?>},
            fileManagerId:'#fileManager2_LV',
            loggerId:'#debug2',
            fileVarName:'fileInputUpLoad',
            loaderId:'#files2',
            onCompletePjaxReload:'#hddPjaxInfo',
            fileManagerPjaxId:'#fileZPjax2'
    
        });
    </script>
<?php JSRegister::end();?>
    <div class="container wrapper">
      <div class="page-header">
        <h1>Файлы<small> дизайнера</small></h1>
      </div>
    
      <div class="row">
        <div class="col-md-6">
          <!-- D&D Zone-->
          <div id="drag-and-drop-zone2" class="uploader">
            <div>Перетаскивайте файлы сюда</div>
            <div class="or">-или-</div>
            <div class="browser">
              <label>
                <span>Откройте в проводнике</span>
                <input type="file" name="files[]"  accept="" multiple="multiple" title='Click to add Images'>
              </label>
            </div>
          </div>
          <!-- /D&D Zone -->
          <!-- / Загрузки -->
          <div class="panel panel-default loading">
            <div class="panel-heading">
              <h3 class="panel-title">Загрузка</h3>
            </div>
            <div class="panel-body panel-files" id='files2'>
              <span class="note"></span>
            </div>
          </div>
          <!-- / Загрузки -->
           <!-- Debug box -->
          <div class="panel-group" id="accordion2">
            <div class="panel panel-default debug">
              <div class="panel-heading">
                  <a class="panel-title" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne2">Консоль отладки</a>
              </div>
              <div id="collapseOne2" class="panel-collapse collapse">
                <div class="panel-body">
                  <ul id="debug2" class="list-group">
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <!-- /Debug box -->
      </div>
        <!-- / Left column -->

        <div class="col-xs-8 col-sm-8 col-md-6">
           <!-- Список файлов -->
          
          <?php \yii\widgets\Pjax::begin([
              'id'=>'fileZPjax2',
              'timeout'=>300000
          ])?>
          <?=app\widgets\FileList::widget([
              'id'=>'fileManager2',
              'removeAction'=>'designerremfile',
              'headerContent'=>'Список загруженных файлов',
              'options'=>['class'=>'file-loaded'],
              'zakazId'=>$model->id,
              'downloadAction'=>'download',
              'pageSize'=>12,
              'isInputFiles'=>false
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