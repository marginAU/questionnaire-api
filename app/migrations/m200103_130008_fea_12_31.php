<?php

use yii\db\Migration;

/**
 * Class m200103_130008_fea_12_31
 */
class m200103_130008_fea_12_31 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL

SQL;

        $this->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200103_130008_fea_12_31 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200103_130008_fea_12_31 cannot be reverted.\n";

        return false;
    }
    */
}
