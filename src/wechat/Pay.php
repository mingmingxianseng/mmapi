<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/2/8
 * Time: 11:41
 */

namespace mmapi\wechat;

use GuzzleHttp\Client;
use mmapi\wechat\pay\BaseAbstractRequest;
use mmapi\wechat\pay\CloseOrderRequest;
use mmapi\wechat\pay\CloseOrderResponse;
use mmapi\wechat\pay\CreateOrderRequest;
use mmapi\wechat\pay\QueryOrderRequest;
use mmapi\wechat\pay\QueryRefundRequest;
use mmapi\wechat\pay\RefundOrderRequest;

class Pay
{

    protected $options = [
        'app_id'      => '',
        'mch_id'      => '',
        'app_key'     => '',
        'cert_path'   => '',
        'key_path'    => '',
        'rootca_path' => '',
    ];

    private $client;

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
        $this->client  = new Client(['timeout' => 10]);
    }

    /**
     * @desc   init
     * @author chenmingming
     *
     * @param BaseAbstractRequest $request
     */
    private function init(BaseAbstractRequest $request)
    {
        $request->setAppId($this->options['app_id'])
            ->setMchId($this->options['mch_id'])
            ->setApiKey($this->options['app_key']);
    }

    /**
     * @desc   create
     * @author chenmingming
     * @return CreateOrderRequest
     */
    public function create()
    {
        $request = new CreateOrderRequest($this->client);

        $this->init($request);

        return $request;
    }

    public function query()
    {
        $request = new QueryOrderRequest($this->client);

        $this->init($request);

        return $request;
    }

    /**
     * @desc   close
     * @author chenmingming
     *
     * @param string $outTradeNo
     *
     * @return CloseOrderResponse
     */
    public function close(string $outTradeNo): CloseOrderResponse
    {
        $request = new CloseOrderRequest($this->client);

        $this->init($request);

        return $request
            ->setOutTradeNo($outTradeNo)
            ->send();
    }

    /**
     * @desc   refund
     * @author chenmingming
     * @return RefundOrderRequest
     */
    public function refund()
    {
        $request = new RefundOrderRequest($this->client);

        $this->init($request);

        $request->setCertPath($this->options['cert_path'])
            ->setKeyPath($this->options['key_path'])
            ->setRootcaPath($this->options['rootca_path']);

        return $request;
    }

    /**
     * @desc   queryRefund
     * @author chenmingming
     * @return QueryRefundRequest
     */
    public function queryRefund()
    {
        $request = new QueryRefundRequest($this->client);

        $this->init($request);

        return $request;
    }
}