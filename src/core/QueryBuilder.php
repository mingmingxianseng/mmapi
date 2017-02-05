<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/1
 * Time: 20:57
 */

namespace mmapi\core;

class QueryBuilder
{
    const TYPE_SELECT = 'SELECT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_INSERT = 'INSERT';
    const TYPE_REPLACE = 'REPLACE';
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
    private $join = '';//关联表

    private $table_name;//表名
    private $data;
    private $field;

    private $joinTable;//关联表
    private $joinCondition;//关联条件
    private $joinType;//json方式

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
    /** @var  Db */
    private $db;

    public function __construct(Db $db, $options)
    {
        $this->db      = $db;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @desc   setOptions 配置
     * @author chenmingming
     *
     * @param string $option 配置项
     * @param mixed  $value  配置值
     *
     * @return $this
     */
    public function setOptions($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
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

    public function join($tableName, $alias = '', $type = 'INNER')
    {
        $this->parseJoin();
        $this->joinType  = $type;
        $this->joinTable = $tableName . ' ' . $alias;

        return $this;
    }

    public function leftJoin($tableName, $alias = '')
    {
        return $this->join($tableName, $alias, 'LEFT');
    }

    public function rightJoin($tableName, $alias = '')
    {
        return $this->join($tableName, $alias, 'RIGHT');
    }

    /**
     * @desc   on
     * @author chenmingming
     *
     * @param        $fieldA
     * @param        $fieldB
     * @param string $exp
     * @param string $logic
     *
     * @return $this
     */
    public function on($fieldA, $fieldB, $exp = '=', $logic = 'and')
    {
        $this->joinCondition[] = [$fieldA, $fieldB, $exp, $logic];

        return $this;
    }

    /**
     * @desc   parseJoin 解析关联表关系
     * @author chenmingming
     */
    protected function parseJoin()
    {
        $sql = '';
        if ($this->joinTable && $this->joinType && $this->joinCondition) {
            $sql   = "$this->joinType JOIN {$this->joinTable}";
            $first = true;
            foreach ($this->joinCondition as $condition) {
                list($filedA, $filedB, $exp, $logic) = $condition;
                if ($first) {
                    $sql .= " ON {$filedA} {$exp} {$filedB}";
                    $first = false;
                } else {
                    $sql .= " {$logic} {$filedA} {$exp} {$filedB}";
                }
            }
            $this->joinTable = $this->joinType = $this->joinCondition = null;
        }
        $this->join .= $sql;
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
     * @desc   insert
     * @author chenmingming
     *
     * @param string $tableName 表名称
     *
     * @return $this
     */
    public function insert($tableName)
    {
        $this->query_type = self::TYPE_INSERT;
        $this->setTableName($tableName);

        return $this;
    }

    /**
     * @desc   replace replace语句
     * @author chenmingming
     *
     * @param string $tableName 表名称
     *
     * @return $this
     */
    public function replace($tableName)
    {
        $this->query_type = self::TYPE_REPLACE;
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

    public function notin(array $array)
    {
        return $this->exp($array, 'NOT IN');
    }

    /**
     * @desc   match 模糊匹配关键字
     * @author chenmingming
     *
     * @param string $keywords 关键字
     *
     * @return QueryBuilder
     */
    public function match($keywords)
    {
        $kw = str_replace("_", "\_", trim($keywords));
        $kw = str_replace("%", "\%", $kw);

        return $this->like("%{$kw}%");
    }

    /**
     * @desc   like
     * @author chenmingming
     *
     * @param string $keywords 关键字
     *
     * @return QueryBuilder
     */
    public function like($keywords)
    {
        return $this->exp($keywords, 'like');
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
        $this->exp($value, false);

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
        $this->exp($value, true);

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
     *
     * @return $this
     */
    public function order($order)
    {
        $this->order = $order;

        return $this;
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
            case self::TYPE_INSERT:
                $this->parseInsert();
                break;
            case self::TYPE_REPLACE:
                $this->parseReplace();
                break;
            case self::TYPE_DELETE:
                $this->parseDelete();
                break;
        }

        return $this;
    }

    /**
     * @desc   parseDelete
     * @author chenmingming
     */
    public function parseDelete()
    {
        $this->sql = "DELETE FROM {$this->table_name} ";
        $this->sql .= $this->getWhereStr();
    }

    /**
     * @desc   parseInsert 解析insert语法
     * @author chenmingming
     */
    protected function parseInsert()
    {
        $this->sql = "INSERT INTO {$this->table_name} ";
        $this->sql .= $this->getInsertStr();
    }

    /**
     * @desc   parseReplace 解析replace语法
     * @author chenmingming
     */
    protected function parseReplace()
    {
        $this->sql = "REPLACE INTO {$this->table_name} ";
        $this->sql .= $this->getInsertStr();
    }

    /**
     * @desc   parseUpdate
     * @author chenmingming
     */
    protected function parseUpdate()
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
        $this->sql .= $this->getJoinStr();
        $this->sql .= $this->getWhereStr();
        $this->order && $this->sql .= " ORDER BY {$this->order} ";
        $this->limit && $this->sql .= " {$this->limit} ";
    }

    /**
     * @desc   getJoinStr
     * @author chenmingming
     * @return string
     */
    protected function getJoinStr()
    {
        $this->parseJoin();

        return $this->join;
    }

    /**
     * @desc   getInsertStr 获取insert字符串
     * @author chenmingming
     * @return string
     */
    protected function getInsertStr()
    {
        $fileds = [];
        $values = [];
        if ($this->data) {
            foreach ($this->data as $item) {
                list(, $field, $exp, $value) = $item;
                $fileds[] = "`$field`";
                if ($exp) {
                    //表达式
                    $values[] = $value;
                } else {
                    $values[]       = '?';
                    $this->params[] = $value;
                }

            }
        }

        return sprintf('(%s)VALUES(%s)', implode(',', $fileds), implode(',', $values));
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
                    //表达式
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
                    if ($exp == 'IN' || $exp == "NOT IN") {
                        if (is_array($value)) {
                            $this->params = array_merge($this->params, $value);
                            $str .= " {$field} {$exp} (" . implode(',', array_fill(0, count($value), '?')) . ") ";
                        }
                        //非数组直接跳过
                    } else {
                        $str .= " {$field} {$exp} ? ";
                        $this->params[] = $value;
                    }
                } else {
                    //运算表达式
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

    /**
     * @desc   fetch
     * @author chenmingming
     * @return mixed
     */
    public function fetch()
    {
        $this->limit(0, 1);

        return $this->db->query($this->getSql(), $this->params)->fetch();
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

    /**
     * @desc   fetchAll
     * @author chenmingming
     * @return array
     */
    public function fetchAll()
    {
        return $this->db->query($this->getSql(), $this->params)->fetchAll();
    }

    /**
     * @desc   exec return affected rows
     * @author chenmingming
     * @return int
     */
    public function exec()
    {
        return $this->db->exec($this->getSql(), $this->params);
    }

    /**
     * @desc   getParams
     * @author chenmingming
     * @return array
     */
    public function getParams()
    {
        $this->getSql();

        return $this->params;
    }

    /**
     * @desc   getDb 获取db实例
     * @author chenmingming
     * @return Db
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @desc   getLastInsertId
     * @author chenmingming
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->db->getLastInsertId();
    }
}