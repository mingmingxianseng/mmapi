<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/10
 * Time: 17:29
 */

namespace mmapi\wechat;

use Doctrine\Common\Cache\CacheProvider;
use GuzzleHttp\Client;
use mmapi\api\LoginApi;
use mmapi\wechat\core\Cache;
use mmapi\wechat\core\Http;
use mmapi\wechat\core\Log;
use mmapi\wechat\core\Request;
use mmapi\wechat\core\ResponseInitiative;
use mmapi\wechat\core\WechatException;

class Wechat
{
    private $options = [
        'config' => [
            'appid'      => '',
            'app_secret' => '',
            'app_token'  => '',
            'app_deskey' => '',
            'debug'      => false,
        ],
        'client' => [
            'timeout'         => 10,
            'allow_redirects' => false,
        ],
    ];
    private $debug;
    private $appid;
    private $app_secret;
    private $app_token;
    private $app_deskey;
    /** @var  Client */
    private $client;

    /** @var  Cache */
    private $cacheProvider;
    /** @var  Log */
    private $logger;
    /** @var ResponseInitiative */
    private $reponseInitiative;

    /**
     * Wechat constructor.
     *
     * @param array $options 配置数组
     * @param Cache $cache   cache对象
     *
     * @throws WechatException
     */
    public function __construct(array $options = [], Cache $cache)
    {
        $this->options = array_merge($this->options, $options);
        $config        = $this->options['config'];
        $this->debug   = $config['debug'] == true;

        $this->appid      = $config['appid'];
        $this->app_secret = $config['app_secret'];
        $this->app_token  = $config['app_token'];
        $this->app_deskey = $config['app_deskey'];
        if (empty($this->appid) || empty($this->app_secret) || empty($this->app_token)) {
            throw new WechatException('appid、app_secret、app_token 不能为空', 'MISS_APPID_SECRET_TOKEN');
        }
        if (!($cache instanceof Cache)) {
            throw new WechatException("cache对象不能为空", 'MISSING_CACHE');
        }
        $this->cacheProvider = $cache;
    }

    /**
     * @desc   isDebug 是否调试模式
     * @author chenmingming
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug == true;
    }

    /**
     * @desc   getOption
     * @author chenmingming
     *
     * @param $option
     *
     * @return mixed|null
     */
    public function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * @return string
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * @return string
     */
    public function getAppSecret()
    {
        return $this->app_secret;
    }

    /**
     * @return string
     */
    public function getAppToken()
    {
        return $this->app_token;
    }

    /**
     * @desc   setLogger
     * @author chenmingming
     *
     * @param Log $logger 日志处理类
     *
     * @return $this
     */
    public function setLogger(Log $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @desc   checkSignature 检查配置情况
     * @author chenmingming
     * @return bool 成功失败
     */
    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];

        $tmpArr = [$this->app_token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $_GET['echostr'];

            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc   getAccessToken
     * @author chenmingming
     * @return string
     */
    public function getAccessToken()
    {
        $accessToken = $this->cacheProvider->get($this->getAccessKey());
        if (!$accessToken) {
            $accessToken = $this->_getAccessToken();
        } else {
            if ($accessToken['expire'] < time()) {
                $accessToken = $this->_getAccessToken();
            }
        }

        return $accessToken['access_token'];
    }

    /**
     * @desc   _getAccessToken
     * @author chenmingming
     * @return array
     * @throws WechatException
     */
    private function _getAccessToken()
    {
        $responseArray = $this->getHttp()
            ->setUrl('https://api.weixin.qq.com/cgi-bin/token')
            ->setPrameter('grant_type', 'client_credential')
            ->setPrameter('appid', $this->appid)
            ->setPrameter('secret', $this->app_secret)
            ->withoutAccessToken()
            ->get();
        if (!isset($responseArray['access_token'])) {
            throw new WechatException("获取access_token失败", 'GET_ACCESS_TOKEN_FAILED', $responseArray);
        }
        $responseArray['expire'] = time() + (int)$responseArray['expires_in'];
        $this->cacheProvider->set($this->getAccessKey(), $responseArray, $responseArray['expires_in']);

        return $responseArray;
    }

    /**
     * @desc   getAccessKey
     * @author chenmingming
     * @return string
     */
    private function getAccessKey()
    {
        return 'access_token_' . $this->appid;
    }

    /**
     * @desc   getClient
     * @author chenmingming
     * @return Client
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client($this->options['client']);
        }

        return $this->client;
    }

    /**
     * @desc   getHttp
     * @author chenmingming
     * @return Http
     */
    public function getHttp()
    {
        return new Http($this);
    }

    /**
     * @desc   getInitiativeReponse
     * @author chenmingming
     * @return ResponseInitiative
     */
    public function getInitiativeReponse()
    {
        if (is_null($this->reponseInitiative)) {
            $this->reponseInitiative = new ResponseInitiative($this);
        }

        return $this->reponseInitiative;
    }

    /**
     * @desc   log
     * @author chenmingming
     *
     * @param string $log   日志类型
     * @param string $label 日志标签
     */
    public function log($log, $label)
    {
        if ($this->debug) {
            $this->logger->log($log, $label);
        }
    }

    /**
     * 判断验证请求的签名信息是否正确
     *
     * @param  string $token 验证信息
     *
     * @return boolean
     */
    private function validateSignature()
    {
        $signature      = $_GET['signature'];
        $timestamp      = $_GET['timestamp'];
        $nonce          = $_GET['nonce'];
        $signatureArray = [$this->app_token, $timestamp, $nonce];
        sort($signatureArray, SORT_STRING);

        return sha1(implode($signatureArray)) == $signature;
    }

    /**
     * @desc   listen 监听
     * @author chenmingming
     *
     * @param Request $request 监听对象
     *
     * @return string
     */
    public function listen(Request $request)
    {
        if ($this->validateSignature()) {
            //接受并解析微信中心POST发送XML数据
            $xml = (array)simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);

            $this->log($xml, 'receive-xml');

            //将数组键名转换为小写
            return $request->deal(array_change_key_case($xml, CASE_LOWER));
        }

        return '';
    }
}