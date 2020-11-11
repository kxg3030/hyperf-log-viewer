>最近在Hyperf中需要用到使用路由在线查看文件日志的功能,没有发现比较好用的,自己简单写了一个,支持查看和简单的内容搜索。

#### 一、界面

先上效果图：
![](https://cdn.learnku.com/uploads/images/202011/03/59976/LA3IzHd4WQ.png!large)


#### 二、使用

- 1.安装组件

`composer require sett/hyperf-log-viewer`
- 2.发布配置文件

`php bin/hyperf.php vendor:publish sett/hyperf-log-viewer`

- 3.注册路由

```
Router::get('/logs/list', [LogViewController::class, "index"]);
Router::post('/logs/delete', [LogViewController::class, "delete"]);
Router::get('/logs/download', [LogViewController::class, "download"]);
```

- 4.安装view组件

`composer require hyperf/view`

- 5.安装模板引擎

`composer require sy-records/think-template`

- 6.配置视图

> 在config\autoload\viewe.php文件中(如果不存在,自行创建),添加如下视图配置

```
return [
        'engine' => ThinkEngine::class,
        'mode'   => Mode::TASK,
        'config' => [
            // 若下列文件夹不存在请自行创建
            'view_path'  => BASE_PATH . '/storage/view/',
            'cache_path' => BASE_PATH . '/runtime/view/',
        ],
];
```
- 7.配置组件参数

> 在config\autoload\logViewer.php文件中,添加自己的日志文件目录

```
return [ 
   // 自定义, 比如runtime/logs/202011/ 需要定义成:"/runtime/logs/".date("Ym")."/"
   "path" => BASE_PATH . "/runtime/logs/", 
   // 日志文件匹配规则
   "pattern" => "*.log", 
   // 每页展示的条数
   "size" => 10 
   ]; 
```
#### 三、访问
打开自己的访问地址`ip:port/logs/list`就能看到日志界面了

#### 四、说明
- 日志时间格式只支持年月日时分秒格式,否则可能看不到记录,日志格式大概是这样

    `[2020-11-02 10:12:48] system.INFO: HTTP Server listening at 0.0.0.0:18310`

    `[2020-11-02 14:52:50] system.ERROR: must implement interface`
- 如果大家有什么意见或者建议，欢迎留言。