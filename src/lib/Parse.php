<?php


namespace Tp\lib;


use Tp\exception\MdException;

class Parse
{
    public static function getValidate(string $validate):?array
    {
        try{
            if(empty($validate)) return null;
            $fileMap = Tool::getDirFile(Tool::getValidateRootPath());
            $file = Tool::getValidateFile($validate,$fileMap);
            if ($file == null) return $file;
            $validate = str_replace(env('APP_PATH'),env('APP_NAMESPACE').'/',trim($file,Tool::$EXT));
            $validate = str_replace('/','\\',$validate);
            $rule = (new Validate(new $validate()))->getRule();
            return $rule;

        }catch (\Exception $exception){
            throw new MdException(['message'=>'创建文档失败,'.$exception->getMessage()]);
        }
    }

}