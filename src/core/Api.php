<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 11:04
 */

namespace mmapi\core;

use mmapi\api\ApiException;
use Prophecy\Argument\Token\LogicalAndToken;

abstract class Api
{
    //是否开始调试
    const OPT_DEBUG = 'debug';
    //是否格式化接口输出数据 接口返回只有string和boolean类型
    const OPT_FORMAT_TO_STRING = 'format_to_string';
    //接口默认返回code
    const OPT_DEFAULT_CODE = 'default_code';
    //接口默认返回提交消息
    const OPT_DEFAULT_MSG = 'default_msg';
    //阻止重复提交表单 默认关闭 false
    //key :阻止重复提交的唯一key 为null则对当前请求url进行hash
    //expire: 阻止重复提交的最大有效期 超过有效期则自动失效 默认2s
    //code：当验证是重复提交时 产生的错误码
    //msg:当验证是重复提交时 提交文字内容
    //cache_key_suffix:队列缓存key前缀 默认 deny_resubmit_
    // ['key'    => null,'expire' => 2,'code'   => 'SYSTEM_BUSY','msg'    => '系统正在处理中...']
    const OPT_DENY_RESUBMIT = 'deny_resubmit';

    //接口请求开始时间
    protected $_start_time;
    //参数定义
    protected $options = [
        self::OPT_DEBUG            => false,
        self::OPT_FORMAT_TO_STRING => true,
        self::OPT_DENY_RESUBMIT    => false,
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
     * @var array 接口返回数据数组
     */
    private $return = [];

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
        //检查是否重复提交
        $this->checkResubmit();
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
        $formatToString = $this->options[self::OPT_FORMAT_TO_STRING] == true;

        $response = Response::create();
        foreach ($this->return as $key => $value) {
            $response->set($key, $value, $formatToString);
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
        $this->finishSubmit();
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

    /**
     * @desc   setDenyResubmitKey 设置防止重复提交的唯一key
     * @author chenmingming
     *
     * @param array  $params 参与的key
     * @param string $suffix 前缀
     */
    protected function setDenyResubmitKey(array $params = [], $suffix = '')
    {
        $this->initResubmit();
        $this->options[self::OPT_DENY_RESUBMIT]['key_params'] = $params;
        $this->options[self::OPT_DENY_RESUBMIT]['key_suffix'] = $suffix;
    }

    /**
     * @desc   getDenyResubmitKey 获取key
     * @author chenmingming
     * @return string
     */
    protected function getDenyResubmitKey()
    {
        if ($this->options[self::OPT_DENY_RESUBMIT]) {
            if ($this->options[self::OPT_DENY_RESUBMIT]['key'] === null) {
                $keyArray = [];
                if ($this->options[self::OPT_DENY_RESUBMIT]['key_params']) {
                    foreach ($this->options[self::OPT_DENY_RESUBMIT]['key_params'] as $param) {
                        $apiParam = $this->get($param);
                        if ($apiParam && is_string($apiParam->getValue())) {
                            $keyArray[] = $apiParam->getValue();
                        }
                    }
                } else {
                    $keyArray = [
                        $_SERVER['REQUEST_URI'],
                        $_SERVER['HTTP_COOKIE'],
                    ];
                }

                $keyStr                                        = $this->options[self::OPT_DENY_RESUBMIT]['key_suffix'] . implode('-', $keyArray);
                $this->options[self::OPT_DENY_RESUBMIT]['key'] =
                    $this->options[self::OPT_DENY_RESUBMIT]['cache_key_pre']
                    . md5($keyStr);

                $this->debug('deny_key_str', $keyStr);
                $this->debug('deny_key', $this->options[self::OPT_DENY_RESUBMIT]['key']);
            }

            return $this->options[self::OPT_DENY_RESUBMIT]['key'];

        } else {
            return '';
        }
    }

    /**
     * @desc   finishSubmit 清除重复提交验证
     * @author chenmingming
     */
    protected function finishSubmit()
    {
        if ($this->options[self::OPT_DENY_RESUBMIT]) {
            if ($this->return['code'] != $this->options[self::OPT_DENY_RESUBMIT]['code'])
                //如果开始了重复提交 则业务正常结束后删除key
                Cache::rm($this->getDenyResubmitKey());
        }
    }

    /**
     * @desc   checkResubmit 检查是否重复提交
     * @author chenmingming
     * @throws ApiException
     */
    protected function checkResubmit()
    {
        if ($this->options[self::OPT_DENY_RESUBMIT]) {
            $this->initResubmit();
            //已经开启防止重复提交
            $queueNum = Cache::inc($this->getDenyResubmitKey(), 1, $this->options[self::OPT_DENY_RESUBMIT]['expire']);
            $this->debug('queueNum', $queueNum);
            if ($queueNum !== 1) {
                throw new ApiException(
                    $this->options[self::OPT_DENY_RESUBMIT]['msg'],
                    $this->options[self::OPT_DENY_RESUBMIT]['code']
                );
            }
        }
    }

    /**
     * @desc   initResubmit 初始化重复提交配置
     * @author chenmingming
     */
    private function initResubmit()
    {
        if (isset($this->options[self::OPT_DENY_RESUBMIT]['init'])) {
            return;
        }
        if (!is_array($this->options[self::OPT_DENY_RESUBMIT])) {
            $this->options[self::OPT_DENY_RESUBMIT] = [];
        }
        $this->options[self::OPT_DENY_RESUBMIT] = array_merge([
            'key'           => null,
            'key_params'    => [],
            'key_suffix'    => '',
            'expire'        => 2,
            'code'          => 'SYSTEM_BUSY',
            'msg'           => '系统正在处理中...',
            'cache_key_pre' => 'deny_resubmit_',
            'init'          => true,
        ], $this->options[self::OPT_DENY_RESUBMIT]);
    }

}