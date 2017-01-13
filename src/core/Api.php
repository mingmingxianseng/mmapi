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
    const OPT_DEBUG = 'debug';
    const OPT_FORMAT_TO_STRING = 'format_to_string';
    const OPT_DEFAULT_CODE = 'default_code';
    const OPT_DEFAULT_MSG = 'default_msg';
    //接口请求开始时间
    protected $_start_time;
    //参数定义
    protected $options = [
        self::OPT_DEBUG            => false,
        self::OPT_FORMAT_TO_STRING => true,
        self::OPT_DEFAULT_CODE     => 'SUCCESS',
        self::OPT_DEFAULT_MSG      => 'SUCCESS',
    ];

    /** @var ApiParams[] */
    protected $params = [];

    /**
     * @var array 调试信息
     */
    private $debug = [];

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
     * @desc   beforeRun
     * @author chenmingming
     */
    protected function beforeRun()
    {
        $this->init();
        $this->parse();
    }

    /**
     * @desc   main 主线程
     * @author chenmingming
     */
    final public function main()
    {
        $this->beforeRun();
        $this->run();
        $this->send();
    }

    /**
     * FrontApi constructor. 构造函数
     */
    public function __construct()
    {
        set_exception_handler([$this, 'exceptionHandler']);
        $this->_start_time = microtime(true);
        $this->options     = array_merge($this->options, Config::get('api', []));
        $this->set('code', $this->options[self::OPT_DEFAULT_CODE]);
        $this->set('msg', $this->options[self::OPT_DEFAULT_MSG]);
    }

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
     * @desc   exceptionHandler 异常拦截
     * @author chenmingming
     *
     * @param \Exception $e
     */
    public function exceptionHandler(\Exception $e)
    {
        if ($this->getOption(self::OPT_DEBUG)) {
            $this
                ->set('exception', get_class($e))
                ->set('trace', explode("\n", $e->getTraceAsString()));
            method_exists($e, 'getDetail')
            &&
            $this->set('detail', $e->getDetail());
        }
        $errno = method_exists($e, 'getErrno') ? $e->getErrno() : 'ERROR';
        $this->error($e->getMessage(), $errno);
        App::handleException($e);
    }

    /**
     * @desc   error 错误输出
     * @author chenmingming
     *
     * @param string $msg  错误字符串
     * @param string $code 错误码
     */
    protected function error($msg, $code)
    {
        $this->set('code', $code)
            ->set('msg', $msg)
            ->send();
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
        Response::create()->set($key, $value, $this->options[self::OPT_FORMAT_TO_STRING] == true);

        return $this;
    }

    /**
     * @desc   _beforeOutput 输出数据之前的拦截器
     * @author chenmingming
     */
    protected function beforeResponse()
    {
        $this->options[self::OPT_DEBUG] && $this->set('debug', $this->debug);
    }

    /**
     * @desc   output
     * @author chenmingming
     *
     */
    final private function send()
    {
        $this->options[self::OPT_DEBUG] && $this->calcost();
        $this->beforeResponse();
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
        $this->debug('cost_time', sprintf('%.6f', microtime(true) - $this->_start_time));
        $this->debug('cost_mem', sprintf('%.6f', memory_get_usage(true) / 1048576));
    }

    /**
     * @desc   debug
     * @author chenmingming
     *
     * @param $key
     * @param $value
     */
    protected function debug($key, $value)
    {
        $this->debug[$key] = $value;
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

    /**
     * @desc   getOption
     * @author chenmingming
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function getOption($key)
    {
        return $this->options[$key];
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