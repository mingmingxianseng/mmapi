<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/10
 * Time: 21:40
 */

namespace mmapi\wechat\core;

interface Cache
{
    /**
     * @desc   set 设置缓存
     * @author chenmingming
     *
     * @param string $key    key
     * @param mixed  $value  值
     * @param int    $expire 生存时间 单位秒
     *
     * @return mixed
     */
    public function set($key, $value, $expire);

    /**
     * @desc   get 获取缓存
     * @author chenmingming
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @desc   del 删除缓存
     * @author chenmingming
     *
     * @param string $key 缓存key
     *
     * @return bool
     */
    public function del($key);
}