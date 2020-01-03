<?php

use yii\db\Migration;

/**
 * Class m200103_140858_fea_12_31
 */
class m200103_140858_fea_12_31 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
alter table `user` 
  add column `idcard` varchar(255) not null default '' comment'身份证';

delete from `worker_place`;

INSERT INTO `worker_place` 
(`id`, `name`, `status`, `ctime`)VALUES 
 (1, '四川省气象局', '1', '0') ,
 (2, '甘肃省气象局', '1', '0');
SQL;

        $this->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200103_140858_fea_12_31 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200103_140858_fea_12_31 cannot be reverted.\n";

        return false;
    }
    */
}
