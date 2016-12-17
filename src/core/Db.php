<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/13
 * Time: 14:47
 */

namespace mmapi\core;

use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\IBMDB2\DB2Exception;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Db
{
    static private $instances;

    /**
     * @desc   create
     * @author chenmingming
     *
     * @param string $name db配置名称
     *
     * @return EntityManager
     * @throws AppException
     */
    static public function create($name = 'default')
    {
        if (!isset(self::$instances[$name])) {
            $conf = Config::get('db.' . $name);
            $memcache = new MemcachedCache();
            /** @var \Memcached $memcacheHandler */
            $memcacheHandler = Cache::store()->handler();
            $memcache->setMemcached($memcacheHandler);
            $config = Setup::createConfiguration($conf['is_dev_mode']==true,null, $memcache);

            $driver = new XmlDriver($conf['path']);
            $config->setMetadataDriverImpl($driver);

            $config->setSQLLogger(new SqlLog());

            $config->setMetadataCacheImpl($memcache);
            $config->setQueryCacheImpl($memcache);
            $config->setResultCacheImpl($memcache);
            try {
                self::$instances[$name] = $entityManager = EntityManager::create($conf['conn'], $config);
                $platform               = $entityManager->getConnection()
                    ->getDatabasePlatform();
                $platform->registerDoctrineTypeMapping('enum', 'string');
            } catch (\Exception $e) {
                throw new AppException("创建DB实例失败，请检查实例", 'DB_CREATE_FAILED', $conf);
            }
        }

        return self::$instances[$name];
    }

    /**
     * @desc   save 更新或者插入
     * @author chenmingming
     *
     * @param object $object 待更新或者插入的对象 entity
     *
     * @throws AppException
     */
    static public function save($object)
    {
        self::create()->persist($object);
        try {
            self::create()->flush();
        } catch (DriverException $e) {
            $msg = DEBUG ? '更新数据失败' : $e->getMessage();
            throw new AppException($msg, "SQL_" . $e->getErrorCode(), $e->getTrace());

        } catch (DBALException $e) {
            $msg = DEBUG ? '更新数据失败' : $e->getMessage();
            throw new AppException($msg, "SQL_ERROR", $e->getTrace());
        }
    }

    /**
     * @desc   remove
     * @author chenmingming
     *
     * @param $object
     *
     * @throws AppException
     */
    static public function remove($object)
    {
        self::create()->remove($object);
        try {
            self::create()->flush();
        } catch (DriverException $e) {
            $msg = DEBUG ? '更新数据失败' : $e->getMessage();
            throw new AppException($msg, "SQL_" . $e->getErrorCode(), $e->getTrace());

        } catch (DBALException $e) {
            $msg = DEBUG ? '更新数据失败' : $e->getMessage();
            throw new AppException($msg, "SQL_ERROR", $e->getTrace());
        }
    }

    /**
     * @desc   qb
     * @author chenmingming
     *
     * @param string $name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    static public function qb($name = 'default')
    {
        return self::create($name)->createQueryBuilder();
    }
}