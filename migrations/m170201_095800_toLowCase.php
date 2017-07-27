<?php

use yii\db\Migration;
//use app\models\MaterialsNew;

class m170201_095800_toLowCase extends Migration
{
    public function up()
    {
        $q= app\models\Description::find()->all();
        foreach ($q as $model){
            $model->name= mb_strtolower($model->name);
            $model->save();
        }
        $q= app\models\PaperName::find()->all();
        foreach ($q as $model){
            $model->name= mb_strtolower($model->name);
            $model->save();
        }
        
    }

    public function down()
    {
        echo "m170201_095800_toLowCase cannot be reverted.\n";

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
