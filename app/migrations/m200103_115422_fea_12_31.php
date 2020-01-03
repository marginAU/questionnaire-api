<?php

use yii\db\Migration;

/**
 * Class m200103_115422_fea_12_31
 */
class m200103_115422_fea_12_31 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE `rand_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT '' COMMENT '登录码',
  `status` tinyint(2) DEFAULT '1' COMMENT '是否有效，1=是，2=否',
  `expire` int(11) DEFAULT '0' COMMENT '有效截止时间戳',
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='登录随机码表';

CREATE TABLE `worker_place` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT '' COMMENT '单位名称',
  `status` tinyint(2) DEFAULT '1' COMMENT '是否有效，1=是。2=否',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='单位';

ALTER TABLE `user` 
  ADD `worker_place_id` INT(11)  NULL  DEFAULT '0'  COMMENT 'worker_place_id';
  
ALTER TABLE `admin_user` 
  ADD `worker_place_id` INT(11)  NULL  DEFAULT '0'  COMMENT 'worker_place_id';
SQL;

        $this->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200103_115422_fea_12_31 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200103_115422_fea_12_31 cannot be reverted.\n";

        return false;
    }
    */
}
