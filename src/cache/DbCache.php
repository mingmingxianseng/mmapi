<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/29
 * Time: 12:15
 */

namespace mmapi\cache;

use Doctrine\Common\Cache\CacheProvider;
use \Memcached;
use mmapi\core\AppException;
use mmapi\core\Cache;

class DbCache extends CacheProvider
{
    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return Cache::get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetchMultiple(array $keys)
    {
        $data = [];
        foreach ($keys as $key) {
            $tmp        = Cache::get($key);
            $data[$key] = $tmp;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSaveMultiple(array $keysAndValues, $lifetime = 0)
    {
        if ($lifetime > 30 * 24 * 3600) {
            $lifetime = time() + $lifetime;
        }
        foreach ($keysAndValues as $key => $value) {
            Cache::set($key, $value, $lifetime);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return Cache::has($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 30 * 24 * 3600) {
            $lifeTime = time() + $lifeTime;
        }

        return Cache::set($id, $data, (int)$lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return false !== Cache::rm($id);

    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return Cache::clear();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return [];
    }
}