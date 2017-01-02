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
}