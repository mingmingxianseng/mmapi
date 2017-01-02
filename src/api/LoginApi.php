<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/2
 * Time: 20:35
 */

namespace mmapi\api;

use mmapi\core\Api;

abstract class LoginApi extends Api
{
    //是否需要登录的标志
    const OPT_VERIFY_LOGGED = 'is_check_login';


    public function __construct()
    {
        //默认需要验证登录
        $this->option(self::OPT_VERIFY_LOGGED, true);
        parent::__construct();
        $this->options[self::OPT_VERIFY_LOGGED] && $this->checkLogin();
    }

    /**
     * @desc   withoutRequireLogin 设置该接口无需登录
     * @author chenmingming
     */
    public function withoutRequireLogged()
    {
        $this->option(self::OPT_VERIFY_LOGGED, false);
    }

    /**
     * @desc   checkLogin
     * @author chenmingming
     */
    abstract public function checkLogin();

}