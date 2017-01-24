<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 23:59
 */

namespace mmapi\core;

use Doctrine\DBAL\Logging\SQLLogger;

class SqlLog implements SQLLogger
{
    const MAX_STRING_LENGTH = 32;
    const BINARY_DATA_VALUE = '(binary value)';

    static protected $num = 0;
    protected $start_time;
    protected $sql;

    /**
     * @desc   getCount 获取sql执行数量
     * @author chenmingming
     * @return int
     */
    static public function getCount()
    {
        return self::$num;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        self::$num++;
        $this->start_time = microtime(true);
        $sql              = "[" . self::$num . "] " . $sql;
        if (null !== $params) {
            $i   = 0;
            $sql = preg_replace_callback('/\?/', function () use ($params, &$i) {
                return $params[$i++];
            }, $sql);
        }
        $this->sql = $sql;
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        Log::sql(sprintf('%s [Exec: %.6f s]', $this->sql, microtime(true) - $this->start_time));
    }

    /**
     * @desc   normalizeParams
     * @author chenmingming
     *
     * @param array $params
     *
     * @return array
     */
    private function normalizeParams(array $params)
    {
        foreach ($params as $index => $param) {
            // normalize recursively
            if (is_array($param)) {
                $params[$index] = $this->normalizeParams($param);
                continue;
            }

            if (!is_string($params[$index])) {
                continue;
            }

            // non utf-8 strings break json encoding
            if (!preg_match('//u', $params[$index])) {
                $params[$index] = self::BINARY_DATA_VALUE;
                continue;
            }

            // detect if the too long string must be shorten
            if (self::MAX_STRING_LENGTH < mb_strlen($params[$index], 'UTF-8')) {
                $params[$index] = mb_substr($params[$index], 0, self::MAX_STRING_LENGTH - 6, 'UTF-8') . ' [...]';
                continue;
            }
        }

        return $params;
    }

}