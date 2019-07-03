<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\exception;


class DocException extends \WangYu\BaseException
{
    public $code = 400;
    public $message = '创建Md接口文档错误';
    public $error_code = 66667;
}