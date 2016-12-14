<?php

namespace model\entity;

/**
 * AdminMasterPermission
 */
class AdminMasterPermission
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $permissionId;

    /**
     * @var integer
     */
    private $masterId;

    /**
     * @var string
     */
    private $type = 'master';

    /**
     * @var string
     */
    private $group = 'admin';

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
     * Set permissionId
     *
     * @param string $permissionId
     *
     * @return AdminMasterPermission
     */
    public function setPermissionId($permissionId)
    {
        $this->permissionId = $permissionId;

        return $this;
    }

    /**
     * Get permissionId
     *
     * @return string
     */
    public function getPermissionId()
    {
        return $this->permissionId;
    }

    /**
     * Set masterId
     *
     * @param integer $masterId
     *
     * @return AdminMasterPermission
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
     * Set type
     *
     * @param string $type
     *
     * @return AdminMasterPermission
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
     * @return AdminMasterPermission
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
     * @return AdminMasterPermission
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

