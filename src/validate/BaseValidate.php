<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\validate;


use WangYu\exception\DocException;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * @return bool
     * @throws DocException
     */
    public function goCheck()
    {
        //获取HTTP传入的参数
        $params = Request::param();
        //对这些参数做校验
        $result = $this->batch()->check($params);
        if (!$result) {
            throw new DocException(['message' => $this->error]);
        } else {
            return true;
        }
    }

    public function getField(){
        return $this->field;
    }

    public function getRule(){
        return $this->rule;
    }

    public function getMessage(){
        return $this->message;
    }
}