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
    /** @var  Memcached */
    protected $memcached;

    public function __construct(Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

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
        $stats   = $this->memcached->getStats();
        $servers = $this->memcached->getServerList();
        $key     = $servers[0]['host'] . ':' . $servers[0]['port'];
        $stats   = $stats[$key];

        return [
            \Doctrine\Common\Cache\Cache::STATS_HITS             => $stats['get_hits'],
            \Doctrine\Common\Cache\Cache::STATS_MISSES           => $stats['get_misses'],
            \Doctrine\Common\Cache\Cache::STATS_UPTIME           => $stats['uptime'],
            \Doctrine\Common\Cache\Cache::STATS_MEMORY_USAGE     => $stats['bytes'],
            \Doctrine\Common\Cache\Cache::STATS_MEMORY_AVAILABLE => $stats['limit_maxbytes'],
        ];
    }
}