<?php

use yii\db\Migration;

/**
 * Class m181009_114950_demo
 */
class m181009_114950_demo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<sql
sql;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181009_114950_demo cannot be reverted.\n";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181009_114950_demo cannot be reverted.\n";

        return false;
    }
    */
}
