<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\exception;


class MdException extends \Exception
{
    public $code = 400;
    public $message = '创建Md接口文档错误';
    public $error_code = 66667;

    public function __construct($params = [])
    {
        isset($params['code']) && $this->code = $params['code'];
        isset($params['message']) && $this->message = $params['message'];
        isset($params['error_code']) && $this->error_code = $params['error_code'];
        if(class_exists('\LinCmsTp5\exception\BaseException')){
            throw  new \LinCmsTp5\exception\BaseException([
                'code' => $this->code,
                'msg' => $this->message,
                'error_code' => $this->error_code,
            ]);
        }
        throw new \Exception( $this->error_code.$this->message,$this->code);
    }
}