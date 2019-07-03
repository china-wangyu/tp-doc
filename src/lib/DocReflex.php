<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\lib;

use think\Exception;
use WangYu\exception\DocException;

/**
 * Class Reflex 获取反射文档
 * @package LinCmsTp\lib
 */
class DocReflex extends \WangYu\Reflex
{

    public static function toReflex(array $api):?array
    {
        try{
            $result = $action = [];
            if (empty($api) or !is_array($api)) return [];
            foreach ($api as $key => $actions){
                $action = static::getApiActions(new $key(),$actions);
                $doc = static::getApiClass(new $key());
                if(empty($action)) continue;
                array_push($result,['class'=>$key,'doc'=>$doc,'actions'=>$action]);
            }
            return $result;
        }catch (\Exception $exception){
            throw new DocException(['message'=>$exception->getMessage()]);
        }
    }

    public static function getApiClass($object)
    {
        try{
            $result = '';
            if (is_object($object)){
                $reflex = new static($object);
                ($reflex)->reflex->getDocComment();
                $result = $reflex->get('doc',['doc']);
                $result = isset($result[0]['doc']) ? $result[0]['doc'] : get_class($object);
            }
            return $result;
        }catch (\Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 获取API方法内容
     * @param $object
     * @param array $actions
     * @return array
     * @throws Exception
     */
    public static function getApiActions($object,array $actions = []):array
    {
        try{
            $result = [];
            if (is_object($object)){
                foreach ($actions as $key => $item){
                    $Reflex = new static($object,$item);
                    $route = $Reflex->get('route', ['rule', 'method']);
                    $params = $Reflex->get('param', ['name','doc','rule']);
                    $validate = $Reflex->get('validate', ['validateModel']);
                    if(empty($route) and empty($params) and empty($validate)) continue;
                    if(!empty($validate)){
                        $params = DocParse::getValidate($validate[0]['validateModel']);
                    }
                    array_push($result,[$item=>['route'=>$route,'params'=>$params]]);
                }
            }
            return $result;
        }catch (\Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }
}