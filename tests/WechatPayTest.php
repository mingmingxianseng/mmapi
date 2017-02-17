<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/10
 * Time: 21:22
 */

namespace mmxs\mmapi\tests;

use GuzzleHttp\Client;
use mmapi\core\Cache;
use mmapi\core\Config;
use mmapi\wechat\cache\CacheProvider;
use mmapi\wechat\core\UserManage;
use mmapi\wechat\core\WechatException;
use mmapi\wechat\log\EchologProvider;
use mmapi\wechat\Pay;
use mmapi\wechat\pay\CreateOrderRequest;
use mmapi\wechat\pay\QueryOrderResponse;
use mmapi\wechat\Wechat;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\CascadingStrategy;

class WechatPayTest extends TestCase
{
    /** @var  Pay */
    protected $pay;
    protected $outTradeNo;

    public function setUp()
    {

        parent::setUp(); // TODO: Change the autogenerated stub
        Config::batchSet(['wxpay' => [
            'app_id'      => 'wx231c1caff92c34bc',
            'mch_id'      => 1273225701,
            'app_key'     => '99dcdc556c0ca260b04339242560e333',
            'cert_path'   => __DIR__ . '/cert/apiclient_cert.pem',
            'key_path'    => __DIR__ . '/cert/apiclient_key.pem',
            'rootca_path' => __DIR__ . '/cert/rootca.pem',
        ]]);
        $this->pay        = new Pay(Config::get('wxpay'));
        $this->outTradeNo = '201702081642188710';
    }

    public function testCreateOrder()
    {
        $request = $this->pay->create();
        $request
            ->setBody('test')
            ->setOutTradeNo($this->outTradeNo)
            ->setTotalFee(1)
            ->setNotifyUrl('')
            ->setTradeType(CreateOrderRequest::TRADE_TYPE_APP)
            ->setSpbillCreateIp('192.168.1.1')
            ->send();

        $this->assertTrue($request->getResponse()->isSuccessful());
    }

    public function testQuery()
    {
        $request = $this->pay->query();

        $request->setOutTradeNo($this->outTradeNo)->send();

        $this->assertFalse($request->getResponse()->isPayed());
    }

    public function testCloseOrder()
    {
        $response = $this->pay->close($this->outTradeNo);
        $this->assertTrue($response->isSuccessful());
    }

    public function testRefund()
    {
        $request = $this->pay->refund();

        $response = $request
            ->setOutTradeNo($this->outTradeNo)
            ->setOutRefundNo(date('YmdHis') . mt_rand(10, 99))
            ->setTotalFee(1000)
            ->setRefundFee(1000)
            ->send();

        if ($response->isSuccessful()) {
            //退款成功
        } else {
            echo $response->getError();
        }
    }

    public function testQueryRefund()
    {
        $request = $this->pay->queryRefund();

        $response = $request->setOutTradeNo('201701191906141174')
            ->send();
        if ($response->isSuccessful()) {
            //退款成功
        } else {
            echo $response->getError();
        }
    }
}