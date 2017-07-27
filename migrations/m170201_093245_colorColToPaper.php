<?php

use yii\db\Migration;

class m170201_093245_colorColToPaper extends Migration
{
    public $tblName='paper';
    public $colName='pcolors';
    public function up()
    {
        $this->addColumn($this->tblName, $this->colName, $this->integer()->notNull());
        $this->addCommentOnColumn($this->tblName, $this->colName, 'Цвет');
        $this->createTable($this->colName, [
            'id'=>$this->primaryKey(),
            'name'=>$this->text()->notNull()->comment('Название'),
            'toproduct'=>$this->text()->notNull()->comment('К материалу')
        ]);
        $this->addCommentOnTable($this->colName, 'Цвет для бумаги');
    }

    public function down()
    {
        $this->dropColumn($this->tblName, $this->colName);
        $this->dropTable($this->colName);
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
