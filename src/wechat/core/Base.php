<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/12
 * Time: 15:40
 */

namespace mmapi\wechat\core;

use mmapi\wechat\Wechat;

class Base
{
    /** @var  Wechat */
    protected $wechat;

    public function __construct(Wechat $wechat)
    {
        $this->wechat = $wechat;
    }

    /**
     * @return Wechat
     */
    public function getWechat()
    {
        return $this->wechat;
    }
}