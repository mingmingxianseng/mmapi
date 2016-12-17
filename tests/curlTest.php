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
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use mmapi\core\App;
use mmapi\core\AppException;
use mmapi\core\Cache;
use mmapi\core\Config;
use mmapi\core\Db;
use mmapi\core\Log;
use mmapi\entity\PmsStore;
use Symfony\Component\Config\Definition\Exception\Exception;

class curlTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $client = new Client();
        try {
            $reponse = $client->get('http://admin.xzb.chenmm.cn/common/uploadimg');
            var_dump($reponse->getBody()->getContents());
        } catch (Exception $e) {
            var_dump(get_class($e));
        }
    }

    public function test1(){

    }

}
