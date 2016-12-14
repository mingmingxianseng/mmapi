<?php

namespace model\entity;

/**
 * AdminPermission
 */
class AdminPermission
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $text = '';

    /**
     * @var string
     */
    private $group = 'oa';

    /**
     * @var boolean
     */
    private $status = '1';

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
     * Set model
     *
     * @param string $model
     *
     * @return AdminPermission
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return AdminPermission
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return AdminPermission
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
     * Set group
     *
     * @param string $group
     *
     * @return AdminPermission
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
     * Set status
     *
     * @param boolean $status
     *
     * @return AdminPermission
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
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
     * @return AdminPermission
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

