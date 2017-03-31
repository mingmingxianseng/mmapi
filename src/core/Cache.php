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

use mmapi\cache\Driver;

class Cache
{
    protected static $instance = [];
    public static $readTimes = 0;
    public static $writeTimes = 0;

    /**
     * 操作句柄
     *
     * @var Driver
     * @access protected
     */
    protected static $handler;

    /**
     * 连接缓存
     *
     * @access public
     *
     * @param array       $options 配置数组
     * @param bool|string $name    缓存连接标识 true 强制重新连接
     *
     * @return \mmapi\cache\Driver
     */
    public static function connect(array $options = [], $name = false)
    {
        $type = !empty($options['type']) ? $options['type'] : 'File';
        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $class = false !== strpos($type, '\\') ? $type : '\\mmapi\\cache\\driver\\' . ucwords($type);

            // 记录初始化信息
            if (true === $name) {
                return new $class($options);
            } else {
                self::$instance[$name] = new $class($options);
            }
        }
        self::$handler = self::$instance[$name];

        return self::$handler;
    }

    /**
     * 自动初始化缓存
     *
     * @access public
     *
     * @param array $options 配置数组
     *
     * @return void
     */
    public static function init(array $options = [])
    {
        if (is_null(self::$handler)) {
            // 自动初始化缓存
            if (!empty($options)) {
                self::connect($options);
            } elseif ('complex' == Config::get('cache.type')) {
                self::connect(Config::get('cache.default'));
            } else {
                self::connect(Config::get('cache'));
            }
        }
    }

    /**
     * 切换缓存类型 需要配置 cache.type 为 complex
     *
     * @access public
     *
     * @param string $name 缓存标识
     *
     * @return \mmapi\cache\Driver
     */
    public static function store($name = 'default')
    {
        if ('complex' == Config::get('cache.type')) {
            self::connect(Config::get('cache.' . $name), strtolower($name));
        }

        return self::$handler;
    }

    /**
     * 判断缓存是否存在
     *
     * @access public
     *
     * @param string $name 缓存变量名
     *
     * @return bool
     */
    public static function has($name)
    {
        self::init();
        self::$readTimes++;

        return self::$handler->has($name);
    }

    /**
     * 读取缓存
     *
     * @access public
     *
     * @param string $name    缓存标识
     * @param mixed  $default 默认值
     *
     * @return mixed
     */
    public static function get($name, $default = false)
    {
        self::init();
        self::$readTimes++;

        return self::$handler->get($name, $default);
    }

    /**
     * 写入缓存
     *
     * @access public
     *
     * @param string   $name   缓存标识
     * @param mixed    $value  存储数据
     * @param int|null $expire 有效时间 0为永久
     *
     * @return boolean
     */
    public static function set($name, $value, $expire = null)
    {
        self::init();
        self::$writeTimes++;

        return self::$handler->set($name, $value, $expire);
    }

    /**
     * 自增缓存（针对数值缓存）
     *
     * @access public
     *
     * @param string $name   缓存变量名
     * @param int    $step   步长
     * @param int    $expire 过期时间
     *
     * @return false|int
     */
    public static function inc($name, $step = 1, $expire = null)
    {
        self::init();
        self::$writeTimes++;

        return self::$handler->inc($name, $step, $expire);
    }

    /**
     * 自减缓存（针对数值缓存）
     *
     * @access public
     *
     * @param string $name 缓存变量名
     * @param int    $step 步长
     *
     * @return false|int
     */
    public static function dec($name, $step = 1)
    {
        self::init();
        self::$writeTimes++;

        return self::$handler->dec($name, $step);
    }

    /**
     * 删除缓存
     *
     * @access public
     *
     * @param string $name 缓存标识
     *
     * @return boolean
     */
    public static function rm($name)
    {
        self::init();
        self::$writeTimes++;

        return self::$handler->rm($name);
    }

    /**
     * 清除缓存
     *
     * @access public
     *
     * @param string $tag 标签名
     *
     * @return boolean
     */
    public static function clear($tag = null)
    {
        self::init();
        self::$writeTimes++;

        return self::$handler->clear($tag);
    }

    /**
     * 缓存标签
     *
     * @access public
     *
     * @param string       $name    标签名
     * @param string|array $keys    缓存标识
     * @param bool         $overlay 是否覆盖
     *
     * @return \mmapi\cache\Driver
     */
    public static function tag($name, $keys = null, $overlay = false)
    {
        self::init();

        return self::$handler->tag($name, $keys, $overlay);
    }

    /**
     * @desc   add 等价于set 但是如果key已经存在 则返回false
     * @author chenmingming
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expire
     *
     * @return bool
     */
    public static function add(string $key, $value, int $expire = 0)
    {
        self::init();

        return self::$handler->add($key, $value, $expire);
    }
}
