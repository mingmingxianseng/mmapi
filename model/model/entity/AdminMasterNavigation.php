<?php

namespace model\entity;

/**
 * AdminMasterNavigation
 */
class AdminMasterNavigation
{
    /**
     * @var integer
     */
    private $masterId;

    /**
     * @var integer
     */
    private $navId;

    /**
     * @var string
     */
    private $type = 'master';

    /**
     * @var string
     */
    private $group = 'default';

    /**
     * @var \DateTime
     */
    private $addTime;


    /**
     * Set masterId
     *
     * @param integer $masterId
     *
     * @return AdminMasterNavigation
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
     * Set navId
     *
     * @param integer $navId
     *
     * @return AdminMasterNavigation
     */
    public function setNavId($navId)
    {
        $this->navId = $navId;

        return $this;
    }

    /**
     * Get navId
     *
     * @return integer
     */
    public function getNavId()
    {
        return $this->navId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return AdminMasterNavigation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set group
     *
     * @param string $group
     *
     * @return AdminMasterNavigation
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     *
     * @return AdminMasterNavigation
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

