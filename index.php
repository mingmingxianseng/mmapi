<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/12
 * Time: 11:22
 */
namespace mmapi;

use mmapi\core\App;
use mmapi\core\Config;

require_once './vendor/autoload.php';
Config::set('vpath', __DIR__);
Config::set('debug', true);
App::start();