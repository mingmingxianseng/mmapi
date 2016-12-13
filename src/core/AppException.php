<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/12
 * Time: 11:24
 */
namespace mmapi\core;

class AppException extends \Exception
{
    protected $errno;
    protected $detail;

    /**
     * AppException constructor.
     *
     * @param string $msg    错误详情
     * @param string $errno  错误英文码
     * @param array  $detail 错误详情
     */
    public function __construct($msg, $errno = 'ERROR', $detail = [])
    {
        $this->errno  = $errno;
        $this->detail = $detail;
        parent::__construct($msg, (int)$errno);
    }

    /**
     * @return string
     */
    public function getErrno()
    {
        return $this->errno;
    }

    /**
     * @desc   getDetail 获取异常详情数组
     * @author chenmingming
     * @return array
     */
    public function getDetail()
    {
        return $this->detail;
    }
}
