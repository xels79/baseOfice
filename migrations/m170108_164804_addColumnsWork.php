<?php

use yii\db\Migration;

class m170108_164804_addColumnsWork extends Migration
{
    public $tblName='zakaz';
    public $colName='workType';
    public function up()
    {
        $this->addColumn($this->tblName, $this->colName, 'integer default 0');
        $this->addCommentOnColumn($this->tblName, $this->colName, 'Тип работы');
//        echo "В таблицу '$this->tblName'\n";
//        echo "Добавлена колонка '$this->colName'\n";
    }

    public function down()
    {
        $this->dropColumn($this->tblName, $this->colName);
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
