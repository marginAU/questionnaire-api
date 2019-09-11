<?php

use yii\db\Migration;

/**
 * Class m190908_142809_init
 */
class m190908_142809_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sex` varchar(255)   NOT NULL DEFAULT 'A' COMMENT '性别，A=男。B=女',
  `username` varchar(255)   NOT NULL DEFAULT '' COMMENT '姓名',
  `idcard` varchar(255)   NOT NULL DEFAULT '' COMMENT '身份证',
  `position` varchar(255)   NOT NULL DEFAULT '' COMMENT '应聘岗位',
  `age` tinyint(3) NOT NULL DEFAULT '0' COMMENT '年龄',
  `nation` varchar(255)   NOT NULL DEFAULT '' COMMENT '名族',
  `political_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '政治面貌，1=群众，2=团圆，3=党员',
  `marital_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '婚姻状况，A 已婚  B 未婚  C离异  D丧偶',
  `education` varchar(255)   NOT NULL DEFAULT '' COMMENT '学历，A=初中及以下，B=高中/中专，C=高职/大专，D=大学本科，E=硕士（包括MBA,EMBA,MPA等），F=博士',
  `status` tinyint(2) NOT NULL DEFAULT '2' COMMENT '是否提交完成，1=是，2=否',
  `ctime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `work_time` tinyint(2) NOT NULL DEFAULT '0' COMMENT '从事气象行业工作时间',
  `work_place` varchar(512)   NOT NULL DEFAULT '' COMMENT '工作地点',
  `children_or_not` varchar(255)   NOT NULL DEFAULT 'B' COMMENT '是否有子女A=是，2=否',
  `children_sex` varchar(255)   NOT NULL DEFAULT 'A' COMMENT '子女性别A=男。B=女',
  `children_age` tinyint(2) NOT NULL DEFAULT '0' COMMENT '子女年龄',
  `parent_work_status` tinyint(2) NOT NULL DEFAULT '2' COMMENT '父母或近亲中是否有人在气象行业工作',
  `social_scale` tinyint(2) NOT NULL DEFAULT '10' COMMENT '社会等级',
  `source_place` varchar(512)   NOT NULL DEFAULT '' COMMENT '生源地',
  `mobile` varchar(512)   NOT NULL DEFAULT '' COMMENT '电话',
  PRIMARY KEY (`id`),
  KEY `idcard` (`idcard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='提交答案用户表';

CREATE TABLE `answer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `total_points` int(11) NOT NULL DEFAULT '0' COMMENT '总分',
  `frustration_points` int(11) NOT NULL DEFAULT '0' COMMENT '抗挫能力分数',
  `responsible_points` int(11) NOT NULL DEFAULT '0' COMMENT '责任心分数',
  `debugging_points` int(11) NOT NULL DEFAULT '0' COMMENT '心理调试能力分数',
  `assistance_points` int(11) NOT NULL DEFAULT '0' COMMENT '团队协助能力分数',
  `self_efficacy_points` int(11) NOT NULL DEFAULT '0' COMMENT '自我效能感分数',
  `subscale_points` int(11) NOT NULL DEFAULT '0' COMMENT '分量表分数',
  `ctime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='用户答案';

CREATE TABLE `answer_source` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `answer_list` text   COMMENT '原始答案',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `ctime` int(11) NOT NULL DEFAULT '0' COMMENT '提交时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='答案原数据表';

CREATE TABLE `admin_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255)   NOT NULL DEFAULT '' COMMENT '用户账户',
  `password` varchar(255)   NOT NULL DEFAULT '' COMMENT '密码',
  `status` tinyint(2) NOT NULL COMMENT '状态，1=有效，2=无效',
  `ctime` int(11) NOT NULL default 0 COMMENT '创建时间',
  `token` varchar(512)   NOT NULL DEFAULT '' COMMENT 'token',
  `salt` varchar(64)   NOT NULL DEFAULT '' COMMENT '盐',
  `expires` int (11)  NOT NULL DEFAULT 0 COMMENT 'token有效时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='后台用户';

insert into `admin_user` (username,password,status,salt) values ('oujun','7c47ba4845476d9e2104508d898116ae',1,'asdf');
SQL;

        $this->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190908_142809_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190908_142809_init cannot be reverted.\n";

        return false;
    }
    */
}
