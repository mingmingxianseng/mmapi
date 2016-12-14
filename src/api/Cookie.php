<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 11:00
 */

namespace mmapi\api;

abstract class Cookie
{
    //参数类型
    //整形
    const PARAM_INT = 'int';
    //字符串类型
    const PARAM_STRING = 'string';
    //浮点型
    const PARAM_FLOAT = 'float';
    //json格式
    const PARAM_JSON = 'json';

    const PARAM_METHOD_GET = 'get';
    const PARAM_METHOD_POST = 'post';
    //get post 均可
    const PARAM_METHOD_REQUEST = 'request';
    const PARAM_METHOD_COOKIE = 'cookie';

    //成功code
    const CODE_SUCCESS = 'SUCCESS';
    //成功返回消息
    const MSG_SUCCESS = 'SUCCESS';

    //是否验证 签名
    const OPTION_CHECK = 'check_sign';
    //是否debug调试
    const OPTION_DEBUG = 'debug';
    //签名字段
    const OPTION_SIGN_KEY = 'sign_name';
    //密钥
    const OPTION_SIGN_SECRET = 'sign_secret';
    //参数传输方式
    const OPTION_METHOD = 'method';

    //是否包含公共参数
    const OPTION_IS_GLOBAL = 'is_global';
    //公共参数
    const OPTION_GLOBAL_PARAMS = 'global_param';
    //验证参数规则
    const OPTION_VALIDATE = 'validate';
    //输出结果全部为string
    const OPTION_OUTPUT_ALL_STRING = 'output_all_string';
    const VALIDATE_TYPE_COMMON = 'common';//一般验证
    const VALIDATE_TYPE_REG = 'reg';//正则验证
    const VALIDATE_TYPE_IN = 'in';//在范围里

    //接口参数  数组格式 ['key','参数格式','是否必须 默认必须','默认值']  ['id', self::PARAM_INT, false]
    const OPTION_PARAMS = 'params';
    //返回信息数组
    private $_return = [
        'code' => self::CODE_SUCCESS,
        'msg'  => self::MSG_SUCCESS,
    ];

    //接口请求开始时间
    private $_start_time;
    //参数定义
    private $options = [
        self::OPTION_DEBUG             => false,
        self::OPTION_CHECK             => true,
        self::OPTION_IS_GLOBAL         => true,
        self::OPTION_SIGN_KEY          => 'sign',
        self::OPTION_SIGN_SECRET       => '2a5f4e6ee46c26cd3dae942f5e2d2ea6',
        self::OPTION_METHOD            => self::PARAM_METHOD_REQUEST,
        self::OPTION_PARAMS            => [],
        //全局参数
        self::OPTION_GLOBAL_PARAMS     => [
        ],
        //验证规则
        self::OPTION_VALIDATE          => [

        ],
        self::OPTION_OUTPUT_ALL_STRING => true,
    ];

    protected $auth;
    protected $sign;
    protected $V;
    protected $F;

    protected $_params = [];

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
     * @desc   _init 初始化
     * @author chenmingming
     */
    final private function _init()
    {
        //添加公共参数
        $this->setGlobalParams('V')
            ->setGlobalParams('sign')
            ->setGlobalParams('F')
            ->setGlobalParams('noncestr')
            ->setGlobalParams('auth', self::PARAM_STRING, true);
    }

    /**
     * FrontApi constructor. 构造函数
     */
    public function __construct()
    {
        $this->_start_time = microtime(true);
        set_exception_handler([$this, 'exceptionHandler']);
        $this->_init();
        $this->init();

        $this->options[self::OPTION_DEBUG] && $this->_return['debug'] = [];

        //获取参数
        $this->parseParams();
        //验证参数
        $this->validateParams();
        $this->options[self::OPTION_CHECK] && $this->checkSign();
    }

    /**
     * @desc   thisUserObj 返回当前登录用户
     * @author chenmingming
     * @return AxUser
     */
    protected function loginUserObj()
    {
        if (is_null($this->_userObj)) {
            $uid = AxUser::auth2Id($this->auth, $password);
            if ($uid <= 0) {
                throw new AppException('您还没有登录~', 'USER_NOT_LOGIN');
            }
            $this->_userObj = AxUser::getInstance($uid);
            if ($this->_userObj->getPassword() != $password) {
                throw new AppException('登录已经失效~请重新登录', 'AUTH_INVALID');
            }
            if ($this->loginUserObj()->isLock()) {
                throw new AppException('您的账号已经被锁定，请联系管理员~', 'USER_LOCKED');
            }
        }

        return $this->_userObj;
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
        $this->setCode($errno)->setMsg($e->getMessage())
            ->output();
    }

