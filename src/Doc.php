<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/7/1
 */

namespace WangYu;

use WangYu\exception\DocException;
use WangYu\lib\DocApi;
use WangYu\lib\DocReflex;
use WangYu\lib\DocTool;

/**
 * Class Doc API文档生成
 * @package WangYu
 */
class Doc
{
    /**
     * @var string $file 文件
     */
    public $file = '';
    /**
     * @var array $apis API反射数据
     */
    public $apis = [];
    /**
     * @var string $ds 默认文档前缀
     */
    protected $dp = '#';
    /**
     * @var string $ds 默认文档后缀
     */
    protected $ds = PHP_EOL.PHP_EOL;

    /**
     * Doc constructor.
     * @param string $module 模块名称
     * @param string $filename 文档名称
     * @param bool $force 新建文件,如果存在，默认会更改以前的名称，然后根据文件的时间生成文件备份
     * @throws DocException
     */
    public function __construct(string $module = 'api',string $filename = 'api-md',bool $force = true)
    {
        try{
            $this->setFilename($filename);
            $this->backupFile($force);
            $this->apis = (new DocApi($module))->get();
            $this->apis = DocReflex::toReflex($this->apis);
        }catch (\Exception $exception){
            throw new DocException(['message'=>'初始化数据失败~，'.$exception->getMessage()]);
        }
    }

    /**
     * 执行
     * @throws DocException
     */
    public function execute()
    {
        try{
            $this->writeToc();
            $this->writeApi();
        }catch (\Exception $exception){
            throw new DocException(['message'=>'生成文档失败~，'.$exception->getMessage()]);
        }
    }

    /**
     * 设置文件名
     * @param string|null $name
     */
    protected function setFilename(string $name = null):void
    {
        $name = trim($name);
        $name = $name ?: 'api-md-'.date('YmdHis');
        $this->file = env('ROOT_PATH').$name.'.md';
    }

    /**
     * 是否备份文件
     * @param bool $bool
     * @throws \Exception
     */
    protected function backupFile(bool $bool = true):void {
        $bool && DocTool::backupFile($this->file);
    }

    /**
     * 写入数据
     * @param string $file 文件路径
     * @param string $content
     */
    protected function write(string $file,string $content):void
    {
        DocTool::write($file,$content);
    }

    /**
     * 写TOC文档
     */
    protected function writeToc():void
    {
        $content = $this->format(' API文档目录');
        try{
            foreach ($this->apis as $api){
                $this->dp = '- '; $content .= $this->formatToc(DocTool::substr($api['class']).':'.
                    DocTool::substr($api['doc']));
                foreach ($api['actions'] as $action){
                    $this->dp = '   - '; $this->ds = PHP_EOL;
                    $content .= $this->formatToc(DocTool::substr($action['action']).':'.
                        DocTool::substr($action['doc']));
                }
            }
            $this->write($this->file,$content);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }



    /**
     * 写API文档
     */
    protected function writeApi():void
    {
        try{
            $this->ds = PHP_EOL.PHP_EOL;
            $this->dp = '# ';
            $content = $this->format(' API文档内容');
            foreach ($this->apis as $api){
                $this->dp = '- '; $content .= $this->formatToc(DocTool::substr($api['class']).':'.
                    DocTool::substr($api['doc']));
                foreach ($api['actions'] as $action){
                    $content .= $this->writeAction($api,$action);
                }
            }
            $this->write($this->file,$content);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * 写入方法
     * @param array $action
     * @return string
     * @throws \Exception
     */
    protected function writeAction(array $api,array $action = []):string
    {
        try{
            $this->dp = '### ';
            $content = $this->writeDoc($action['action'],$action['doc']);
            $this->dp = '- ';
            $content .= $this->writeUrl($api['route'],$action['route']['rule']);
            $content .= $this->writeMethod($action['route']['method']);
            $content .= $this->writeParam($action['params']);
            $content .= $this->writeError($action['error']);
            $content .= $this->writeSuccess($action['success']);
            $this->ds = PHP_EOL.PHP_EOL;
            return $content;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    // 写方法注释
    protected function writeDoc($action,$doc){
        return $this->format(DocTool::substr($action).':'.DocTool::substr($doc));
    }

    // 写方法URL
    protected function writeUrl($route,$rule){
        return $this->format('[url] : `/'.$route.'/'.$rule.'`');
    }

    // 写方法请求类型
    protected function writeMethod($method){
        return $this->format('[method] : `'.$method.'`');
    }

    // 写方法请求参数
    protected function writeParam($params){
        $content = $this->format('[params] : `请求参数文档`');
        $this->dp = ''; $this->ds = PHP_EOL;
        $content .= $this->format('| 参数名称 | 参数文档 | 参数 `filter` | 参数默认 |');
        $content .= $this->format('| :----: | :----: | :----: | :----: |');
        foreach ($params as $param){
            $content .= $this->format('| '.$param['name'].' | '.$param['doc'].' | '.
                str_replace('|','#',$param['rule']).' | 保留字段 |');
        }
        return $content;
    }

    // 写方法错误返回
    protected function writeError($json){
        $content = $this->format('- [error] : `错误返回样例`');
        $content .= $this->format('```json5'.PHP_EOL.$json.PHP_EOL.'```');
        return $content;
    }

    // 写方法正确返回
    protected function writeSuccess($json){
        $content = $this->format('- [success] : `成功返回样例`');
        $content .= $this->format('```json5'.PHP_EOL.$json.PHP_EOL.'```');
        return $content;
    }

    /**
     * 格式化内容文档
     * @param string $content
     * @return string
     */
    protected function format(string $content = ''):string
    {
        return $this->dp.$content.$this->ds;
    }


    /**
     * 获取Toc内容文档
     * @param string $content
     * @return string
     */
    protected function formatToc(string $content = ''):string
    {
        return $this->dp.' ['.ucwords($content).'](#'.$content.')'.$this->ds;
    }
}