<?php

namespace model\entity;

/**
 * AdminCrontabLog
 */
class AdminCrontabLog
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
    private $text;

    /**
     * @var string
     */
    private $label;

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
     * @return AdminCrontabLog
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
     * Set text
     *
     * @param string $text
     *
     * @return AdminCrontabLog
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
     * Set label
     *
     * @param string $label
     *
     * @return AdminCrontabLog
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     *
     * @return AdminCrontabLog
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

