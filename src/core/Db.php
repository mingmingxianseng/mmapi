<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/13
 * Time: 14:47
 */

namespace mmapi\core;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use mmapi\cache\DbCache;

class Db
{
    static private $instances;

    private $options = [];
    //该数据库配置名称 唯一
    private $db_name;

    /** @var  EntityManager */
    private $entityManager;

    /**
     * Db constructor.
     *
     * @param array $options 数据库配置
     *
     * @throws AppException
     */
    public function __construct($options)
    {
        $this->options = $options;
        $this->db_name = isset($options['name']) ? $options['name'] : md5(serialize($options));

        $memcache = null;
        if (isset($this->options['no_cache'])) {
            $cache = Cache::store();
            $cache && $memcache = new DbCache($cache->handler());
        }

        $config = Setup::createConfiguration($this->options['is_dev_mode'] == true, null, $memcache);
        $config->setMetadataDriverImpl(new XmlDriver($this->options['path']));
        $config->setSQLLogger(new SqlLog());
        try {
            $this->entityManager = EntityManager::create($this->options['conn'], $config);
        } catch (\Exception $e) {
            throw new AppException("创建DB实例失败，请检查实例", 'DB_CREATE_FAILED', $this->options);
        }
        self::$instances[$this->db_name] = $this;
    }

    /**
     * @desc   create
     * @author chenmingming
     *
     * @param string $name db配置名称
     *
     * @return Db
     * @throws AppException
     */
    static public function create($name = 'default')
    {
        if (!isset(self::$instances[$name])) {
            $conf         = Config::get('db.' . $name);
            $conf['name'] = $name;
            new self($conf);
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
    public function save($object)
    {
        $this->entityManager->persist($object);
        try {
            $this->entityManager->flush();
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
    public function remove($object)
    {
        $this->entityManager->remove($object);
        try {
            $this->entityManager->flush();
        } catch (DriverException $e) {
            $msg = DEBUG ? '删除数据失败' : $e->getMessage();
            throw new AppException($msg, "SQL_" . $e->getErrorCode(), $e->getTrace());

        } catch (DBALException $e) {
            $msg = DEBUG ? '删除数据失败' : $e->getMessage();
            throw new AppException($msg, "SQL_ERROR", $e->getTrace());
        }
    }

    /**
     * @desc   qb
     * @author chenmingming
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function dqlBuilder()
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @desc   sqlBuilder
     * @author chenmingming
     * @return QueryBuilder
     */
    public function sqlBuilder()
    {
        return new QueryBuilder($this, $this->options['queryBuilder']);
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
    public function find($entityName, $id, $lockMode = null, $lockVersion = null)
    {
        return $this->entityManager->find($entityName, $id, $lockMode, $lockVersion);
    }

    /**
     * @desc   exec
     * @author chenmingming
     *
     * @param string $sql    sql
     * @param array  $params 绑定参数列表
     *
     * @return int
     */
    public function exec($sql, $params = [])
    {
        return $this->entityManager->getConnection()->executeUpdate($sql, $params);
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
    public function query($sql, $params = [])
    {
        return $this->entityManager->getConnection()->executeQuery($sql, $params);
    }

    /**
     * @desc   getLastInsertId 上一次插入的id
     * @author chenmingming
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->entityManager->getConnection()->lastInsertId();
    }

    /**
     * @desc   getEntityManager
     * @author chenmingming
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}