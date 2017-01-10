<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/21
 * Time: 21:13
 */

namespace mmapi\core;

abstract class Model
{
    const DB_INI = 'default';
    private $db;
    private $entityClass;

    /**
     * @desc   getInstance 获取一个实例
     * @author chenmingming
     *
     * @param string $id 主键id
     *
     * @return object
     */
    static public function getInstance($id)
    {
        return Db::create(static::DB_INI)->find(static::class, $id);
    }

    /**
     * @desc   getRepository
     * @author chenmingming
     * @return \Doctrine\ORM\EntityRepository
     */
    static function getRepository()
    {
        return Db::create(static::DB_INI)->getEntityManager()->getRepository(static::class);
    }

    /**
     * @desc   save 保存对象
     * @author chenmingming
     */
    public function save()
    {
        $this->getDb()->save($this);
    }

    /**
     * @desc   remove
     * @author chenmingming
     */
    public function remove()
    {
        $this->getDb()->remove($this);
    }

    /**
     * @desc   getDb
     * @author chenmingming
     * @return Db
     */
    public function getDb()
    {
        if (is_null($this->db)) {
            $class    = $this->getEntityClass();
            $this->db = Db::create($class::DB_INI);
        }

        return $this->db;
    }

    /**
     * @desc   getEntityClass 获取当前实例的类名称
     * @author chenmingming
     * @return string
     */
    public function getEntityClass()
    {
        if (is_null($this->entityClass)) {
            $this->entityClass = get_class($this);
        }

        return $this->entityClass;
    }

    /**
     * @desc   tryInstance 尝试获取一个对象
     * @author chenmingming
     *
     * @param  string                 $id 对象主键id
     * @param \Exception|array|string $e  若该对象不存在抛出的异常
     *
     * @return object
     * @throws \Exception 对象不存在抛出异常
     */
    static public function tryInstance($id, $e)
    {

        $instance = Db::create(static::DB_INI)->find(static::class, $id);
        if (is_null($instance)) {
            if ($e instanceof \Exception) {
                throw $e;
            } elseif (is_array($e)) {
                throw new AppException($e);
            } else {
                $e = $e ?: 'CLASS ' . static::class . ' NOT FUND';
                throw new AppException($e, static::class . '@NOT_FUND');
            }

        }

        return $instance;
    }
}