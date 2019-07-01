<?php


namespace Tp;

use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Exception;

class Command extends think\console\Command
{
    /**
     * @var string $file 文件
     */
    protected $file = '';
    /**
     * @var array $apis API反射数据
     */
    protected $apis = [];
    /**
     * @var string $ds 默认文档前缀
     */
    protected $dp = '#';
    /**
     * @var string $ds 默认文档后缀
     */
    protected $ds = PHP_EOL.PHP_EOL;

    protected function configure()
    {
        $this->setName('lincms:markdown')
            ->addArgument('module', Argument::OPTIONAL, "your LinCms API Folder,Examples: api = /application/api",'api')
            ->addArgument('filename', Argument::OPTIONAL, "your LinCms API to markdown filename")
            ->setDescription('LinCms API to Markdown');
    }

    protected function execute(Input $input, Output $output)
    {
        $doc = new Doc($input->getArgument('module'),$input->getArgument('filename'));
        $doc->execute();
        $output->writeln("Successful. Output Document Successful . File Path ：$doc->file ");
    }
}