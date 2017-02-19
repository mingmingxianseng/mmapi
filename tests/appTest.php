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
    public function setUp()
    {
        parent::setUp();
        Config::set('VPATH', __DIR__);
    }

    public function test()
    {
        App::start();
        $this->assertEquals(VPATH, Config::get('vpath'));
        $this->assertEquals('1234', Config::get('aaa'));
    }

    public function testHostConf()
    {
        $_SERVER['HTTP_HOST'] = 'www.test.com';
        Config::set('host_conf', ['/^.+.test.com$/' => 'host.php']);
        App::start();
        $this->assertEquals('12345', Config::get('aaa'));

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

    public function testConfig()
    {

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

    public function testDb()
    {

        Config::set('VPATH', dirname(__DIR__));
        App::start();
    }
}
