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



