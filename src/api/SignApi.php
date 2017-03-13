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
use mmapi\core\AppException;

abstract class SignApi extends DenyResubmitApi
{
    const OPT_WITHOUT_CHECK_SIGN = 'without_check_sign';
    const GLOBAL_PARAMS = ['V', 'F', 'noncestr', 'auth'];
    const SIGN_PARAM = 'sign';

    public function __construct()
    {
        $this->options[self::OPT_WITHOUT_CHECK_SIGN] = false;
        $this->addParams(static::GLOBAL_PARAMS);
        $this->addParam(self::SIGN_PARAM);

        parent::__construct();
    }

    /**
     * @desc   beforeRun
     * @author chenmingming
     */
    protected function beforeRun()
    {
        parent::beforeRun();
        if (!$this->options[self::OPT_WITHOUT_CHECK_SIGN] && $this->makeSign() != $this->get(static::SIGN_PARAM)->getValue()) {
            throw new ApiException('签名错误', 'SIGNATURE_ERROR');
        }
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