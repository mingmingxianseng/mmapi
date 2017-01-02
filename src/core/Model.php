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
     * @desc   getInstance
     * @author chenmingming
     *
     * @param $id
     *
     * @return object
     */
    static public function getInstance($id)
    {
        $class = get_called_class();

        return Db::create($class::DB_INI)->find($class, $id);
    }

    /**
     * @desc   getRepository
     * @author chenmingming
     * @return \Doctrine\ORM\EntityRepository
     */
    static function getRepository()
    {
        $class = get_called_class();

        return Db::create($class::DB_INI)->getEntityManager()->getRepository($class);
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

    public function getEntityClass()
    {
        if (is_null($this->entityClass)) {
            $this->entityClass = get_class($this);
        }

        return $this->entityClass;
    }
}