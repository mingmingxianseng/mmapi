12-16
1.增加图片上传类 支持阿里云OSS
2.优化APP类
3.增加DB缓存
4:优化Api
#17-01-15
1:优化api接口类
2:增加api接口防止重复提交表单的功能
3:修复DB缓存无效的bug
4:增加ApiException

#17-01-16
####api接口增加必传参数抛出特殊异常的功能
    / ****
    *@desc setRequire 传一个参数 类型可以有以下4种
    *array 该参数必传， 抛出一个msg和code的ApiException
    *字符串  该参数必传，若没有穿 抛出一个ApiException错误描述
    *设置false  即该参数非必传
    ****/
    
    $this->addParam('id')->setRequire(['id必须传递', 'ID_MUST']);
    $this->addParam('id')->setRequire('id必须传递');
    $this->addParam('id')->setRequire(false);
    $this->addParam('id')->setRequire(true);
    
####api增加防止重复提交的功能
解决一些对数据库有更新、插入或者删除操作的接口，如果前端产生并发，会产生重复记录的问题
*代码样例*
    
    protected function init()
    {
        $this->addParam('id')->setRequire(false);
        $this->addParam('categoryId')->setType(ApiParams::TYPE_INT)->setRequire(false)->setDefault(0);
        $this->addParam('list')->setType(ApiParams::TYPE_ARRAY);
        $this->addParam('name');
        $this->denyResubmit();
    }
##2017-01-20
php版本升级到7.1
\Exception 变更为 \Throwable

##2017-02-06
sqlBuilder 新增 like 支持
 并支持 match 模糊匹配 自动过滤 % 和 _

##2017-02-08
支持微信支付
###初始化
    Config::batchSet(['wxpay' => [
        'app_id'      => 'wx******',
        'mch_id'      => 127********,
        'app_key'     => '******',//api key
        'cert_path'   => __DIR__ . '/cert/apiclient_cert.pem',
        'key_path'    => __DIR__ . '/cert/apiclient_key.pem',
        'rootca_path' => __DIR__ . '/cert/rootca.pem',
    ]]);
    $this->pay        = new Pay(Config::get('wxpay'));
    $this->outTradeNo = '201702081642188710';
###创建订单   
    $request = $this->pay->create();
    $request
        ->setBody('test')
        ->setOutTradeNo($this->outTradeNo)
        ->setTotalFee(1)
        ->setNotifyUrl('')
        ->setTradeType(CreateOrderRequest::TRADE_TYPE_APP)
        ->setSpbillCreateIp('192.168.1.1')
        ->send();    
    if ($response->isSuccessful()) {
        //退款成功
    } else {
        echo $response->getError();
    }
###查询订单

    $request = $this->pay->query();
    $request->setOutTradeNo($this->outTradeNo)->send();
    if ($response->isSuccessful()) {
        $payData = $response->getData();
    } else {
        echo $response->getError();
    }

###关闭订单
     $response = $this->pay->close($this->outTradeNo);
     if ($response->isSuccessful()) {
         //关闭成功
     } else {
        //关闭失败 获取失败原因
         echo $response->getError();
     }
     
### 申请退款
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
         //失败 获取失败原因
        echo $response->getError();
    }
### 查询退款窗台
    $request = $this->pay->queryRefund();
    $response = $request->setOutTradeNo('201701191906141174')
        ->send();
    if ($response->isSuccessful()) {
        //退款成功
    } else {
        echo $response->getError();
    }



##2017-02-09

修复sqlBuilder join 3张表的bug 

##2017-02-19

增加根据不同域名加载不同配置的功能


##2017-03-02

model不会立即更新  走flush流程

##2017-03-04

不同类型的日志可以分开文件存放

sql 日志可以打印出datetime类型的绑定变量

##2017-03-05
优化sql日志
db 事务的处理  传递一个闭包即可

##2017-03-06
优化参数赋值
如果从目标传参方式中取不到值 则不填充value 

##2017-03-31
缓存只支持memcached 和redis 放弃其他缓存

##2017-04-12
支持post原始数据的参数
