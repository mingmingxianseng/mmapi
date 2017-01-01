<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/1
 * Time: 20:57
 */

namespace mmapi\core;

use model\footballModel;

class QueryBuilder
{
    const TYPE_SELECT = 'SELECT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_INSERT = 'INSERT';
    const TYPE_DELETE = 'DELETE';

    const QUERY_WHERE = 'where';
    const QUERY_DATA = 'data';

    const LOGIC_AND = 'AND';
    const LOGIC_OR = 'OR';

    private $options = [
        'tablePrefix' => '',//表名前缀
    ];

    private $query_type;//sql类型
    private $current_field;//当前字段
    private $current_logic;//and or
    private $current_type;//where 还是 data
    private $current_value;//值
    private $current_exp;//表达式

    private $table_name;//表名
    private $data;
    private $field;

    /**
     * field:'id'
     * exp:'='
     * value:'2'
     *
     * @var
     */
    private $where;
    private $order;
    private $limit;
    private $params = [];
    private $sql;

    public function __construct($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @desc   create
     * @author chenmingming
     *
     * @param $options
     *
     * @return QueryBuilder
     */
    static public function create($options)
    {
        return new self($options);
    }

    /**
     * @desc   select
     * @author chenmingming
     *
     * @param string $field 要查询的表达式
     *
     * @return $this
     */
    public function select($field = '*')
    {
        $this->query_type = self::TYPE_SELECT;
        $this->field      = $field;

        return $this;
    }

    /**
     * @desc   from 要查询的表
     * @author chenmingming
     *
     * @param string $tableName 表名称
     * @param string $alias     别名
     *
     * @return $this
     */
    public function from($tableName, $alias = '')
    {
        $this->setTableName($tableName, $alias);

        return $this;
    }

    /**
     * @desc   update 更新语句
     * @author chenmingming
     *
     * @param string $tableName 表名称
     *
     * @return $this
     */
    public function update($tableName)
    {
        $this->query_type = self::TYPE_UPDATE;
        $this->setTableName($tableName);

        return $this;
    }

    /**
     * @desc   delete 删除
     * @author chenmingming
     *
     * @param string $tableName 表名
     *
     * @return $this
     */
    public function delete($tableName)
    {
        $this->query_type = self::TYPE_DELETE;
        $this->setTableName($tableName);

        return $this;
    }

    /**
     * @desc   where 等价于andWhere
     * @author chenmingming
     *
     * @param string $field 字段或者表达式
     *
     * @return QueryBuilder
     */
    public function where($field)
    {
        return $this->andWhere($field);
    }

    /**
     * @desc   andWhere 与条件语句
     * @author chenmingming
     *
     * @param string $field 字段或者表达式
     *
     * @return $this
     */
    public function andWhere($field)
    {
        $this->parseCurrentField();
        $this->current_type  = self::QUERY_WHERE;
        $this->current_logic = self::LOGIC_AND;
        $this->current_field = $field;

        return $this;
    }

    /**
     * @desc   orWhere 或者条件
     * @author chenmingming
     *
     * @param string $field 字段或者表达式
     *
     * @return $this
     */
    public function orWhere($field)
    {
        $this->parseCurrentField();
        $this->current_type  = self::QUERY_WHERE;
        $this->current_logic = self::LOGIC_OR;
        $this->current_field = $field;

        return $this;
    }

    /**
     * @desc   eq 等于
     * @author chenmingming
     *
     * @param string $value 值
     *
     * @return QueryBuilder
     */
    public function eq($value)
    {
        return $this->exp($value, '=');
    }

    /**
     * @desc   gt 大于某个值
     * @author chenmingming
     *
     * @param string $value 值
     *
     * @return QueryBuilder
     */
    public function gt($value)
    {
        return $this->exp($value, '>');
    }

    /**
     * @desc   lt 小于
     * @author chenmingming
     *
     * @param string $value 值
     *
     * @return QueryBuilder
     */
    public function lt($value)
    {
        return $this->exp($value, '<');
    }

    /**
     * @desc   neq 不等于
     * @author chenmingming
     *
     * @param string $value 值
     *
     * @return QueryBuilder
     */
    public function neq($value)
    {
        return $this->exp($value, '!=');
    }

    /**
     * @desc   ge 大于等于
     * @author chenmingming
     *
     * @param string $value 值
     *
     * @return QueryBuilder
     */
    public function ge($value)
    {
        return $this->exp($value, '>=');
    }

    /**
     * @desc   le 小于等于
     * @author chenmingming
     *
     * @param string $value 值
     *
     * @return QueryBuilder
     */
    public function le($value)
    {
        return $this->exp($value, '<=');
    }

    /**
     * @desc   in
     * @author chenmingming
     *
     * @param array $array 查询的范围
     *
     * @return QueryBuilder
     */
    public function in(array $array)
    {
        return $this->exp($array, 'IN');
    }

    /**
     * @desc   isNull
     * @author chenmingming
     * @return $this
     */
    public function isNull()
    {
        $this->current_field .= ' IS NULL ';

        return $this;
    }

    /**
     * @desc   set update 和 insert 设置字段的值
     * @author chenmingming
     *
     * @param string $field 字段名称
     *
     * @return $this
     */
    public function set($field)
    {
        $this->parseCurrentField();
        $this->current_logic = self::LOGIC_AND;
        $this->current_field = $field;
        $this->current_type  = self::QUERY_DATA;

        return $this;
    }

    /**
     * @desc   value 值
     * @author chenmingming
     *
     * @param string $value 设置的值
     *
     * @return $this
     */
    public function value($value)
    {
        $this->current_value = $value;
        $this->current_exp   = false;
        $this->parseCurrentField();

        return $this;
    }

    /**
     * @desc   value 表达式值
     * @author chenmingming
     *
     * @param string $value 设置的值 表达式值
     *
     * @return $this
     */
    public function expValue($value)
    {
        $this->current_value = $value;
        $this->current_exp   = true;
        $this->parseCurrentField();

        return $this;
    }

    /**
     * @desc   isNotNull
     * @author chenmingming
     * @return $this
     */
    public function isNotNull()
    {
        $this->current_field .= ' IS NOT NULL ';

        return $this;
    }

    /**
     * @desc   exp
     * @author chenmingming
     *
     * @param      $value
     * @param null $exp
     *
     * @return $this
     */
    public function exp($value, $exp = null)
    {
        $this->current_exp   = $exp;
        $this->current_value = $value;
        $this->parseCurrentField();

        return $this;
    }

    /**
     * @desc   order 排序
     * @author chenmingming
     *
     * @param string $order 排序
     */
    public function order($order)
    {
        $this->order = $order;
    }

    /**
     * @desc   limit
     * @author chenmingming
     *
     * @param int $start 开始
     * @param int $size  数量
     *
     * @return $this
     */
    public function limit($start, $size)
    {
        $this->limit = " LIMIT {$start},{$size} ";

        return $this;
    }

    /**
     * @desc   setTableName 设置表名
     * @author chenmingming
     *
     * @param string $tableName 表名称
     * @param string $alias     表别名
     */
    protected function setTableName($tableName, $alias = '')
    {
        $this->table_name = "`{$this->options['tablePrefix']}{$tableName}`";
        if ($alias) {
            $this->table_name .= " AS {$alias} ";
        }
    }

    /**
     * @desc   __toString
     * @author chenmingming
     * @return string
     */
    public function __toString()
    {
        return $this->getSql();
    }

    /**
     * @desc   getSql 获取生成的sql
     * @author chenmingming
     * @return string
     */
    public function getSql()
    {
        if (is_null($this->sql)) {
            $this->parse();
        }

        return $this->sql;
    }

    /**
     * @desc   parse 解析sql
     * @author chenmingming
     * @return $this
     */
    public function parse()
    {
        $this->parseCurrentField();
        $this->sql = '';
        switch ($this->query_type) {
            case self::TYPE_SELECT:
                $this->parseSelect();
                break;
            case self::TYPE_UPDATE:
                $this->parseUpdate();
                break;
        }

        return $this;
    }

    /**
     * @desc   parseUpdate
     * @author chenmingming
     */
    public function parseUpdate()
    {
        $this->sql = "UPDATE {$this->table_name}";
        $this->sql .= $this->getUpdateStr();
        $this->sql .= $this->getWhereStr();
    }

    /**
     * @desc   parseSelect 解析select语句
     * @author chenmingming
     */
    protected function parseSelect()
    {
        $this->sql = "SELECT {$this->field} FROM {$this->table_name} ";
        $this->sql .= $this->getWhereStr();
        $this->order && $this->sql .= " ORDER BY {$this->order} ";
        $this->limit && $this->sql .= " {$this->limit} ";
    }

    /**
     * @desc   getUpdateStr
     * @author chenmingming
     * @return string
     */
    protected function getUpdateStr()
    {
        $str = '';
        if ($this->data) {
            foreach ($this->data as $item) {
                list(, $field, $exp, $value) = $item;
                if ($str == '') {
                    $str = ' SET ';
                } else {
                    $str .= ',';
                }
                if ($exp) {
                    $str .= "`{$field}`={$value}";

                } else {
                    $str .= "`{$field}`=?";
                    $this->params[] = $value;
                }

            }
        }

        return $str;
    }

    /**
     * @desc   getWhereStr 解析where str
     * @author chenmingming
     * @return string
     */
    protected function getWhereStr()
    {
        $str = '';
        if ($this->where) {
            foreach ($this->where as $item) {
                list($logic, $field, $exp, $value) = $item;
                if ($str == '') {
                    $str .= ' WHERE ';
                } else {
                    $str .= " {$logic} ";
                }

                if ($exp) {
                    if ($exp == 'IN') {
                        if (is_array($value)) {

                            $tmp = [];
                            foreach ($value as $v) {
                                $tmp[]          = '?';
                                $this->params[] = $v;
                            }
                            $str .= " {$field} IN (" . implode(',', $tmp) . ") ";
                        }

                    } else {
                        $str .= " {$field} {$exp} ? ";
                        $this->params[] = $value;
                    }
                } else {
                    $str .= " {$field} {$value}";
                }

            }
        }

        return $str;
    }

    /**
     * @desc   parseCurrentField 解析当前字段
     * @author chenmingming
     */
    protected function parseCurrentField()
    {
        if ($this->current_field) {
            $tmp = [
                $this->current_logic,
                $this->current_field,
                $this->current_exp,
                $this->current_value,
            ];
            if ($this->current_type == self::QUERY_WHERE) {
                $this->where[] = $tmp;
            } else {
                $this->data[] = $tmp;
            }
            $this->current_logic = $this->current_exp = $this->current_value = $this->current_field = null;
        }
    }

    public function fetch()
    {
        return Db::query($this->getSql(), $this->params)->fetch();
    }

    /**
     * @desc   getFiled
     * @author chenmingming
     *
     * @param string $key 字段名称
     *
     * @return null|string
     */
    public function getField($key)
    {
        $arr = $this->fetch();

        return isset($arr[$key]) ? $arr[$key] : null;
    }

    public function fetchAll()
    {
        return Db::query($this->getSql(), $this->params)->fetchAll();
    }

    public function exec()
    {
        return Db::exec($this->getSql(), $this->params);
    }

    public function getParams()
    {
        $this->getSql();

        return $this->params;
    }
}