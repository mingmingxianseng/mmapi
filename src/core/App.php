<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/12
 * Time: 11:22
 */
namespace mmapi\core;

class  App
{
    /**
     * @desc   start 开始一个项目
     * @author chenmingming
     * @throws AppException
     */
    static public function start()
    {
        set_exception_handler('mmapi\core\App::handleException');
        register_shutdown_function('mmapi\core\App::fatalError');
        set_error_handler('mmapi\core\App::appError');

        define('DEBUG', Config::get('debug') == true);
        define('REQUEST_ID', uniqid());

        error_reporting(Config::get('error_reportiong', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING));
        ini_set('display_errors', false);

        if (!Config::get('vpath')) {
            throw new AppException("VPATH can't be empty!", 'VPATH_EMPTY');
        }
        define('VPATH', Config::get('vpath'));

        self::init();
        Dispatcher::dispatch();
    }

    /**
     * @desc   init 初始化
     * @author chenmingming
     */
    static private function init()
    {
        //定义是否AJAX请求
        define('IS_AJAX',
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) and
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        );
        define('IS_CLI', PHP_SAPI == 'cli');
        IS_CLI ? define('__URL__', 'cli') : define('__URL__',
            (isset($_SERVER['HTTPS']) ? "https://" : "http://")
            . ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_ADDR'])
            . $_SERVER['REQUEST_URI']
        );
        //加载默认配置文件
        $default_conf_path = dirname(__DIR__) . '/convention.php';
        is_file($default_conf_path) && Config::batchSet(include $default_conf_path);
        //加载配置文件
        $conf_path = Config::get('conf_path', VPATH . '/conf');
        define('CONF_PATH', $conf_path);

        $conf_file = Config::get('conf_file', 'conf.php');
        $conf_file = CONF_PATH . '/' . $conf_file;
        is_file($conf_file) and Config::batchSet(include $conf_file);

        $file_load = ' [File loaded：' . count(get_included_files()) . ']';
        $server    = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
        $remote    = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $method    = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI';

        $info = __URL__ . "\t{$server}\t{$remote}\t{$method}\t$file_load";
        Log::write($info);
    }

    /**
     * 自定义异常处理
     *
     * @access public
     *
     * @param \Exception $e 异常对象
     */
    static public function handleException($e)
    {
        if ($e instanceof AppException) {
            Response::create()->error($e->getErrno(), $e->getMessage(), $e->getDetail());
        } else {
            if (DEBUG) {
                Response::create()->error('undefined_exception', $e->getMessage(), $e->getTrace());
            } else {
                Response::create()->error(Config::get('response.default_errno'), Config::get('response.default_errmsg'));
            }

        }
    }

    // 致命错误捕获
    static public function fatalError()
    {
        if ($e = error_get_last()) {
            Response::create()->error('fatal_error', $e['message'], $e);
        }
        Log::save();
    }

    /**
     * 自定义错误处理
     *
     * @access public
     *
     * @param int    $errno   错误类型
     * @param string $errstr  错误信息
     * @param string $errfile 错误文件
     * @param int    $errline 错误行数
     *
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline)
    {
        Log::error("[{$errno}] $errstr \r\n #{$errfile} +{$errline}");
    }

}