    /**
     * @desc   setParams 设置参数
     * @author chenmingming
     *
     * @param string $key     设置key
     * @param string $type    设置参数类型
     * @param string $method  设置参数传递方式
     * @param bool   $require 设置参数是否必传
     * @param null   $default 设置参数默认值
     */
    final protected function setParams($key, $type = self::PARAM_STRING, $method = null, $require = true, $default = null)
    {
        $this->options[self::OPTION_PARAMS][$key] = [$key, $type, $require == true, $default, is_null($method) ? $this->options[self::OPTION_METHOD] : $method];

        return $this;
    }

    /**
     * @desc   setValidate
     * @author chenmingming
     *
     * @param string       $key      字段名称
     * @param string|array $validate 验证规则
     * @param array        $error    验证失败错误提示 [$error_str,$errno]
     * @param string       $type     验证类型
     *
     * @return $this
     */
    final protected function setValidate($key, $validate, $error = [], $type = self::VALIDATE_TYPE_COMMON)
    {
        $this->options[self::OPTION_VALIDATE][] = [$key, $validate, $error, $type];

        return $this;
    }

    /**
     * @desc   setParams 设置参数
     * @author chenmingming
     *
     * @param string $key     设置key
     * @param string $type    设置参数类型
     * @param bool   $require 设置参数是否必传
     * @param null   $default 设置参数默认值
     * @param string $method  设置参数传递方式
     */
    final protected function setGlobalParams($key, $type = self::PARAM_STRING, $require = true, $default = null, $method = null)
    {
        if (is_null($type)) {
            unset($this->options[self::OPTION_GLOBAL_PARAMS][$key]);

            return $this;
        }
        $this->options[self::OPTION_GLOBAL_PARAMS][$key] = [$key, $type, $require == true, $default, is_null($method) ? $this->options[self::OPTION_METHOD] : $method];

        return $this;
    }

