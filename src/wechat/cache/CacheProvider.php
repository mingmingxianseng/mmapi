<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/10
 * Time: 21:45
 */

namespace mmapi\wechat\cache;

use mmapi\wechat\core\Cache;

class CacheProvider implements Cache
{
    public function set($key, $value, $expire)
    {
        return \mmapi\core\Cache::set($key, $value, $expire);
    }

    public function get($key)
    {
        return \mmapi\core\Cache::get($key);
    }

    public function del($key)
    {
        return \mmapi\core\Cache::rm($key);
    }

}