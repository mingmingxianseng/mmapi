<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/12
 * Time: 21:01
 */

namespace mmxs\mmapi\tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Setup;
use GuzzleHttp\Psr7\Request;
use mmapi\core\App;
use mmapi\core\AppException;
use mmapi\core\Cache;
use mmapi\core\Config;
use mmapi\core\Db;
use mmapi\core\Log;
use mmapi\entity\PmsStore;

class appTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        Config::set('VPATH', dirname(__DIR__));
        var_dump(Config::get('vpath'));
        App::start();
        var_dump(VPATH);
    }

    public function test2()
    {
        Config::batchSet([
            'cache' => [
                'type'    => 'complex',
                'default' => [
                    'type'          => "file",
                    'expire'        => 0,
                    'cache_subdir'  => false,
                    'prefix'        => '',
                    'path'          => '',
                    'data_compress' => false,
                ],

            ],
        ]);
        Cache::set('test', '123456');

        var_dump(Cache::get('test'));
    }

    public function test3()
    {
        Config::batchSet([
            'db' => [
                'driver'   => 'pdo_mysql',
                'dbname'   => 'anxin_oa',
                'host'     => '121.40.248.146',
                'user'     => 'anxin_oa',
                'password' => 'ff42864b5cc4d229',
            ],
        ]);
        require_once __DIR__ . '/Entities/test.php';
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $isDevMode = true;
        $config    = Setup::createAnnotationMetadataConfiguration([__DIR__ . "/Entities"], $isDevMode);
        $config->setSQLLogger(new \Logging());
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
        $conn = Config::get('db');
        require_once __DIR__ . '/Entities/admin_master.php';
// obtaining the entity manager

        $entityManager = EntityManager::create($conn, $config);

//        echo $rs;
        $rs    = $entityManager->find(\admin_master::class, '2')->getId();
        $qb    = $entityManager->createQueryBuilder();
        $query = $qb->select('a')->from(\admin_master::class, 'a')->getQuery();

        $rs = $query
            ->setMaxResults(1)
            ->getResult();
//        var_dump($rs);
        var_dump($query->getSQL());

////
//        $query = $entityManager->createQuery($rs);
//        $bug   = $query->getSingleResult();
//
//        var_dump(11111);
//        var_dump($bug);
    }

    public function testLog()
    {
        Config::set('VPATH', dirname(__DIR__));
        App::start();
        Config::batchSet([
            'log' => [
                'time_format' => ' c ',
                'file_size'   => 2097152,
                'filepath'    => __DIR__ . '/log',
                'apart_level' => [],
                'level'       => ['log', 'error', 'info', 'sql', 'notice', 'alert'],
                'suffix'      => REQUEST_ID . "\t" . __URL__,
            ],
        ]);
        Log::write('1221312312', Log::ALERT);
        Log::write('1221312312444545', Log::ALERT);
        Log::write('1221312312222233123', Log::ALERT);
    }

    public function testNavtiveQuery()
    {
        Config::batchSet([
            'db' => [
                'driver'   => 'pdo_mysql',
                'dbname'   => 'anxin_oa',
                'host'     => '121.40.248.146',
                'user'     => 'anxin_oa',
                'password' => 'ff42864b5cc4d229',
            ],
        ]);
        require_once dirname(__DIR__) . '/entities/mmapi/entity/PmsStore.php';
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $isDevMode = true;
        $config    = Setup::createXMLMetadataConfiguration([dirname(__DIR__) . "/project/xml"], $isDevMode);
        $conn      = Config::get('db');
        // obtaining the entity manager

        $entityManager = EntityManager::create($conn, $config);
        $rs            = $entityManager->createQueryBuilder()->select('s')->from(PmsStore::class, 's')->getQuery()->setMaxResults(1)->getArrayResult();
        var_dump($rs);
//        $rsm = new ResultSetMapping();
//        $rsm->addEntityResult(\PmsStore::class, 'p')
//            ->addFieldResult('p', 'id', 'id');
//
//        $query = $entityManager->createNativeQuery('SELECT id FROM pms_store WHERE id=?', $rsm);
//        $query->setParameter(1, '1');
//        var_dump($query->getResult());
    }

    public function testDb(){

        Config::set('VPATH', dirname(__DIR__));
        App::start();
    }
}
