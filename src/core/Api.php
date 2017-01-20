<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 11:04
 */

namespace mmapi\core;


abstract class Api extends ParseParams
{
    //是否开始调试
    const OPT_DEBUG = 'debug';
    //是否格式化接口输出数据 接口返回只有string和boolean类型
    const OPT_FORMAT_TO_STRING = 'format_to_string';
    //接口默认返回code
    const OPT_DEFAULT_CODE = 'default_code';
    //接口默认返回提交消息
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
    /**
     * @var array 接口返回数据数组
     */
    protected $return = [];

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
        //自定义初始化 自定义参数
        $this->init();
        //解析获取参数
        $this->parse();
        $this->set('code', $this->options[self::OPT_DEFAULT_CODE]);
        $this->set('msg', $this->options[self::OPT_DEFAULT_MSG]);
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
    }

    /**
     * @desc   exceptionHandler 异常拦截
     * @author chenmingming
     *
     * @param \Throwable $e
     */
    public function exceptionHandler(\Throwable $e)
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
        if (strpos($key, '.') > 0) {
            list($pK, $k) = explode('.', $key);
            $this->return[$pK][$k] = $value;
        } else {
            $this->return[$key] = $value;
        }

        return $this;
    }

    /**
     * @desc   _beforeOutput 输出数据之前的拦截器
     * @author chenmingming
     */
    protected function beforeResponse()
    {
        $this->debug('cost_time', sprintf('%.6f', microtime(true) - $this->_start_time));
        $this->debug('cost_mem', sprintf('%.6f', memory_get_usage(true) / 1048576));
    }

    /**
     * @desc   output
     * @author chenmingming
     *
     */
    final private function send()
    {
        $this->beforeResponse();
        $response = Response::create();
        $response->options([self::OPT_FORMAT_TO_STRING => $this->options[self::OPT_FORMAT_TO_STRING]]);
        foreach ($this->return as $key => $value) {
            $response->set($key, $value);
        }
        $response->send();
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
     * @desc   debug
     * @author chenmingming
     *
     * @param string $key   key
     * @param mixed  $value 值
     */
    protected function debug($key, $value)
    {
        if ($this->options[self::OPT_DEBUG]) {
            $this->set('debug.' . $key, $value);
        }
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

}