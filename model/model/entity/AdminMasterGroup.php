<?php

namespace model\entity;

/**
 * AdminMasterGroup
 */
class AdminMasterGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $gNavigation;

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
     * Set name
     *
     * @param string $name
     *
     * @return AdminMasterGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set gNavigation
     *
     * @param string $gNavigation
     *
     * @return AdminMasterGroup
     */
    public function setGNavigation($gNavigation)
    {
        $this->gNavigation = $gNavigation;

        return $this;
    }

    /**
     * Get gNavigation
     *
     * @return string
     */
    public function getGNavigation()
    {
        return $this->gNavigation;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     *
     * @return AdminMasterGroup
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

