<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace mmapi\log;

/**
 * 本地化调试输出到文件
 */
class File
{
    const COLOR_RIGHT = Color::PINK;
    const COLOR_RED = Color::RED;
    const COLOR_COMMON = Color::BLUE;
    const COLOR_GREEN = Color::GREEN;
    const COLOR_BLUE = Color::BLUE;
    const COLOR_GREY = Color::GREY;
    const COLOR_END = Color::END;

    protected $config = [
        //开关
        'status'      => true,
        //时间格式
        'time_format' => ' c ',
        //日志未知
        'filepath'    => VPATH . '/log.txt',
        //日志记录类型
        'level'       => ['log', 'error', 'info', 'sql', 'notice', 'alert'],
        //是否转码gbk
        'togbk'       => true,
        'suffix'      => '',
        'color'       => [
            'sql'   => self::COLOR_GREY,
            'error' => self::COLOR_RED,
        ],
    ];

    // 实例化并传入参数
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 日志写入接口
     *
     * @access public
     *
     * @param array $log 日志信息
     *
     * @return bool
     */
    public function save(array $log = [])
    {
        $now         = date($this->config['time_format']);
        $destination = $this->config['filepath'];
        $info        = '';
        foreach ($log as $type => $val) {
            $level = '';

            foreach ($val as $msg) {
                if (!is_string($msg)) {
                    $msg = var_export($msg, true);
                }
                $level .= '[ ' . $type . ' ] ' . $msg . "\r\n";
            }
            if (isset($this->config['color'][$type])) {
                $level = $this->config['color'][$type] . $level . self::COLOR_END;
            }
            $info .= $level;
        }
        if ($this->config['togbk']) {
            $info = iconv('UTF-8', 'GB18030', $info);
        }

        return error_log("[{$now}] {$this->config['suffix']} \t{$info}", 3, $destination);
    }

}
