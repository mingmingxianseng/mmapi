<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace mmapi\core;

class Response
{

    // 原始数据
    protected $data = [];

    //状态
    protected $code = 200;

    // 输出参数
    protected $options = [
        'json_encode_param' => JSON_UNESCAPED_UNICODE,
    ];
    // header参数
    protected $header = [];

    protected $content = null;
    static protected $instance;

    /**
     * 架构函数
     *
     * @access   public
     *
     */
    public function __construct()
    {
        $this->header  = Config::get('response.header');
        $options       = Config::get('response.options', []);
        $this->options = array_merge($this->options, is_array($options) ? $options : []);
        $this->set('code', Config::get('response.default_code', 'SUCCESS'));
        $this->set('msg', Config::get('response.default_msg', 'SUCCESS'));
    }

    /**
     * 创建Response对象
     *
     * @access public
     *
     *
     * @return Response
     */
    public static function create()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @desc   error 错误输出
     * @author chenmingming
     *
     * @param string     $errno 错误码
     * @param string     $msg   错误描述
     * @param array|null $data  错误详情
     */
    public function error($errno, $msg, $data = null)
    {
        $this->set('code', strtoupper($errno))
            ->set('msg', $msg)
            ->set('data', $data)
            ->send();
    }

    /**
     * 发送数据到客户端
     *
     * @access public
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function send()
    {
        if (!headers_sent() && !empty($this->header)) {
            // 发送状态码
            http_response_code($this->code);
            $this->header('Content-Type', $this->options['content_type']);
            // 发送头部信息
            foreach ($this->header as $name => $val) {
                header($name . ':' . $val);
            }
        }
        // 处理输出数据
        echo $this->getContent();

        if (function_exists('fastcgi_finish_request')) {
            // 提高页面响应
            fastcgi_finish_request();
        }
    }

    /**
     * 输出的参数
     *
     * @access public
     *
     * @param mixed $options 输出参数
     *
     * @return $this
     */
    public function options($options = [])
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * 输出数据设置
     *
     * @access public
     *
     * @param string $key            key
     * @param mixed  $value          值
     * @param bool   $formatToString 是否格式化成字符串
     *
     * @return $this
     */
    public function set($key, $value, $formatToString = false)
    {
        if (is_null($value)) {
            unset($this->data[$key]);
        } else {
            $this->data[$key] = $formatToString ? $this->formatToString($value) : $value;
        }

        return $this;
    }

    /**
     * @desc   formatToString 所有类型数据格式化成字符串
     * @author chenmingming
     *
     * @param mixed $value 待格式化数据
     *
     * @return array|string
     */
    private function formatToString($value)
    {
        if (is_array($value)) {
            $tmp = [];
            foreach ($value as $k => $v) {
                $tmp[$k] = $this->formatToString($v);
            }

            return $tmp;
        } elseif (!is_bool($value)) {
            return (string)$value;
        }

        return $value;
    }

    /**
     * 设置响应头
     *
     * @access public
     *
     * @param string|array $name  参数名
     * @param string       $value 参数值
     *
     * @return $this
     */
    public function header($name, $value = null)
    {
        if (is_array($name)) {
            $this->header = array_merge($this->header, $name);
        } else {
            $this->header[$name] = $value;
        }

        return $this;
    }

    /**
     * 发送HTTP状态
     *
     * @param integer $code 状态码
     *
     * @return $this
     */
    public function code($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * LastModified
     *
     * @param string $time
     *
     * @return $this
     */
    public function lastModified($time)
    {
        $this->header['Last-Modified'] = $time;

        return $this;
    }

    /**
     * Expires
     *
     * @param string $time
     *
     * @return $this
     */
    public function expires($time)
    {
        $this->header['Expires'] = $time;

        return $this;
    }

    /**
     * ETag
     *
     * @param string $eTag
     *
     * @return $this
     */
    public function eTag($eTag)
    {
        $this->header['ETag'] = $eTag;

        return $this;
    }

    /**
     * 页面缓存控制
     *
     * @param string $cache 状态码
     *
     * @return $this
     */
    public function cacheControl($cache)
    {
        $this->header['Cache-control'] = $cache;

        return $this;
    }

    /**
     * 页面输出类型
     *
     * @param string $contentType 输出类型
     * @param string $charset     输出编码
     *
     * @return $this
     */
    public function contentType($contentType, $charset = 'utf-8')
    {
        $this->header['Content-Type'] = $contentType . '; charset=' . $charset;

        return $this;
    }

    /**
     * 获取头部信息
     *
     * @param string $name 头部名称
     *
     * @return mixed
     */
    public function getHeader($name = '')
    {
        return !empty($name) ? $this->header[$name] : $this->header;
    }

    /**
     * 获取原始数据
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取输出数据
     *
     * @return string
     */
    public function getContent()
    {
        if (null == $this->content) {
            try {
                // 返回JSON数据格式到客户端 包含状态信息
                $this->content = json_encode($this->data, $this->options['json_encode_param']);

                if ($this->content === false) {
                    throw new \InvalidArgumentException(json_last_error_msg());
                }

            } catch (\Exception $e) {
                if ($e->getPrevious()) {
                    throw $e->getPrevious();
                }
                throw $e;
            }
        }

        return $this->content;
    }

    /**
     * @desc   setContent
     * @author chenmingming
     *
     * @param string $string 设置输出内容
     *
     * @return $this
     */
    public function setContent($string)
    {
        $this->content = $string;

        return $this;
    }

    /**
     * 获取状态码
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

}
