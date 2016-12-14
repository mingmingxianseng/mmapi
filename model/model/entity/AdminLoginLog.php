<?php

namespace model\entity;

/**
 * AdminLoginLog
 */
class AdminLoginLog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $masterId;

    /**
     * @var string
     */
    private $nickName;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $ext;

    /**
     * @var \DateTime
     */
    private $addTime;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set masterId
     *
     * @param integer $masterId
     *
     * @return AdminLoginLog
     */
    public function setMasterId($masterId)
    {
        $this->masterId = $masterId;

        return $this;
    }

    /**
     * Get masterId
     *
     * @return integer
     */
    public function getMasterId()
    {
        return $this->masterId;
    }

    /**
     * Set nickName
     *
     * @param string $nickName
     *
     * @return AdminLoginLog
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * Get nickName
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return AdminLoginLog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set ext
     *
     * @param string $ext
     *
     * @return AdminLoginLog
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     *
     * @return AdminLoginLog
     */
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;

        return $this;
    }

    /**
     * Get addTime
     *
     * @return \DateTime
     */
    public function getAddTime()
    {
        return $this->addTime;
    }
}

