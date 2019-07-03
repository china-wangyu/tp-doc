<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\lib;


use WangYu\exception\DocException;

class DocValidate
{

    public $validate = [];

    public function __construct($validate)
    {
        if ($validate instanceof \Tp\validate\BaseValidate){
            $this->validate = $validate;
        }else{
            throw new DocException(['message'=>'数据类型异常~']);
        }

    }

    /**
     * 获取rule数据
     * @return array|null
     * @throws DocException
     */
    public function getRule():?array
    {
        try{
            $result = [];
            $fields = $this->validate->getField();
            $rules = $this->validate->getRule();
            if (empty($rules))return array();
            foreach ($rules as $key => $rule){
                array_push($result,['name'=>$this->getRuleName($key),'rule'=>$rule,'doc'=>$this->getRuleDoc($key,$fields)]);
            }
            return $result;
        }catch (\Exception $exception){
            throw new DocException(['message'=>$exception->getMessage()]);
        }
    }

    /**
     * 获取规则名称
     * @param string $name
     * @return string
     */
    public function getRuleName(string $name):string
    {
        // 用户格式为：$rule = {'name|名称'=>'require'}
        return strstr($name,'|') ? explode('|',$name)[0] : $name;
    }

    /**
     * 获取规则名称
     * @param string $name
     * @param array $fields
     * @return string
     */
    public function getRuleDoc(string $name,array $fields):string
    {
        $doc = '';
        // 用户格式为：$rule = {'name|名称'=>'require'}
        strstr($name,'|') && $doc = explode('|',$name)[1];
        // 用户格式为：$field = {'name'=>'名称'}
        if(empty($doc) && isset($fields[$name])) $doc = $fields[$name];
        return $doc;
    }
}