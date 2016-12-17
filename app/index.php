<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 16:47
 */

namespace app;

use mmapi\core\Api;

class index extends Api
{
    protected function init()
    {
        // TODO: Implement init() method.
    }

    public function run()
    {
        $this
            ->set('data', [1, 23]);
    }

}