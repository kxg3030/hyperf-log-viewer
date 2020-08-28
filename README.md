#### 一、效果图
![image](https://github.com/kxg3030/hyperf-log-viewer/blob/master/src/log_viewer.png)

#### 二、使用方法

1.安装组件

`composer require kxg3030/hyperf-log-viewer`

2.发布配置文件

`php bin/hyperf.php vendor:publish kxg3030/hyperf-log-viewer`

3.注册路由地址

`Router::get('/logs', 'Hyperf\LogViewer\Controller\LogViewController@index');`

4.安装Hyperf组件

`composer require hyperf/view`

5.安装模板引擎

`composer require sy-records/think-template`

6.配置视图

> 在config\autoload\view.php文件中添加配置
```$xslt

return [
        // 使用的渲染引擎
        'engine' => ThinkEngine::class,
        // 不填写则默认为 Task 模式，推荐使用 Task 模式
        'mode'   => Mode::TASK,
        'config' => [
            // 若下列文件夹不存在请自行创建
            'view_path'  => BASE_PATH . '/storage/view/',
            'cache_path' => BASE_PATH . '/runtime/view/',
        ],
];
```
