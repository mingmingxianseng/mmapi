<?php

namespace model\entity;

/**
 * AdminNavigation
 */
class AdminNavigation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $pid = '0';

    /**
     * @var string
     */
    private $icon = '';

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $url = '';

    /**
     * @var string
     */
    private $group = '';

    /**
     * @var integer
     */
    private $orderNum = '99';

    /**
     * @var string
     */
    private $target = '_self';

    /**
     * @var string
     */
    private $status = 'open';

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
     * Set pid
     *
     * @param integer $pid
     *
     * @return AdminNavigation
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return AdminNavigation
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return AdminNavigation
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return AdminNavigation
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set group
     *
     * @param string $group
     *
     * @return AdminNavigation
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
     * Set orderNum
     *
     * @param integer $orderNum
     *
     * @return AdminNavigation
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * Get orderNum
     *
     * @return integer
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return AdminNavigation
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return AdminNavigation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     *
     * @return AdminNavigation
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

