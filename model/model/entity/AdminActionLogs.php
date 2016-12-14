<?php

namespace model\entity;

/**
 * AdminActionLogs
 */
class AdminActionLogs
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $text;

    /**
     * @var integer
     */
    private $opId;

    /**
     * @var string
     */
    private $opName;

    /**
     * @var string
     */
    private $ext = '';

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
     * Set action
     *
     * @param string $action
     *
     * @return AdminActionLogs
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
     * @return AdminActionLogs
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
     * Set opId
     *
     * @param integer $opId
     *
     * @return AdminActionLogs
     */
    public function setOpId($opId)
    {
        $this->opId = $opId;

        return $this;
    }

    /**
     * Get opId
     *
     * @return integer
     */
    public function getOpId()
    {
        return $this->opId;
    }

    /**
     * Set opName
     *
     * @param string $opName
     *
     * @return AdminActionLogs
     */
    public function setOpName($opName)
    {
        $this->opName = $opName;

        return $this;
    }

    /**
     * Get opName
     *
     * @return string
     */
    public function getOpName()
    {
        return $this->opName;
    }

    /**
     * Set ext
     *
     * @param string $ext
     *
     * @return AdminActionLogs
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
     * @return AdminActionLogs
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

