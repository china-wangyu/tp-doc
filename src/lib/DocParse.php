<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\lib;


/**
 * Class DocParse 解析类
 * @package WangYu\lib
 */
class DocParse
{
    public static function getValidate(string $validate):?array
    {
        try{
            if(empty($validate)) return null;
            $fileMap = DocTool::getDirFile(DocTool::getValidateRootPath());
            $file = DocTool::getValidateFile($validate,$fileMap);
            if ($file == null) return $file;
            $validate= str_replace(DocTool::$EXT,'',$file);
            $validate = str_replace(env('APP_PATH'),env('APP_NAMESPACE').'/',$validate);
            $validate = str_replace('/','\\',$validate);
            $rule = (new DocValidate(new $validate()))->getRule();
            return $rule;

        }catch (\Exception $exception){
            throw new \Exception('创建文档失败,'.$exception->getMessage());
        }
    }

}