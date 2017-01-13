<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/10
 * Time: 21:48
 */

namespace mmapi\wechat\log;


use mmapi\wechat\core\Log;

class LogProvider implements Log
{
    public function log($msg, $label)
    {
        \mmapi\core\Log::write("[{$label}]\t" . var_export($msg, true));
    }

}