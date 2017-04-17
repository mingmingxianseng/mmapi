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
        $this->persist()->flush();
    }

    /**
     * @desc   persist
     * @author chenmingming
     * @return Db
     */
    public function persist()
    {
        return Db::create(static::DB_INI)->save($this);
    }

    /**
     * @desc   remove
     * @author chenmingming
     * @return Db
     */
    public function remove()
    {
        return Db::create(static::DB_INI)->remove($this);
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