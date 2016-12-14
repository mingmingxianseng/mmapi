<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 16:16
 */

namespace app\common;

use mmapi\core\Api;
use mmapi\core\ApiParams;

class test extends Api
{
    protected $id;

    protected function init()
    {
        ApiParams::create('id')->setRequire(true)->add($this);
    }

    public function run()
    {
        $this->set('data', $this->id)
            ->send();
    }

}