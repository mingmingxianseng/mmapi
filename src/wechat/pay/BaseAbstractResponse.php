<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/2/8
 * Time: 11:56
 */

namespace mmapi\wechat\pay;

abstract class BaseAbstractResponse
{
    /**
     * @var BaseAbstractRequest
     */
    protected $request;
    private $data;

    public function __construct(BaseAbstractRequest $request, $data)
    {
        $this->request = $request;
        $this->data    = $data;
    }

    /**
     * @return BaseAbstractRequest
     */
    public function getRequest(): BaseAbstractRequest
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return isset($this->data['result_code']) && $this->data['result_code'] == 'SUCCESS';
    }

    /**
     * @desc   getReturnCode
     * @author chenmingming
     * @return string
     */
    public function getReturnCode()
    {
        return (string)$this->data['return_code'];
    }

    /**
     * @desc   getReturnMsg
     * @author chenmingming
     * @return string
     */
    public function getReturnMsg()
    {
        return (string)$this->data['return_msg'];
    }

    /**
     * @desc   getErrorStr
     * @author chenmingming
     * @return string
     */
    public function getError()
    {
        if (isset($this->data['err_code'])) {
            return "[{$this->data['err_code']}] {$this->data['err_code_des']}";
        }

        return '';
    }
}