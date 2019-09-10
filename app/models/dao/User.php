<?php

namespace app\models\dao;

/**
 * @property integer $id
 * @property string  $sex              性别，A=男。B=女
 * @property string  $username         姓名
 * @property string  $idcard           身份证
 * @property string  $position         应聘岗位
 * @property integer $age              年龄
 * @property string  $nation           名族
 * @property integer $politicalStatus  政治面貌，1=群众，2=团圆，3=党员
 * @property integer $maritalStatus    婚姻状况，A 已婚  B 未婚  C离异  D丧偶
 * @property string  $education        学历，A=初中及以下，B=高中/中专，C=高职/大专，D=大学本科，E=硕士（包括MBA,EMBA,MPA等），F=博士
 * @property integer $status           是否提交完成，1=是，2=否
 * @property integer $ctime            创建时间
 * @property integer $workTime         从事气象行业工作时间
 * @property string  $workPlace        工作地点
 * @property string  $childrenOrNot    是否有子女A=是，2=否
 * @property string  $childrenSex      子女性别A=男。B=女
 * @property integer $childrenAge      子女年龄
 * @property integer $parentWorkStatus 父母或近亲中是否有人在气象行业工作
 * @property integer $socialScale      社会等级
 * @property string  $sourcePlace      生源地
 * @property string  $mobile           电话
 *
 * User
 *
 * @uses     User
 * @version  2019-09-09
 * @author   Generate
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class User extends Model
{
    public static function tableName()
    {
        return 'user';
    }

    public function maps(): array
    {
        return [
            'political_status'   => 'politicalStatus',
            'marital_status'     => 'maritalStatus',
            'work_time'          => 'workTime',
            'work_place'         => 'workPlace',
            'children_or_not'    => 'childrenOrNot',
            'children_sex'       => 'childrenSex',
            'children_age'       => 'childrenAge',
            'parent_work_status' => 'parentWorkStatus',
            'social_scale'       => 'socialScale',
            'source_place'       => 'sourcePlace',
        ];
    }
}
