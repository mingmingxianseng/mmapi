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
use Doctrine\ORM\Mapping\AnsiQuoteStrategy;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use mmapi\cache\DbCache;

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
            $conf     = Config::get('db.' . $name);
            $memcache = new DbCache();
            $config   = Setup::createConfiguration($conf['is_dev_mode'] == true, null, $memcache);
            $config->setMetadataDriverImpl(new XmlDriver($conf['path']));

            $config->setSQLLogger(new SqlLog());
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
            $msg = DEBUG ? $e->getMessage() : '更新数据失败';
            throw new AppException($msg, "SQL_" . $e->getErrorCode(), $e->getTrace());

        } catch (DBALException $e) {
            $msg = DEBUG ? $e->getMessage() : '更新数据失败';
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

    /**
     * Finds an Entity by its identifier.
     *
     * @param string       $entityName  The class name of the entity to find.
     * @param mixed        $id          The identity of the entity to find.
     * @param integer|null $lockMode    One of the \Doctrine\DBAL\LockMode::* constants
     *                                  or NULL if no specific lock mode should be used
     *                                  during the search.
     * @param integer|null $lockVersion The version of the entity to find when using
     *                                  optimistic locking.
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     *
     */
    static public function find($entityName, $id, $lockMode = null, $lockVersion = null)
    {
        return self::create()->find($entityName, $id, $lockMode, $lockVersion);
    }

    /**
     * @desc   exec
     * @author chenmingming
     *
     * @param $sql
     *
     * @return int
     */
    static public function exec($sql)
    {
        return Db::create()->getConnection()->executeUpdate($sql);
    }

    /**
     * @desc   fetch
     * @author chenmingming
     *
     * @param string $sql    sql
     *
     * @param array  $params 参数列表
     *
     * @return \Doctrine\DBAL\Driver\Statement The executed statement.
     */
    static public function query($sql, $params = [])
    {
        return Db::create()->getConnection()->executeQuery($sql, $params);
    }

}