    /**
     * @desc   setOptions 设置接口配置
     * @author chenmingming
     *
     * @param string $key   key
     * @param mixed  $value 值
     */
    final protected function setOptions($key, $value)
    {
        switch ($key) {
            case self::OPTION_DEBUG:
            case self::OPTION_CHECK:
            case self::OPTION_IS_GLOBAL:
                $value = $value == true;
                break;
            case self::OPTION_SIGN_SECRET:
            case self::OPTION_SIGN_KEY:
            case self::OPTION_METHOD:
                $value = (string)$value;
                break;
            default:
                throw new AppException(["配置项" . (string)$key . ' 非法', 'API_OPTIONS_INVALID']);
        }
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @desc   checkSign 验证签名
     * @author chenmingming
     * @throws AppException
     */
    protected function checkSign()
    {
        $signName = $this->options[self::OPTION_SIGN_KEY];
        if ($this->$signName != $this->makeSign()) {
            throw new AppException(['签名错误', 'API_SIGN_ERROR']);
        }
    }

    /**
     * @desc   makeSign 生成签名
     * @author chenmingming
     * @return string
     */
    private function makeSign()
    {
        $data = $this->_params;
        ksort($data);
        $signstr = '';
        foreach ($data as $k => $v) {
            $k != $this->options[self::OPTION_SIGN_KEY]
            &&
            $signstr .= $k . '=' . $v . '&';
        }
        $signstr .= 'key=' . $this->options[self::OPTION_SIGN_SECRET];

        $sign = strtoupper(substr(md5($signstr), 3, 24));
        if ($this->options[self::OPTION_DEBUG]) {
            $this->_return['debug']['signstr']  = $signstr;
            $this->_return['debug']['truesign'] = $sign;
        }

        return $sign;
    }

    /**
     * formatParams 解析参数
     *
     * @author chenmingming
     * @throws ApiException
     */
    private function parseParams()
    {
        if ($this->options[self::OPTION_IS_GLOBAL]) {
            $params = array_merge($this->options[self::OPTION_GLOBAL_PARAMS], $this->options[self::OPTION_PARAMS]);
        } else {
            $params = $this->options[self::OPTION_PARAMS];
        }
        foreach ($params as $rule) {
            //无索引方式
            list($field_name, $field_type, $is_require, $default, $method) = $rule;
            if (!preg_match('/^[a-zA-Z][a-zA-Z_0-9]*$/', $field_name)) {
                throw new AppException(['接口参数名称非法', 'API_PARAMS_INVALID']);
            }
            if ($this->setParamValue($field_name, $method) === false) {
                //该参数无值传递
                if ($is_require) {
                    throw new AppException(["参数{$field_name}必须传递，且不能为空 方式:{$method}", 'API_PARAM_MUST']);
                }
                is_null($default) || $this->$field_name = $default;
            } else {
                $this->formatParamValue($field_name, $field_type);
            }

            //验证参数合法性
            $method = 'set' . ucfirst($field_name);
            method_exists($this, $method) && $this->$method($this->$field_name);
        }
    }

    /**
     * @desc   validateParams
     * @author chenmingming
     */
    final private function validateParams()
    {
        foreach ($this->options[self::OPTION_VALIDATE] as $validate) {
            list($filed, $value, $error, $type) = $validate;

            switch ($type) {
                case self::VALIDATE_TYPE_COMMON:
                    if (!$value || !is_string($value)) {
                        throw new AppException(["$filed: 验证规则必须为一个字符串", 'VALIDATE_VALUE_MUSTBE_STRING']);
                    }
                    if (method_exists(Validate::class, $value) && Validate::$value($this->$filed) !== true) {
                        if ($error) {
                            throw new AppException($error);
                        } else {
                            throw new AppException(["$filed:{$this->$filed} 简单验证不合法", 'VALIDATE_COMMON_INVALID']);
                        }
                    }
                    break;
                case self::VALIDATE_TYPE_IN:
                    if (!$value || !is_array($value)) {
                        throw new AppException(["$filed:验证规则必须为一个数组", 'VALIDATE_VALUE_MUSTBE_ARRAY']);
                    }
                    if (!in_array($this->$filed, $value)) {
                        if ($error) {
                            throw new AppException($error);
                        } else {
                            throw new AppException(["$filed 不在合法范围内...", 'VALIDATE_NOT_IN_VALID']);
                        }
                    }
                    break;
                case self::VALIDATE_TYPE_REG:
                    if (!$value || !is_string($value)) {
                        throw new AppException(["$filed:验证规则必须为一个字符串", 'VALIDATE_VALUE_MUSTBE_STRING']);
                    }
                    if (!preg_match($value, $this->$filed)) {
                        if ($error) {
                            throw new AppException($error);
                        } else {
                            throw new AppException(["$filed 正则验证失败...", 'VALIDATE_REG_INVALID']);
                        }
                    }
            }
        }
    }

    /**
     * @desc   formatParamValue 格式化数据
     * @author chenmingming
     *
     * @param string $filed_name 字段名称
     * @param string $type       字段类型
     *
     * @throws AppException
     */
    final private function formatParamValue($filed_name, $type)
    {
        switch ($type) {
            case self::PARAM_STRING:
                $this->$filed_name = (string)$this->$filed_name;

                break;
            case self::PARAM_INT:
                if ($this->$filed_name != (string)(int)$this->$filed_name) {
                    throw new AppException(["参数{$filed_name}非整数", 'APIPARAM_NOT_INT']);
                }
                $this->$filed_name = (int)$this->$filed_name;
                break;
            case self::PARAM_FLOAT:
                if ($this->$filed_name != (string)(float)$this->$filed_name) {
                    throw new AppException(["参数{$filed_name}非整数", 'APIPARAM_NOT_INT']);
                }
                $this->$filed_name = (float)$this->$filed_name;
                break;
            case self::PARAM_JSON:
                json_decode($this->$filed_name);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new AppException(["参数{$filed_name}非JSON字符串", 'APIPARAM_NOT_JSON']);
                }
                break;
            default:
                throw new AppException(["参数{$filed_name}类型未指定", 'APIPARAM_INVALID']);
        }
    }

