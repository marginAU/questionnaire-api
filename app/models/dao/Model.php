<?php

namespace app\models\dao;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * 基类数据模型
 *
 * @uses     Model
 * @version  2018年06月01日
 * @author
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class Model extends ActiveRecord
{
    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $name = $this->getFieldName($name);
        parent::__set($name, $value);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $name = $this->getFieldName($name);

        return parent::__get($name);
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $maps   = $this->maps();
        $fields = array_combine($this->attributes(), $this->attributes());

        $fields = ArrayHelper::merge($fields, $maps);

        return array_flip($fields);
    }

    /**
     * @return array
     */
    public function maps(): array
    {
        return [];
    }


    /**
     * @param array $values
     * @param bool  $safeOnly
     */
    public function setAttributes($values, $safeOnly = false)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                $name = $this->getFieldName($name);
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getFieldName(string $name): string
    {
        $fields = $this->fields();
        if (isset($fields[$name])) {
            $name = $fields[$name];
        }

        return $name;
    }

    /**
     * 加载数据到当前类
     *
     * @param array $data
     *
     * @return $this
     */
    public function loadData($data = [])
    {
        if (!empty($data)) {
            foreach ($data as $key => $item) {
                $this->{$key} = $item;
            }
        }

        return $this;
    }

    /**
     * 批量更新不同值的数据
     *
     * @param string $tableName 表名
     * @param array  $data      要修改的参数（必须包含主键id）
     *
     * @example
     * $data = [
     *    [
     *        'id'=> 12,
     *        'xx' => 1,
     *    ],
     *    [
     *        'id'=> 12,
     *        'xx' => 1,
     *    ]
     * ];
     *
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public function updateBatchAlone(string $tableName, array $data = []): int
    {
        if ($tableName && empty($data)) {
            return false;
        }

        $ids = array_column($data, 'id');
        if (empty($ids)) {
            return false;
        }

        $keys = array_keys($data[0]);

        $sql = "UPDATE `$tableName` SET ";

        foreach ($keys as $key) {
            $sql    .= " `$key` = CASE id ";
            $values = array_column($data, $key);
            foreach ($values as $k => $value) {
                $sql .= " WHEN $ids[$k] THEN '" . $value . "' ";
            }
            $sql .= " END,";
        }
        $sql = substr($sql, 0, -1);

        $ids = implode(',', $ids);

        $sql .= " WHERE `id` IN ($ids)";


        $result = \Yii::$app->db->createCommand($sql)->execute();

        return (int)$result;

    }

    /**
     * 批量插入
     *
     * For example,
     *
     * ```php
     * $this->addBatch('user', ['name', 'age'], [
     *     ['Tom', 30],
     *     ['Jane', 20],
     *     ['Linda', 25],
     * ]);
     * ```
     *
     * @param string $table   表名
     * @param array  $columns 属性字段集合
     * @param array  $rows    操作数据
     *
     * @return int 成功返回影响行数
     * @throws \yii\db\Exception
     */
    public function addBatch(string $table, array $columns, array $rows): int
    {
        $result = \Yii::$app->db->createCommand()->batchInsert($table, $columns, $rows)->execute();

        return (int)$result;
    }

    /**
     * 批量插入
     *
     * @param array $columns
     * @param array $rows
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function batchInsert(array $columns, array $rows): bool
    {
        $result = \Yii::$app->db->createCommand()->batchInsert(static::tableName(), $columns, $rows)->execute();

        if ($result === false) {
            log_exception('批量插入操作失败，cols=' . json_unescaped_encode($columns) . ' rows=' . json_unescaped_encode($rows));
        }

        return true;
    }

    /**
     * 支持json字段查询的扩展方法
     *
     * @example
     *      1.新增 jsonWhere 的子方法 ：
     *          $this->jsonWhere([Query::where()])
     *
     * @return LocalActiveQuery|object
     * @throws \yii\base\InvalidConfigException
     */
    public static function findJson()
    {
        return \Yii::createObject(LocalActiveQuery::class, [get_called_class()]);
    }

    /**
     * 批量更新,主键只能在数组第一个
     *
     * @param array  $columns
     * @param array  $rows
     * @param string $primary
     *
     * static::batchUpdateByIds(['id', 'name', 'age'], [
     *     [1, 'Tom', 30],
     *     [2, 'Jane', 20],
     *     [3, 'Linda', 25],
     * ]);
     * ```
     *
     * @throws \yii\db\Exception
     */
    public static function batchUpdateByIds(array $columns, array $rows, string $primary = 'id'): bool
    {
        $tableName = static::tableName();

        $setStr = ' SET ';
        foreach ($columns as $index => $column) {
            if ($column == $primary) {
                continue;
            }

            $setStr .= " `$column` = CASE `$primary` ";
            foreach ($rows as $row) {
                $setStr .= " WHEN '$row[0]' THEN '$row[$index]' ";
            }
            $setStr .= ' END,';
        }

        $setStr = substr($setStr, 0, -1);
        $ids    = array_column($rows, 0);
        $idsStr = implode(',', $ids);
        $sql    = "UPDATE $tableName $setStr WHERE id IN ($idsStr)";
        $result = \Yii::$app->db->createCommand($sql)->execute();

        if ($result === false) {
            log_exception('batchUpdateByIds批量更新失败，col=' . json_unescaped_encode($columns) . ' rows=' . json_unescaped_encode($rows));
        }

        return (bool)$result;
    }

    /**
     * 验证数据操作返回结果
     *
     * @param mixed $result
     *
     * @return bool
     * @throws \Exception
     */
    public static function getResult($result, string $message): bool
    {
        if ($result === false) {
            log_exception($message);
        }

        return true;
    }
}