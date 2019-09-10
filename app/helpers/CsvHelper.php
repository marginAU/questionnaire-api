<?php

namespace app\helpers;

/**
 * @uses     CsvHelper
 * @version  2019年09月08日
 * @author   oujun <oujun@kzl.com.cn>
 * @license  PHP Version 7.1.x {@link [图片]http://www.php.net/license/3_0.txt}
 */
class CsvHelper
{

    /**
     * 导出 CSV 文件
     *
     * @param array[]|\Generator $rows    [
     *                                      [
     *                                          field => value
     *                                          ...
     *                                      ],
     *                                      ...
     *                                    ]
     * @param array              $headers [
     *                                      field => title
     *                                    ]
     * @param array              $options [
     *                                      savePath => '/path/to' 保存路径
     *                                      fileName => 'xxx.csv' 文件名称
     *                                      convType => true  转换字符类型
     *                                    ]
     *
     * @return string
     * @throws \yii\base\ExitException
     */
    public static function exportCsv($rows, array $headers = [], array $options = []): string
    {
        $string   = '';
        $convType = isset($options['convType']) ? (bool)$options['convType'] : false;

        if ($headers) {
            $csvHeaders = [];
            foreach ($headers as $title) {
                $csvHeaders[] = $convType ? \iconv('UTF-8', 'GB2312//IGNORE', $title) : $title;
            }

            $string = \implode(',', $csvHeaders) . "\n";
        }

        $fields = \array_keys($headers);

        foreach ($rows as $row) {
            $csvRow = [];
            foreach ($fields as $field) {
                if (isset($row[$field])) {
                    $cellValue = (string)$row[$field];
                    $csvRow[] = $convType ? \iconv('UTF-8', 'GB2312//IGNORE', $cellValue) : $cellValue;
                }
            }

            $string .= implode(',', $csvRow) . "\n";
        }

        $fileName = !empty($options['fileName']) ? $options['fileName'] : \date('YmdHis') . '.csv';

        // save to file
        if (!empty($options['savePath'])) {
            $filepath = \rtrim($options['savePath'], '/') . '/' . $fileName;

            // save to file
            \file_put_contents($filepath, $string);

            return $filepath;
        }

        // return data
        if (isset($options['returnData']) && $options['returnData']) {
            return $string;
        }

        // response to client
        self::sendFile($fileName, $string, 'text/csv');
        return '';
    }

    /**
     * @param string $fileName
     * @param string $data
     * @param string $mimeType like 'text/csv'
     *
     * @throws \yii\base\ExitException
     */
    public static function sendFile(string $fileName, string $data, string $mimeType): void
    {
        $resp = \Yii::$app->getResponse();
        // with data
        $resp->data = $data;
        $resp->setDownloadHeaders($fileName, $mimeType);
        // $resp->send();
        \Yii::$app->end();
    }

}