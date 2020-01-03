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
INSERT INTO `worker_place` 
(`id`, `name`, `status`, `ctime`)VALUES 
 (1, 'worker1', '1', '0') ,
 (2, 'worker2', '1', '0'), 
 (3, 'worker3', '1', '0'), 
 (4, 'worker4', '1', '0'), 
 (5, 'worker5', '1', '0'), 
 (6, 'worker6', '1', '0');
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
