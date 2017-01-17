<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/2
 * Time: 20:40
 */

namespace mmapi\api;

use mmapi\core\Api;
use mmapi\core\ApiParams;

abstract class SignApi extends DenyResubmitApi
{
    const OPT_SECRET = 'secret';
    const OPT_WITHOUT_CHECK_SIGN = 'without_check_sign';

    protected $V;
    protected $F;
    protected $noncestr;
    protected $sign;
    protected $auth;

    public function __construct()
    {
        $this->options[self::OPT_WITHOUT_CHECK_SIGN] = false;
        $this->addParams(['V', 'F', 'noncestr', 'sign', 'auth']);
        parent::__construct();
        if (!$this->options[self::OPT_WITHOUT_CHECK_SIGN] && $this->makeSign() != $this->get('sign')->getValue()) {
            $this->error('签名错误', 'SIGNATURE_ERROR');
        }
    }

    /**
     * @desc   withoutLogin 无需登录
     * @author chenmingming
     */
    protected function withoutLogin()
    {
        $this->removeParam('auth');
    }

    /**
     * @desc   withoutCheckSign
     * @author chenmingming
     */
    protected function withoutCheckSign()
    {
        $this->options[self::OPT_WITHOUT_CHECK_SIGN] = true;
    }

    /**
     * @desc   makeSign
     * @author chenmingming
     * @return string
     */
    abstract protected function makeSign();
}