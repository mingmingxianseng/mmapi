<?php

namespace model\entity;

/**
 * AdminMaster
 */
class AdminMaster
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $groupId = '0';

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phone = '';

    /**
     * @var string
     */
    private $userFace = '';

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $nickName;

    /**
     * @var string
     */
    private $password = '';

    /**
     * @var string
     */
    private $salt = '';

    /**
     * @var \DateTime
     */
    private $lastLoginTime;

    /**
     * @var string
     */
    private $lastLoginIp = '';

    /**
     * @var string
     */
    private $lastIpAddr = '';

    /**
     * @var string
     */
    private $guid = '';

    /**
     * @var string
     */
    private $from = '';

    /**
     * @var boolean
     */
    private $isLock = '1';

    /**
     * @var \DateTime
     */
    private $updateTime = 'CURRENT_TIMESTAMP';

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
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return AdminMaster
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return AdminMaster
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return AdminMaster
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set userFace
     *
     * @param string $userFace
     *
     * @return AdminMaster
     */
    public function setUserFace($userFace)
    {
        $this->userFace = $userFace;

        return $this;
    }

    /**
     * Get userFace
     *
     * @return string
     */
    public function getUserFace()
    {
        return $this->userFace;
    }

    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return AdminMaster
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set nickName
     *
     * @param string $nickName
     *
     * @return AdminMaster
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
     * Set password
     *
     * @param string $password
     *
     * @return AdminMaster
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return AdminMaster
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set lastLoginTime
     *
     * @param \DateTime $lastLoginTime
     *
     * @return AdminMaster
     */
    public function setLastLoginTime($lastLoginTime)
    {
        $this->lastLoginTime = $lastLoginTime;

        return $this;
    }

    /**
     * Get lastLoginTime
     *
     * @return \DateTime
     */
    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }

    /**
     * Set lastLoginIp
     *
     * @param string $lastLoginIp
     *
     * @return AdminMaster
     */
    public function setLastLoginIp($lastLoginIp)
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    /**
     * Get lastLoginIp
     *
     * @return string
     */
    public function getLastLoginIp()
    {
        return $this->lastLoginIp;
    }

    /**
     * Set lastIpAddr
     *
     * @param string $lastIpAddr
     *
     * @return AdminMaster
     */
    public function setLastIpAddr($lastIpAddr)
    {
        $this->lastIpAddr = $lastIpAddr;

        return $this;
    }

    /**
     * Get lastIpAddr
     *
     * @return string
     */
    public function getLastIpAddr()
    {
        return $this->lastIpAddr;
    }

    /**
     * Set guid
     *
     * @param string $guid
     *
     * @return AdminMaster
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set from
     *
     * @param string $from
     *
     * @return AdminMaster
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set isLock
     *
     * @param boolean $isLock
     *
     * @return AdminMaster
     */
    public function setIsLock($isLock)
    {
        $this->isLock = $isLock;

        return $this;
    }

    /**
     * Get isLock
     *
     * @return boolean
     */
    public function getIsLock()
    {
        return $this->isLock;
    }

    /**
     * Set updateTime
     *
     * @param \DateTime $updateTime
     *
     * @return AdminMaster
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;

        return $this;
    }

    /**
     * Get updateTime
     *
     * @return \DateTime
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     *
     * @return AdminMaster
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

