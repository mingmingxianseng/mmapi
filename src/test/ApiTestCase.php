<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/6/19
 * Time: 18:38
 */

namespace mmapi\test;

use mmapi\core\Api;
use mmapi\core\Response;

class ApiTestCase extends TestCase
{
    /**
     * @desc   tearDown
     * @author chenmingming
     */
    public function tearDown()
    {
        parent::tearDown();
        Response::create()->setContent(null);
    }

    /**
     * @desc   getResponse
     * @author chenmingming
     *
     * @param Api $api
     *
     * @return Response
     */
    protected function getResponse(Api $api)
    {
        $api->main();

        return $api->getResponse();
    }
}