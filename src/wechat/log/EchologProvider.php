<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/10
 * Time: 22:03
 */

namespace mmapi\wechat\log;

use mmapi\wechat\core\Log;

class EchologProvider implements Log
{
    public function log($msg, $label)
    {
        echo "[$label] " . var_export($msg) . PHP_EOL;
    }

}