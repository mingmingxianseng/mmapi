<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/13
 * Time: 14:47
 */

namespace mmapi\core;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\IBMDB2\DB2Exception;
use Doctrine\DBAL\Exception\DriverException;
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

            $config = Setup::createXMLMetadataConfiguration($conf['path'], $conf['is_dev_mode'] == true, null, null);
            $config->setSQLLogger(new SqlLog());
            try {
                self::$instances[$name] = EntityManager::create($conf['conn'], $config);
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
     */
    static public function save($object)
    {
        self::create()->persist($object);
        try {
            self::create()->flush();
        } catch (DriverException $e) {
            throw new AppException($e->getMessage(), "SQL_" . $e->getErrorCode(), $e->getTrace());
        } catch (DBALException $e) {
            throw new AppException($e->getMessage(), "SQL_ERROR", $e->getTrace());
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