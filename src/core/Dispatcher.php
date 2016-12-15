<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/14
 * Time: 10:36
 */

namespace mmapi\core;

class Dispatcher
{
    /**
     * @desc   dispatch 调度
     * @author chenmingming
     */
    static public function dispatch()
    {
        defined('APP_PATH') || define('APP_PATH', Config::get('app_path'));
        $layer  = Config::get('dispatcher.layer', 's');
        $action = isset($_GET[$layer]) ? $_GET[$layer] : Config::get('dispatcher.default_action', '');

        $class = str_replace('/', '\\', $action);
        if ($namespace = Config::get('dispatcher.namespace', '')) {
            $class = $namespace . "\\" . $class;
        }
        if (!class_exists($class)) {
            throw new AppException("模块{$class}不存在", 'module_not_fund', $action);
        }
        $ajax   = new $class();
        $method = Config::get('dispatcher.method', 'main');
        if (method_exists($ajax, $method)) {
            $ajax->$method();
        } else {
            throw new AppException("模块{$class}不合法", 'module_invalid');
        }
    }
}