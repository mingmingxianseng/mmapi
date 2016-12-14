<?php

namespace model\entity;

/**
 * AdminCrontab
 */
class AdminCrontab
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $crontabStr;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $query = '';

    /**
     * @var string
     */
    private $remark;

    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $runCount = '0';

    /**
     * @var string
     */
    private $status = 'wait';

    /**
     * @var \DateTime
     */
    private $lastTime;

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
     * @return AdminCrontab
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
     * Set crontabStr
     *
     * @param string $crontabStr
     *
     * @return AdminCrontab
     */
    public function setCrontabStr($crontabStr)
    {
        $this->crontabStr = $crontabStr;

        return $this;
    }

    /**
     * Get crontabStr
     *
     * @return string
     */
    public function getCrontabStr()
    {
        return $this->crontabStr;
    }

    /**
     * Set command
     *
     * @param string $command
     *
     * @return AdminCrontab
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set query
     *
     * @param string $query
     *
     * @return AdminCrontab
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set remark
     *
     * @param string $remark
     *
     * @return AdminCrontab
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return AdminCrontab
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
     * Set runCount
     *
     * @param integer $runCount
     *
     * @return AdminCrontab
     */
    public function setRunCount($runCount)
    {
        $this->runCount = $runCount;

        return $this;
    }

    /**
     * Get runCount
     *
     * @return integer
     */
    public function getRunCount()
    {
        return $this->runCount;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return AdminCrontab
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
     * Set lastTime
     *
     * @param \DateTime $lastTime
     *
     * @return AdminCrontab
     */
    public function setLastTime($lastTime)
    {
        $this->lastTime = $lastTime;

        return $this;
    }

    /**
     * Get lastTime
     *
     * @return \DateTime
     */
    public function getLastTime()
    {
        return $this->lastTime;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     *
     * @return AdminCrontab
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

