<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/11
 * Time: 00:16
 */

namespace mmapi\wechat\core;

use mmapi\wechat\Wechat;

class User
{
    protected $openid;
    protected $nickname;
    protected $sex;
    protected $province;
    protected $city;
    protected $country;
    protected $headimgurl;
    protected $privilege;
    protected $unionid;
    /** @var  Wechat $wechat */
    protected $wechat;

    public function __construct($data, Wechat $wechat)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
        $this->wechat = $wechat;
    }

    /**
     * @return string
     */
    public function getOpenid()
    {
        return $this->openid;
    }

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getHeadimgurl()
    {
        return $this->headimgurl;
    }

    /**
     * @return array
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @return string
     */
    public function getUnionid()
    {
        return $this->unionid;
    }

    /**
     * @return Wechat
     */
    public function getWechat()
    {
        return $this->wechat;
    }
    
    
}