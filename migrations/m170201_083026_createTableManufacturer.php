<?php

use yii\db\Migration;

class m170201_083026_createTableManufacturer extends Migration
{
    public function up()
    {
        $this->createTable('manufacturer', [
            'id'=>$this->primaryKey()->comment('Индекс'),
            'name'=>$this->text()->notNull()->comment('Название'),
            'toproduct'=>$this->text()->notNull()->comment('К материалу')
        ]);
        $this->addCommentOnTable('manufacturer', 'Производители');
    }

    public function down()
    {
        $this->dropTable('manufacturer');
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
