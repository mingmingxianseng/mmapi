<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2017/1/16
 * Time: 12:26
 */

namespace mmapi\core;

interface Params
{
//参数类型
    //整形
    const TYPE_INT = 'int';
    //字符串类型
    const TYPE_STRING = 'string';
    //浮点型
    const TYPE_FLOAT = 'float';
    //json格式
    const TYPE_JSON = 'json';

    //array 数组
    const TYPE_ARRAY = 'array';
    //参数传递方式
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    //get post 均可
    const METHOD_REQUEST = 'request';
    const METHOD_COOKIE = 'cookie';
    //参数验证方式
    const VALIDATE_TYPE_COMMON = 'common';//一般验证
    const VALIDATE_TYPE_REG = 'reg';//正则验证
    const VALIDATE_TYPE_IN = 'in';//在范围里
}