    /**
     * @desc   setValue
     * @author chenmingming
     *
     * @param $field_name
     * @param $method
     *
     * @return bool
     */
    final private function setParamValue($field_name, $method)
    {
        switch ($method) {
            case self::PARAM_METHOD_REQUEST:
                if (!isset($_REQUEST[$field_name])) {
                    return false;
                }
                $this->$field_name = $_REQUEST[$field_name];
                break;
            case self::PARAM_METHOD_POST:
                if (!isset($_POST[$field_name])) {
                    return false;
                }
                $this->$field_name = $_POST[$field_name];
                break;
            case self::PARAM_METHOD_GET:
                if (!isset($_GET[$field_name])) {
                    return false;
                }
                $this->$field_name = $_GET[$field_name];
                break;
            case self::PARAM_METHOD_COOKIE:
                if (!isset($_COOKIE[$field_name])) {
                    return false;
                }
                $this->$field_name = $_COOKIE[$field_name];
                break;
            default:
                return false;
        }
        if (is_null($this->$field_name)) {
            return false;
        }
        $this->_params[$field_name] = $this->$field_name;
    }

    /**
     * @desc   setCode
     * @author chenmingming
     *
     * @param string $code 错误码
     *
     * @return $this
     */
    final protected function setCode($code)
    {
        $this->_return['code'] = (string)$code;

        return $this;
    }

    /**
     * @desc   setMsg 设置返回消息
     * @author chenmingming
     *
     * @param string $msg 返回消息
     *
     * @return $this
     */
    final protected function setMsg($msg)
    {
        $this->_return['msg'] = (string)$msg;

        return $this;
    }

    /**
     * @desc   set
     * @author chenmingming
     *
     * @param string $key   key
     * @param string $value 值
     *
     * @return $this
     */
    final protected function setData($key, $value = null)
    {
        if (is_null($value)) {
            $this->_return['data'] = $this->format2AllString($key);;
        } else {
            $this->_return['data'][$key] = $this->format2AllString($value);
        }

        return $this;
    }

    /**
     * @desc   setMinId
     * @author chenmingming
     *
     * @param string $min_id 最小id
     *
     * @return $this
     */
    final protected function setMinId($min_id)
    {
        $this->_return['min_id'] = (string)$min_id;

        return $this;
    }

    /**
     * @desc   setIsEnd
     * @author chenmingming
     *
     * @param bool $is_end 是否结束
     *
     * @return $this
     */
    final protected function setIsEnd($is_end)
    {
        $this->_return['is_end'] = $is_end == true;

        return $this;
    }

    /**
     * @desc   format2AllString
     * @author chenmingming
     *
     * @param $value
     *
     * @return mixed
     */
    private function format2AllString($value)
    {
        if (!$this->options[self::OPTION_OUTPUT_ALL_STRING]) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->format2AllString($v);
            }
        } elseif (!is_bool($value)) {
            return (string)$value;
        }

        return $value;
    }

    /**
     * @desc   _beforeOutput 输出数据之前的拦截器
     * @author chenmingming
     */
    private function _beforeOutput()
    {
        $this->options[self::OPTION_DEBUG] && $this->calcost();
    }

    /**
     * @desc   output
     * @author chenmingming
     *
     * @param bool $background 是否后台继续执行
     */
    final protected function output()
    {
        $this->_beforeOutput();
        $data_new = json_encode($this->_return, JSON_UNESCAPED_UNICODE);
        if (!headers_sent()) {
            header("Content-type:application/json;charset=utf-8");
            header('Content-Length: ' . strlen($data_new));
        }
        ob_end_clean();
        echo $data_new;
        fastcgi_finish_request();
        $this->_afterRequest();
        exit();
    }

    /**
     * @desc   afterRequest 自定义结束拦截器
     * @author chenmingming
     */
    protected function afterRequest()
    {

    }

    /**
     * @desc   _afterRequest 全局拦截器
     * @author chenmingming
     */
    private function _afterRequest()
    {
        //接口自定义结束后拦截器
        method_exists($this, 'afterRequest') && $this->afterRequest();
    }

    /**
     * @desc    计算该接口时间消耗和内存消耗
     * @author  陈明明 mailto:838965806@qq.com
     * @since   2015-08-07 13:02:56
     */
    private function calcost()
    {
        $this->_return['debug'] = array_merge($this->_return['debug'],
            [
                'cost_time' => sprintf('%.6f', microtime(true) - $this->_start_time),
                'cost_mem'  => sprintf('%.6f', memory_get_usage(true) / 1048576),
                'sql_count' => DB::$sql_count,
            ]
        );
    }
}