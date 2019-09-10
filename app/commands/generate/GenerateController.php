<?php

namespace app\commands\generate;

/**
 * Generate
 *
 * @uses     yangjin
 * @version  2018/10/10
 * @author   yangjin <imyangjin@vip.qq.com>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class GenerateController extends Command
{
    /**
     * 生成logic/model/data
     *
     * php app/yii generate/mvc "tableName" "className" "group"
     *
     * @param string $table     表名
     * @param string $className 类名
     * @param string $group     分组-组名
     */
    public function actionMvc(string $table, string $className, string $group = '')
    {
        $className = $this->convertUnderline($className, true);
        $this->generateModel($table, $className, $group);
        $this->generateLogic($table, $className, $group);
        $this->generateData($table, $className, $group);
    }

    /**
     * 生成controller
     *
     * php app/yii generate/controller "tableName" "className" "group"
     *
     * @param string $table     表名
     * @param string $className 类名
     * @param string $group     分组-组名
     */
    public function actionController(string $table, string $className, string $group = '')
    {
        $className = $this->convertUnderline($className, true);
        $this->generateController($table, $className, $group);
    }

    /**
     * 生成logic
     *
     * php app/yii generate/logic "tableName" "className" "group"
     *
     * @param string $table     表名
     * @param string $className 类名
     * @param string $group     分组-组名
     */
    public function actionLogic(string $table, string $className, string $group = '')
    {
        $className = $this->convertUnderline($className, true);
        $this->generateLogic($table, $className, $group);
    }

    /**
     * 生成data
     *
     * php app/yii generate/data "tableName" "className" "group"
     *
     * @param string $table     表名
     * @param string $className 类名
     * @param string $group     分组-组名
     */
    public function actionData(string $table, string $className, string $group = '')
    {
        $className = $this->convertUnderline($className, true);
        $this->generateData($table, $className, $group);
    }

    /**
     * 生成Model
     *
     * php app/yii generate/model "tableName" "className" "group"
     *
     * @param string $table     表名
     * @param string $className 类名
     * @param string $group     分组-组名
     */
    public function actionModel(string $table, string $className, string $group = '')
    {
        $className = $this->convertUnderline($className, true);
        $this->generateModel($table, $className, $group);
    }

    /**
     * 生成markdown文档
     *
     * php app/yii generate/doc "tableName"
     *
     * @param string $table 表名，支持模糊表名，例如：admin%
     */
    public function actionDoc(string $table)
    {
        $db     = \Yii::$app->getDb();
        $tables = $db->createCommand("SHOW TABLES LIKE '" . $table . "'")->queryAll();

        if (empty($tables)) {
            echo 'Table Not Found!' . PHP_EOL;

            return;
        }

        foreach ($tables as $item) {
            $table = current($item);
            $this->generateDoc($table);
        }
    }

    /**
     * 执行生成文档
     *
     * @param $table
     */
    private function generateDoc($table)
    {
        $tableSchema = \Yii::$app->db->getTableSchema($table);

        $doc = "|字段       |类型         |是否为空|默认值    |       说明|" . PHP_EOL;
        $doc .= '|:----------|:------------|:-------|:---------|:---------|' . PHP_EOL;
        foreach ($tableSchema->columns as $column) {
            $isNull  = $column->allowNull ? 'true' : 'false';
            $default = $column->defaultValue == '' ? '-' : $column->defaultValue;
            $comment = $column->comment == '' ? '-' : $column->comment;
            $doc     .= "| $column->name \t| $column->dbType \t| $isNull | $default | $comment |" . PHP_EOL;
        }
        echo $doc . PHP_EOL;
    }


    /**
     * 执行生成controller
     *
     * @param $table
     * @param $className
     * @param $group
     */
    private function generateController($table, $className, $group)
    {
        $table       = $this->convertUnderline($table);
        $group_space = '';
        if ($group) {
            $group_space = '\\' . $group;
            $group       = '/' . $group;
        }
        $dataContent = $this->defaultController($table, $className, date('Y-m-d'), $group_space);
        $file_path   = APP_PATH . 'controllers' . $group . '/' . $className . 'Controller.php';
        $dir         = APP_PATH . 'controllers/' . $group;
        $ret         = $this->createFile($dataContent, $file_path, $dir);

        echo 'controllers' . $group . '/' . $className . 'Controller.php' . ' success!' . PHP_EOL;

    }

    /**
     * 执行生成logic
     *
     * @param $table
     * @param $className
     * @param $group
     */
    private function generateLogic($table, $className, $group)
    {
        $table       = $this->convertUnderline($table);
        $group_space = '';
        if ($group) {
            $group_space = '\\' . $group;
            $group       = '/' . $group;
        }

        $dataContent = $this->defaultLogic($table, $className, date('Y-m-d'), $group_space);
        $file_path   = APP_PATH . 'models' . $group . '/logic/' . $className . 'Logic.php';
        $dir         = APP_PATH . 'models/' . $group . '/logic';
        $ret         = $this->createFile($dataContent, $file_path, $dir);

        echo 'models' . $group . '/logic/' . $className . 'Logic.php' . ' success!' . PHP_EOL;
    }

    /**
     * 执行生成Data
     *
     * @param $table
     * @param $className
     * @param $group
     */
    private function generateData($table, $className, $group)
    {
        $table       = $this->convertUnderline($table);
        $group_space = '';
        if ($group) {
            $group_space = '\\' . $group;
            $group       = '/' . $group;
        }

        $dataContent = $this->defaultData($table, $className, date('Y-m-d'), $group_space);
        $file_path   = APP_PATH . 'models' . $group . '/data/' . $className . 'Data.php';
        $dir         = APP_PATH . 'models/' . $group . '/data';
        $ret         = $this->createFile($dataContent, $file_path, $dir);

        echo 'models' . $group . '/data/' . $className . 'Data.php' . ' success!' . PHP_EOL;
    }


    /**
     * 执行生成model
     *
     * @param $table
     * @param $className
     */
    private function generateModel($table, $className, $group)
    {
        $tableSchema = \Yii::$app->db->getTableSchema($table);

        $label       = $maps = [];
        $group_space = '';
        if ($group) {
            $group_space = '\\' . $group;
            $group       = '/' . $group;
        }

        foreach ($tableSchema->columns as $column) {
            $camel = $this->convertUnderline($column->name);
            if ($camel != $column->name) {
                $maps[] = "            '{$column->name}' => '{$camel}',";
            }

            $label[] = " * @property {$column->phpType}  \${$camel} $column->comment";
        }
        $dataContent = $this->defaultModel($table, $className, implode(PHP_EOL, $label), implode(PHP_EOL, $maps),
            date('Y-m-d'), $group_space);

        $file_path = APP_PATH . 'models' . $group . '/dao/' . $className . '.php';
        $dir       = APP_PATH . 'models/' . $group . '/dao';
        $ret       = $this->createFile($dataContent, $file_path, $dir);
        echo 'models' . $group . '/dao/' . $className . '.php' . ' success!' . PHP_EOL;
    }


    /**
     * model模板
     *
     * @param $table
     * @param $className
     * @param $label
     * @param $maps
     * @param $date
     *
     * @return string
     */
    private function defaultModel($table, $className, $label, $maps, $date, $group = '')
    {
        $modelPage = <<<EOE
<?php

namespace app\models{$group}\dao;

use hxh\models\Model;

/**
{$label}
 *
 * {$className}
 *
 * @uses     {$className}
 * @version  {$date}
 * @author   Generate
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class {$className} extends Model
{
    public static function tableName()
    {
        return '{$table}';
    }
    
    public function maps(): array
    {
        return [
{$maps}
        ];
    }
}

EOE;

        return $modelPage;
    }


    /**
     * logic模板
     *
     * @param $table
     * @param $className
     * @param $label
     * @param $maps
     * @param $date
     *
     * @return string
     */
    private function defaultLogic($table, $className, $date, $group = '')
    {
        $modelPage = <<<EOE
<?php

namespace app\models{$group}\logic;

use app\models{$group}\data\\{$className}Data;
use hxh\models\Logic;

/**
 *
 * {$table}Logic
 *
 * @uses     {$className}Logic
 * @version  {$date}
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class {$className}Logic extends Logic
{

    private \${$table}Data;

    public function __construct()
    {
       \$this->{$table}Data = new {$className}Data();
    }
    
}

EOE;

        return $modelPage;
    }


    /**
     * data模板
     *
     * @param $table
     * @param $className
     * @param $label
     * @param $maps
     * @param $date
     *
     * @return string
     */
    private function defaultData($table, $className, $date, $group = '')
    {
        $modelPage = <<<EOE
<?php

namespace app\models{$group}\data;

use hxh\models\Data;

/**
 *
 * {$table}Data
 *
 * @uses     {$className}Data
 * @version  {$date}
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class {$className}Data extends Data
{
    
}

EOE;

        return $modelPage;
    }

    /**
     * controller模板
     *
     * @param $table
     * @param $className
     * @param $label
     * @param $maps
     * @param $date
     *
     * @return string
     */
    private function defaultController($table, $className, $date)
    {
        $modelPage = <<<EOE
<?php

namespace app\controllers\inner;

use app\models\logic\\{$className}Logic;
use hxh\InnerController;

/**
 *
 * {$table}Controller
 *
 * @uses     {$className}Controller
 * @version  {$date}
 * @author   wm<wm@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link http://www.php.net/license/3_0.txt}
 */
class {$className}Controller extends InnerController
{
    public function {$table}Logic()
    {
        return new {$className}Logic();
    }
}

EOE;

        return $modelPage;
    }

    /**
     * 下划线转驼峰
     *
     * @param      $str
     * @param bool $ucfirst
     *
     * @return null|string|string[]
     */
    private function convertUnderline($str, $ucfirst = false)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);

        return $ucfirst ? ucfirst($str) : $str;
    }

    /**
     * 创建文件
     *
     * @param $dataContent  文件名内容
     * @param $file_path    文件名
     * @param $dir          分组名
     */
    private function createFile($dataContent, $file_path, $dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $fp1 = fopen($file_path, "w+");
        fwrite($fp1, $dataContent);
        fclose($fp1);

        return true;
    }
}
