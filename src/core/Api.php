<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 11:04
 */

namespace mmapi\core;

abstract class Api
{

    //接口请求开始时间
    private $_start_time;
    //参数定义
    private $options = [
        'debug'            => false,
        'format_to_string' => true,
        'default_code'     => 'SUCCESS',
        'default_msg'      => 'SUCCESS',
    ];

    /** @var ApiParams[] */
    protected $params = [];

    /**
     * @desc   init 初始化
     * @author chenmingming
     */
    abstract protected function init();

    /**
     * @desc   run 主线程
     * @author chenmingming
     * @return void
     */
    abstract public function run();

    /**
     * FrontApi constructor. 构造函数
     */
    public function __construct()
    {
        $this->_start_time = microtime(true);
        set_exception_handler([$this, 'exceptionHandler']);
        $this->options = array_merge($this->options, Config::get('api', []));
        $this->init();
        $this->parse();
        $this->set('code', $this->options['default_code']);
        $this->set('msg', $this->options['default_msg']);
    }

    /**
     * @desc   parse
     * @author chenmingming
     */
    protected function parse()
    {
        foreach ($this->params as $param) {
            $param->parse();
        }
    }

    /**
     * @desc   exceptionHandler 异常拦截
     * @author chenmingming
     *
     * @param AppException $e
     */
    public function exceptionHandler(AppException $e)
    {
        $errno = method_exists($e, 'getErrno') ? $e->getErrno() : $e->getCode();

        $this->set('code', $errno)
            ->set('msg', $e->getMessage())
            ->send();
    }

    /**
     * @desc   setParams 设置参数
     * @author chenmingming
     *
     * @param ApiParams $param 参数
     *
     * @return $this
     */
    final public function addParam(ApiParams $param)
    {
        $param->parse();
        $this->setField($param->getKey(), $param->getValue());

        return $this;
    }

    /**
     * @desc   createParam
     * @author chenmingming
     *
     * @param        $key
     * @param string $type
     *
     * @return ApiParams
     */
    protected function createParam($key, $type = ApiParams::TYPE_STRING)
    {
        return new ApiParams($key, $type);
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
     * @desc   set 设置返回参数
     * @author chenmingming
     *
     * @param string $key   key
     * @param string $value value
     *
     * @return $this
     */
    final protected function set($key, $value)
    {
        Response::create()->set($key, $value, $this->options['format_to_string']);

        return $this;
    }

    /**
     * @desc   _beforeOutput 输出数据之前的拦截器
     * @author chenmingming
     */
    protected function beforeRespnse()
    {

    }

    /**
     * @desc   output
     * @author chenmingming
     *
     */
    final protected function send()
    {
        $this->options['debug'] && $this->calcost();
        $this->beforeRespnse();
        Response::create()->send();
        $this->afterResponse();
    }

    /**
     * @desc   afterRequest 自定义结束拦截器
     * @author chenmingming
     */
    protected function afterResponse()
    {

    }

    /**
     * @desc    计算该接口时间消耗和内存消耗
     * @author  陈明明 mailto:838965806@qq.com
     * @since   2015-08-07 13:02:56
     */
    protected function calcost()
    {
        $this->set('debug', [
            'cost_time' => sprintf('%.6f', microtime(true) - $this->_start_time),
            'cost_mem'  => sprintf('%.6f', memory_get_usage(true) / 1048576),
        ]);
    }

    /**
     * @desc   option 配置
     * @author chenmingming
     *
     * @param string $key   key
     * @param mixed  $value value
     */
    public function option($key, $value)
    {
        $this->options[$key] = $value;
    }
}