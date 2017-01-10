<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/4
 * Time: 14:02
 */

namespace mmxs\mmapi\tests;

use mmapi\core\AppException;
use mmapi\core\Config;
use mmapi\core\Db;
use mmapi\tool\ModelXmlBuilder;
use PHPUnit\Framework\TestCase;

class ModelXmlBuilderTest extends TestCase
{
    public function setUp()
    {

        //table test ddl
        /*
         * CREATE TABLE `test` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `add_time` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        */

        parent::setUp(); // TODO: Change the autogenerated stub
        Config::set('db', [
            'default' => [
                'is_dev_mode' => true,
                'conn'        => [
                    'driver'   => 'pdo_mysql',
                    'dbname'   => 'entity',
                    'host'     => '127.0.0.1',
                    'user'     => 'root',
                    'password' => '123456',
                ],
                'path'        => [],
            ],
        ]);
    }

    public function test()
    {
        $obj = new ModelXmlBuilder();
        $obj->setDb(Db::create())
            ->setNamespace('model\entity')
            ->setTableName('mall_goods');
        foreach (Db::create()->query('show tables')->fetchAll() as $v) {
            $table = current($v);
            try {
                $obj
                    ->setTableName($table)
                    ->builder(dirname(__DIR__) . '/model/football/');
            } catch (AppException $e) {
                echo ($e->getMessage()) . PHP_EOL;
            }
            $obj->setEntity(null);

        }
    }
}