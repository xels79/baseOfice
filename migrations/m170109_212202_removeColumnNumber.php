<?php

use yii\db\Migration;

class m170109_212202_removeColumnNumber extends Migration
{
    public $tblName='zakaz';
    public $colName='number';

    public function up()
    {
        $this->dropColumn($this->tblName, $this->colName);
    }

    public function down()
    {
        $this->addColumn($this->tblName, $this->colName, 'integer default 0');
        $this->addCommentOnColumn($this->tblName, $this->colName, 'Номер заказа');
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
