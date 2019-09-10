<?php

namespace app\models\dao;

use yii\db\ActiveQuery;

/**
 * LocalActiveQuery Extended JSON query support
 *
 * @uses
 * @version  2018/10/18
 * @author   yangjin <imyangjin@vip.qq.com>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class LocalActiveQuery extends ActiveQuery
{
    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the 'AND' operator.
     * @param array $condition  the new WHERE condition. Please refer to [[where()]]
     *                          on how to specify this parameter.
     *                          example like :
     *                          1. ['>', 'content->"$.en.content"' , 1]
     *                          2. ['content->"$.en.content"' => 1]
     * @return $this the query object itself
     * @see where()
     * @see orWhere()
     */
    public function jsonWhere($condition, $params = [])
    {
        $condition = \Yii::$app->db->queryBuilder->buildCondition($condition, $params);
        $condition = str_replace('`', '', $condition);

        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }

        $this->addParams($params);
        return $this;
    }

    /**
     * Support JSON_CONTAINS(target, candidate[, path]) in query
     * This query is equivalent to the query, but the difference is that the query is an inclusion relation, that is, the field contains the value of the value
     *
     * @param string     $column A multilevel field supporting JSON fields is segmented using '.'
     *                           example content.lang.en
     * @param string|int $value
     * @param bool       $isMulti The JSON field of the query a multidimensional array
     *
     * @return $this
     * @see https://dev.mysql.com/doc/refman/5.7/en/json-search-functions.html
     */
    public function jsonContainsWhere($column, $value, $isMulti = false)
    {
        $prefix  = $isMulti ? '$[*]' : '$';
        $columns = explode('.', $column);
        $cond    = array_shift($columns);

        array_unshift($columns, $prefix);
        $jsonCond  = implode('.', $columns);
        $condition = "JSON_CONTAINS($cond->'$jsonCond', '$value')";

        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }

        return $this;
    }

    /**
     * Support JSON_EXTRACT(json_doc, path[, path] ...) in query
     *
     * @param string     $column  A multilevel field supporting JSON fields is segmented using '.'
     *                            example content.lang.en
     * @param string|int $value
     * @param string     $operate Query Operators，default '='
     *                            example support : >|>=|<|<=
     * @return $this
     * @see https://dev.mysql.com/doc/refman/5.7/en/json-search-functions.html#function_json-extract
     */
    public function jsonExtractWhere($column, $value, $operate = '=')
    {
        list($cond, $jsonCond) = $this->splitJsonColumn($column, true);

        $condition = "JSON_EXTRACT($cond, '$jsonCond') $operate $value";

        if ($this->where === null) {
            $this->where = $condition;
        } else {
            $this->where = ['and', $this->where, $condition];
        }

        return $this;
    }

    public function splitJsonColumn($column, $jsonColumn = false)
    {
        $columns = explode('.', $column);
        $cond    = array_shift($columns);

        if (count($columns) == 0) {
            if ($jsonColumn == true) {
                throw new InvalidArgumentException(get_class($this) . ' has no json column "' . $column . '".');
            }
            $jsonColumn == '';
        } else {
            array_unshift($columns, '$');
            $jsonCond = implode('.', $columns);
        }

        return [$cond, $jsonCond];
    }
}