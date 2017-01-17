<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/17
 * Time: 11:33
 */

namespace mmapi\core;

class ParseParams implements Params
{
    //是否开始调试
    /** @var ApiParams[] */
    protected $params = [];
    /**
     * @desc   parse
     * @author chenmingming
     */
    protected function parse()
    {
        foreach ($this->params as $param) {
            $param->parse();
            $this->setField($param->getKey(), $param->getValue());
        }
    }
    /**
     * @desc   setParams 设置参数
     * @author chenmingming
     *
     * @param ApiParams|string $param 参数
     *
     * @return ApiParams
     */
    final public function addParam($param)
    {
        if ($param instanceof ApiParams) {
            $this->params[$param->getKey()] = $param;
        } else {
            $this->params[$param] = ApiParams::create($param);
        }

        return $this->params[$param];
    }

    /**
     * @desc   removeParam
     * @author chenmingming
     *
     * @param string $key 参数名称
     */
    final public function removeParam($key)
    {
        if (isset($this->params[$key])) {
            unset($this->params[$key]);
        }
    }

    /**
     * @desc   get 获取参数
     * @author chenmingming
     *
     * @param string $key 参数名称
     *
     * @return ApiParams|null
     */
    final public function get($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return null;
    }

    /**
     * @desc   addParams 批量添加
     * @author chenmingming
     *
     * @param array $params 参数列表
     */
    final public function addParams(array $params)
    {
        foreach ($params as $param) {
            $this->addParam($param);
        }
    }

    /**
     * @desc   setField
     * @author chenmingming
     *
     * @param string $field 属性
     * @param mixed  $value 值
     */
    private function setField($field, $value)
    {
        $this->$field = $value;
    }


    /**
     * @desc   __get 获取值
     * @author chenmingming
     *
     * @param $key
     *
     * @return mixed|null
     */
    public function __get($key)
    {
        $apiParam = $this->get($key);
        if (is_null($apiParam)) {
            return null;
        }

        return $apiParam->getValue();
    }
}