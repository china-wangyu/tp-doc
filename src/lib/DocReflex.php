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
                $doc = static::getApiClass(new $key());
                $action = static::getApiActions(new $key(),$actions);
                if(empty($action)) continue;
                $result[$key] = array_merge(['class'=>$key,'actions'=>$action],$doc);
            }
            return $result;
        }catch (\Exception $exception){
            throw new DocException(['message'=>$exception->getMessage()]);
        }
    }

    /**
     * 获取类反射内容
     * @param $object
     * @return array|string
     * @throws \Exception
     */
    public static function getApiClass($object)
    {
        try{
            $result = '';
            if (is_object($object)){
                $reflex = new static($object);
                ($reflex)->reflex->getDocComment();
                $doc = $reflex->get('doc',['doc']);
                $route = $reflex->get('route',['rule']);
                $middleware = $reflex->get('middleware',[]);
                $result = [
                    'doc' => $doc[0]['doc'] ?? basename(get_class($object)),
                    'route' => $route[0]['rule'] ?? '',
                    'middleware' => $middleware[0] ?? []
                ];
            }
            return $result;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * 获取API方法内容
     * @param $object
     * @param array $actions
     * @return array
     * @throws \Exception
     */
    public static function getApiActions($object,array $actions = []):array
    {
        try{
            $result = [];
            if (is_object($object)){
                foreach ($actions as $key => $item){
                    $Reflex = new static($object,$item);
                    $doc = $Reflex->get('doc', ['doc']);
                    $route = $Reflex->get('route', ['rule', 'method']);
                    $params = $Reflex->get('param', ['name','doc','rule']);
                    $validate = $Reflex->get('validate', ['validateModel']);
                    $error = $Reflex->get('error', ['result']);
                    $success = $Reflex->get('success', ['result']);
                    if(empty($route) and empty($params) and empty($validate)) continue;
                    if(!empty($validate)){
                        $params = DocParse::getValidate($validate[0]['validateModel']);
                    }
                    $result[$item] = [
                        'action' => $item,
                        'doc' => $doc[0]['doc'] ?? '',
                        'route' => $route[0] ?? ['rule'=>'','method'=>''],
                        'params' => $params ?? [],
                        'error' => $error[0]['result'] ?? '',
                        'success' => $success[0]['result'] ?? '',
                    ];
                }
            }
            return $result;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}