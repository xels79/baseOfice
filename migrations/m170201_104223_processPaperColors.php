<?php

//use yii;
use yii\db\Migration;
use yii\helpers\Console;

class m170201_104223_processPaperColors extends Migration
{
    private function proceedEl($num){
        $el= app\models\MaterialsNew::crateObject('paper')->findOne($num);
        $ppName=app\models\PaperName::findOne($el->paperName);
        $spl=explode(' ', $ppName->name);
        $name=array_shift($spl);
        $color='';
        if ($el->pcolors) return;
        while(count($spl)){
            $tmp=array_shift($spl);
            if ($tmp && $tmp!=' '){
                if ($color!='') $color.=' ';
                $color.=$tmp;
            }
        }
        if (!$color || $color=='' || $color==' ') $color='нет';
//        echo "name: '$name' color: '$color'\n";
        if ($mColor=app\models\Pcolors::find()->where(['name' => $color])->one()){
            $el->pcolors=$mColor->id;
            if (mb_stripos($mColor->toproduct,$name)===false){
                if (mb_strlen($mColor->toproduct))
                    $mColor->toproduct.=';';
                $mColor->toproduct.=$name;
                $mColor->save();
            }
        }else{
            $mColor=New app\models\Pcolors();
            $mColor->name=$color;
            $mColor->toproduct=$name;
            $mColor->save();
            $el->pcolors=$mColor->id;
        }
        $ppName->name=$name;
        $ppName->save();        
        if ($tmp=app\models\MaterialsNew::crateObject('paper')
                ->find()
                ->where(['not',['id'=>(int)$num]])
                ->andWhere([
                    'paperName'=>(int)$ppName->id,
                    'pcolors'=>0
                ])->all())
            foreach ($tmp as $processEl){
                $processEl->paperName=$ppName->id;
                $processEl->pcolors =$mColor->id;
                $processEl->save();
            }
        $el->paperName=$ppName->id;
        $el->save();
    }
    public function processLoopt($cnt,&$remCnt){
        $all= app\models\PaperName::find()->all();
        $cnt=count($all);
        $i=0;
        $all= app\models\PaperName::find()->all();
        foreach ($all as $paper){
            foreach($tmp=app\models\PaperName::find()
                    ->where(['name'=>$paper->name])
                    ->andWhere(['not',['id'=>(int)$paper->id]])
                    ->all() as $paperDouble){
                foreach($mTmp=\app\models\MaterialsNew::crateObject('paper')
                        ->find()
                        ->where(['paperName'=>(int)$paperDouble->id])
                        ->all() as $material){
                   $material->paperName=$paper->id;
                   $material->save();
                }
                $paperDouble->delete();
                $remCnt++;
                return true;
            }
            $i++;
            Console::updateProgress($i, $cnt,'Чистим paperName:');
        }        
        return false;
    }
    public function checkEmptyPaperName(){
        $all= app\models\PaperName::find()->all();
        $cnt=count($all);
        $remCnt=0;
        Console::startProgress(0, $cnt);
        Console::updateProgress(0, $cnt,'Чистим paperName шаг1:');
        while ($this->processLoopt($cnt,$remCnt));
        echo "Шаг1: удалили $remCnt повторяющихся названий\n";
        $all= app\models\PaperName::find()->all();
        $cnt=count($all);
        $i=1;
        $remCnt=0;
        Console::startProgress($i, $cnt);
        Console::updateProgress($i, $cnt,'Чистим paperName шаг2:');
        foreach ($all as $paper){
            Console::updateProgress($i++, $cnt,'Чистим paperName шаг2:');
            if (!\app\models\MaterialsNew::crateObject('paper')
                        ->find()
                        ->where(['paperName'=>(int)$paper->id])
                        ->one()){
                $paper->delete();
                $remCnt++;
            }
        }
        Console::endProgress();
        echo "Шаг2: удалили $remCnt неиспользуемых названий\n";
    }
    public function checkNoUsedMaterial(){
        $all= \app\models\MaterialsNew::crateObject('paper')->find()->all();
        $remCnt=0;
        $i=1;
        $cnt=count($all);
        //"materialsIdList":
        Console::startProgress($i, $cnt);
        Console::updateProgress($i, $cnt,'Чистим paper:');
        foreach ($all as $material){
            if (!\app\models\Zakaz::find()
                    ->andFilterWhere(['like','materialDetails','{"0":"paper"'])
                    ->andFilterWhere(['like','materialDetails','"materialsIdList":'.$material->id])
                    ->all()){
                $material->delete();
                $remCnt++;
            }
            Console::updateProgress($i++, $cnt,'Чистим paper:');
        }
        Console::endProgress();
        echo "На третьем этапе удалили $remCnt неиспользуемых материалов\n";
        if ($remCnt){
            echo "Повторим названия\n";
            $this->checkEmptyPaperName();
        }
    }
    public function remove_(){
        $cnt=app\models\PaperName::find()->count();
        Console::startProgress(0, $cnt);
        $i=0;
        $updCnt=0;
        Console::updateProgress($i, $cnt,'Заменяем символ "_" в названиях');
        foreach (app\models\PaperName::find()->all() as $paper){
            $str='';
            if ($tmp=explode('_',$paper->name)){
                foreach ($tmp as $part){
                    if ($str=='')
                        $str.=$part;
                    else
                        $str.=' '.$part;
                }
                $paper->name=$str;
                $paper->save();
                if (count($tmp)>1) $updCnt++;
            }
            Console::updateProgress(++$i, $cnt,'Заменяем символ "_" в названиях');
        }
        Console::endProgress();
        echo "Обработано $updCnt записей\n";

        $cnt=app\models\Pcolors::find()->count();
        Console::startProgress(0, $cnt);
        $i=0;
        $updCnt=0;
        Console::updateProgress($i, $cnt,'Заменяем символ "_" в сносках на цвет');
        foreach (app\models\Pcolors::find()->all() as $color){
            $endstr='';
            foreach (explode(';',$color->toproduct) as $snoska){
                $str='';
                if ($tmp=explode('_',$snoska)){
                    foreach ($tmp as $part){
                        if ($str=='')
                            $str.=$part;
                        else
                            $str.=' '.$part;
                    }
                    if (count($tmp)>1) $updCnt++;
                }
                if ($endstr=='')
                    $endstr=$str;
                else
                    $endstr.=';'.$str;
            }
            $color->toproduct=$endstr;
            
            $color->save();
            Console::updateProgress(++$i, $cnt,'Заменяем символ "_" в сносках на цвет');
        }
        Console::endProgress();
        echo "Обработано $updCnt записей\n";
    }
    public function renameTach(){
        $all= \app\models\PaperName::find()->all();
        $cnt=count($all);
        $i=0;
        $renCount=0;
        Console::startProgress(0, $cnt);
        foreach ($all as $el){
            $spl= explode(' ', $el->name);
            $nVal='';
            foreach ($spl as $ln){
                if ($ln=='тач'){
                    $ln='тачкавер';
                    $renCount++;
                }elseif($ln=='кавер'){
                    $ln='';
                    $renCount++;
                }
                if ($ln){
                    if ($nVal) $nVal.=' ';
                    $nVal.=$ln;
                }
            }
            $el->name=$nVal;
            $el->save();
            Console::updateProgress(++$i, $cnt,'Заменяем "тач" на "тачкавер"');
        }
        Console::endProgress();
        echo "Обработано $renCount крокозябров.\n";
    }
    public function up()
    {
        echo "Сначала крокозябры:\n";
        $this->renameTach();
        $all= \app\models\Zakaz::find()->andFilterWhere(['like','materialDetails','{"0":"paper"'])->all();
        $cntT=count($all);
        echo "Будет обработано ".$cntT." заказов\n";
        Console::startProgress(0, $cntT);
        $i=1;
        foreach ($all as $z_m){
            Console::updateProgress($i++, $cntT, 'Общий прогресс ');
            Console::moveCursorNextLine();
            $mater=$z_m->prepareToSaveMaterialDetails();//mDetails;
            if (isset($mater['value'])){
                if (isset($mater[0])){
                    if ($mater[0]==='paper'&&isset($mater['value'])){
                        $pCnt=count($mater['value']);
                        $st='Заказ №'.$z_m->id;
                        Console::startProgress(0, $pCnt, 'Заказ №'.$z_m->id);
                        for ($i2=0;$i2<$pCnt;$i2++){
                            $this->proceedEl($mater['value'][$i2]['materialsIdList']);
                            $z_m->materialDetails=$mater;
                            $z_m->materialDetails=  \yii\helpers\Json::encode($mater);
                            $attrToSave=['materialDetails'];
                            $z_m->runBeforSave=false;
                            $z_m->saveUpdateTime=false;
                            //$z_m->save(true,$attrToSave);
                            Console::updateProgress($i2+1, $pCnt);
                        }
                        Console::endProgress();
                    }
                }
            }
            Console::moveCursorPrevLine();
            Console::moveCursorPrevLine();
        }
        Console::endProgress();
        Console::moveCursorNextLine();
        echo "Обработка завершена.\nЧистка:\n";
        $this->checkEmptyPaperName();
        $this->checkNoUsedMaterial();
        $this->remove_();
        return true;
    }

    public function down()
    {
        echo "m170201_104223_processPaperColors cannot be reverted.\n";

        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
