<?php


namespace Tp\lib;


use Tp\exception\MdException;

class Validate
{

    public $validate = [];

    public function __construct($validate)
    {
        if ($validate instanceof \Tp\validate\BaseValidate){
            $this->validate = $validate;
        }else{
            throw new MdException(['message'=>'数据类型异常~']);
        }

    }

    /**
     * 获取rule数据
     * @return array|null
     * @throws MdException
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
            throw new MdException(['message'=>$exception->getMessage()]);
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