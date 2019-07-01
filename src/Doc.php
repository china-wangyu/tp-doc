<?php


namespace WangYu;

use WangYu\exception\MdException;
use WangYu\lib\Api;
use WangYu\lib\Reflex;
use WangYu\lib\Tool;

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
     * Doc constructor.初始化
     * @param string $module
     * @param string $filename
     * @throws MdException
     */
    public function __construct(string $module = 'api',string $filename = 'api-md')
    {
        $this->setFilename($filename);
        $this->apis = (new Api($module))->get();
        $this->apis = Reflex::toReflex($this->apis);
    }

    /**
     * 执行
     * @throws MdException
     */
    public function execute()
    {
        try{
            $this->writeToc();
            $this->writeApi();
        }catch (\Exception $exception){
            throw new MdException(['message'=>'生成文档失败~，'.$exception->getMessage()]);
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
        $this->file = $name.'.md';
    }

    /**
     * 写入数据
     * @param string $file 文件路径
     * @param string $content
     */
    protected function write(string $file,string $content):void
    {
        Tool::write($file,$content);
    }

    /**
     * 写TOC文档
     */
    protected function writeToc():void
    {
        $content = $this->format(' API文档[TOC]');
        try{
            foreach ($this->apis as $api){
                $this->dp = '- '; $content .= $this->formatToc(Tool::substr($api['doc']));
                foreach ($api['actions'] as $action){
                    $this->dp = '   - '; $this->ds = PHP_EOL;
                    $content .= $this->formatToc(Tool::substr($action['action']).':'.
                        Tool::substr($action['doc']));
                }
            }
            $this->write($this->file,$content);
        }catch (\Exception $exception){
            throw new Exception($exception->getMessage());
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
                $this->dp = '## '; $content .= $this->format(Tool::substr($api['doc']));
                foreach ($api['actions'] as $action){
                    $content .= $this->writeAction($action);
                }
            }
            $this->write($this->file,$content);
        }catch (\Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 写入方法
     * @param array $action
     * @return string
     */
    protected function writeAction(array $action = []):string
    {
        try{
            $this->dp = '### ';
            $content = $this->format(Tool::substr($action['action']).':'.Tool::substr($action['doc']));
            $this->dp = '- ';
            $content .= $this->format('[url] : `'.$action['route']['rule'].'`');
            $content .= $this->format('[method] : `'.$action['route']['method'].'`');
            $content .= $this->format('[params] : `参数文档`');
            $this->dp = ''; $this->ds = PHP_EOL;
            $content .= $this->format('| 参数名称 | 参数文档 | 参数 `filter` | 参数默认 |');
            $content .= $this->format('| :----: | :----: | :----: | :----: |');
            foreach ($action['params'] as $param){
                $content .= $this->format('| '.$param['name'].' | '.$param['doc'].' | '.
                    str_replace('|','#',$param['rule']).' | 保留字段 |');
            }
            $this->ds = PHP_EOL.PHP_EOL;
            return $content;
        }catch (\Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }



    /**
     * 写文档标识
     */
    protected function writeFlag():void
    {

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