<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/13
 * Time: 14:47
 */

namespace mmapi\core;

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
     */
    static public function create($name = 'default')
    {
        if (!isset(self::$instances[$name])) {
            $config                 = Config::get('db.' . $name);
            $config                 = Setup::createXMLMetadataConfiguration($config['path'], $config['is_dev_mode'], null, null);
            self::$instances[$name] = EntityManager::create($config['conn'], $config);
        }

        return self::$instances[$name];
    }

    /**
     * @desc   qb
     * @author chenmingming
     * @param string $name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    static public function qb($name = 'default')
    {
        return self::create($name)->createQueryBuilder();
    }
}