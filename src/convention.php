<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2016/12/12
 * Time: 23:33
 */
return [
    'response'         => [
        'default_code'   => 'SUCCESS',
        'default_msg'    => 'SUCCESS',
        'default_errno'  => 'SYSTEM_ERROR',
        'default_errmsg' => '系统异常，请稍后重试',
        'header'         => [
            'Request-Id' => REQUEST_ID,
        ],
        'options'        => [],
    ],
    //路由配置
    'dispatcher'       => [
        'layer'          => 's',
        'default_action' => 'index',
        'namespace'      => 'app',
    ],
    'error_reportiong' => '',
    'cache'            => [
        'type'          => 'file',
        'expire'        => 0,
        'cache_subdir'  => false,
        'prefix'        => '111',
        'path'          => VPATH,
        'data_compress' => false,
    ],
    'log'              => [
        'time_format' => ' c ',
        'file_size'   => 2097152,
        'path'        => VPATH,
        'apart_level' => [],
    ],
];