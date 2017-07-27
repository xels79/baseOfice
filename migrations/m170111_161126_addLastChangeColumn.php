<?php

use yii\db\Migration;

class m170111_161126_addLastChangeColumn extends Migration
{
    public $tblName='zakaz';
    public $colName='lastChange';

    public function up()
    {
        $this->addColumn($this->tblName, $this->colName, 'integer default 0');
        $this->addCommentOnColumn($this->tblName, $this->colName, 'Время последнего изменения');
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
