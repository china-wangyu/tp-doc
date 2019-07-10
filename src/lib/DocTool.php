<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu\lib;


/**
 * Trait DocTool 工具类
 * @package WangYu\lib
 */
trait DocTool
{

    public static $EXT = '.php';


    /**
     * 备份文件
     * @param string $file
     * @throws \Exception
     */
    public static function backupFile(string $file):void
    {
        try{
            if(is_file($file)){
                $newFile = dirname($file).'backup-'.date('YmDHis').'-'.basename($file);
                if(!copy($file, $newFile)) throw new \Exception('备份文件失败~');
            }
        }catch (\Exception $exception){
            throw new \Exception('备份文件失败~');
        }
    }

    /**
     * 写入数据
     * @param string $path 文件路径
     * @param string $data  文件数据
     * @param int $flags file_put_content flags参数
     * @return bool|int 返回数据 或 false
     */
    public static function write(string $path,string $data = '',$flags = FILE_APPEND|LOCK_EX){
        return file_put_contents($path,$data,$flags);
    }

    /**
     * 截取字符串
     * @param $content
     * @param int $start
     * @param int $number
     * @return string
     */
    public static function substr($content,int $start = 0, int $number = 40)
    {
        return strtolower(substr(str_replace(' ','.',trim($content)),$start,$number));
    }

    /**
     * 创建目录，并设置权限
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public static function mkdir(string $path = ''){
        try{
            if(empty($path)) return false;
            is_file($path) &&  $path = dirname($path);
            $res = mkdir($path, 0755, true);
            $res1 = chmod($path, 0777);
            return $res == $res1 && $res == 1 ? true: false;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }


    /**
     * 获取对应文件夹下文件
     * @param string $dir 文件夹
     * @param string $ext 文件后缀
     * @return array|null
     * @throws \Exception
     */
    public static function getDirFile(string $dir,string $ext = ''):?array
    {
        try{
            empty($ext) && $ext = static::$EXT;
            if (empty($dir)) return [];
            $validateFileMap = [];
            foreach (scandir($dir) as $index => $item){
                if (strstr($item,$ext) !== false){
                    array_push($validateFileMap,$dir.'/'.$item);continue;
                }
                if (strstr($item,'.')!=false)  continue;
                $validateFileMap = array_merge($validateFileMap,static::getDirFile($dir.'/'.$item));
            }
            return $validateFileMap;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }


    /**
     * 获取文件对象
     * @param string $file
     * @return object|null
     * @throws \Exception
     */
    public static function getClass(string $file):?object
    {
        try{
            $namespace = str_replace(env('APP_PATH'),'/app/',$file);
            $namespace = str_replace('.php','',$namespace);
            $namespace = str_replace('/','\\',$namespace);
            return new $namespace();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * 获取类命名空间
     * @param object $object
     * @return string
     */
    public static function getClassNamespace(object $object):string
    {
        return get_class($object);
    }

    /**
     * 获取php文件方法
     * @param $object
     * @return array|null
     * @throws \Exception
     */
    public static function getPhpAction($object):?array
    {
        try{
            $parentActions = get_class_methods(get_parent_class($object));
            $objectActions = get_class_methods($object);
            if (empty($parentActions)) return $objectActions;
            $actions = array_diff($objectActions, $parentActions);
            return empty($actions) ? [] : $actions;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * 获取验证器默认路径
     * @return string
     */
    public static function getValidateRootPath(){
        $validate_root_path = empty(config('lin.validate_root_path')) ? 'api/validate' :config('lin.validate_root_path');
        return env('APP_PATH').$validate_root_path;
    }

    /**
     * 获取验证器文件
     * @param string $model
     * @param array $fileMap
     * @return string|null
     * @throws \Exception
     */
    public static function getValidateFile(string $model,array $fileMap = []):?string {
        try{
            // 获取脚本文件后缀
            $ext = static::$EXT;
            // 检测是否为分组验证器
            $controller = strstr(request()->controller(),'.') ?
                explode('.',request()->controller())[1]:
                request()->controller();
            $groupValidateFile = DocTool::getValidateRootPath().DIRECTORY_SEPARATOR.
                strtolower($controller).DIRECTORY_SEPARATOR.$model.$ext;
            if(in_array($groupValidateFile,$fileMap)) return $groupValidateFile;
            // 检测验证器目录下所有的验证器，是否有同名的验证器
            foreach ($fileMap as $item){
                if (strtolower(basename($item)) !== strtolower($model.$ext)) continue;
                return $item;
            }
            return null;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }


}