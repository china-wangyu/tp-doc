# tp-doc

基于ThinkPHP5.1做的API文档模型composer扩展，支持反射文档生成
Website: https://github.com/china-wangyu/tp-doc


# 安装教程

## `composer` 安装
 
```composer
composer require wangyu/tp-doc
```

# 使用教程

## 第一种：输出反射API文档

### 写 `接口类` 注释,例如：`Admin` 类

   反射标识说明：
    
   | 名称 | 注释 | 使用说明 |
   | :----: | :----: | :----: |
   | doc | 文档说明 | @doc('方法名称') |
    
   ```php
    /**
    * @doc('Admin 后台管理类')
    * @package app\api\controller\cms
    */
    class Admin
    {
   ```

### 写`接口方法`注释,例如：`getAdminUsers` 方法

   > **注： 本扩展输出文档参数表是 `markdown table` 做的，
   因为 `markdown table` 的分隔符 `|` 与 `thinkphp 5.1`验证规则分隔符`|`冲突**
    
   > **本扩展输出文档时采取 `#` 代替 `thinkphp5.1` 验证规则分隔符 `|`**
    
   > 使用时：@param('id','用户ID','require|number')
    
   > 输出时：@param('id','用户ID','require#number')
    
   反射标识说明：
    
   | 名称 | 注释 | 使用说明 |
   | :----: | :----: | :----: |
   | doc | 文档说明 | @doc('方法名称') |
   | route | 路由规则 | @route('规则','请求类型') |
   | param | 参数验证 | @param('参数名称','参数注释','参数验证规则') |
   | validate | 验证模型验证 | @validate('模型名称') |
    
   ```php
    /**
    * 配置hidden后，这个权限信息不会挂载到权限图，获取所有可分配的权限时不会显示这个权限
    * @doc('获取所有可分配的权限')
    * @route('cms/admin/users','get')
    * @param('group_id','分组ID','require')
    * @param Request $request
    * @return array
    * @throws \think\exception\DbException
    */
    public function getAdminUsers(Request $request){.....}
   ```

### 输出API文档

- 命令行模式 
  
  > 有关于怎么使用命令行模式参考下，thinkphp5.1官方文档 [点我](https://www.kancloud.cn/manual/thinkphp5_1/354146) 
   
  - 配置application/command.php文件
  ```php
    <?php
    return [
        // API文档输出
        'doc:build'	=>	'WangYu/DocCommand',
    ];
  ```
  -  运行 `doc:build` 命令
  ```bash
    php think doc:build
  ```
  - 输出
  ```bash
    Successful. Output Document Successful . File Path ：api-md-20190701141656.md
  ```
  - 查看 `doc:build` 命令帮助
  ```bash
    php think doc:build -h
  ```
  - 输出
  ```bash
    Usage:
      doc:build [<module>] [<filename>]
    
    Arguments:
      module                your API Folder,Examples: api = /application/api [default: "api"]
      filename              your API to markdown filename
      
    Options:
      -h, --help            Display this help message
      -V, --version         Display this console version
      -q, --quiet           Do not output any message
          --ansi            Force ANSI output
          --no-ansi         Disable ANSI output
      -n, --no-interaction  Do not ask any interactive question
      -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug  
  ```
  
- 使用`Doc`类创建

  - 实例化`Doc`类
  ```php
    $doc = new \WangYu\Doc('API接口模块','输出文件名称');
  ```
  - `execute` 创建文档
  ```php
    $doc->execute();
  ```
  - 在项目根目录查看 `输出文件名称.md`
# 引用技术

- thinkphp5.1 [官网](https://www.kancloud.cn/manual/thinkphp5_1)
- lin-cms-tp/validate-core [官网](https://packagist.org/packages/lin-cms-tp/validate-core)
- lin-cms-tp/reflex-core [官网](https://packagist.org/packages/lin-cms-tp/reflex-core)