
#### 依赖

----------

* 本库依赖xdebug的code_coverage功能

* composer (可选)


#### 安装和加载

----------

最简使用方式如下，以常用的wordpress为例:


![image](http://3haku.net/wp-content/uploads/2014/10/QQ20141015-2@2x.png)

在代码开始的入口位置加上

```
include "phpcodecoveragesniffer/none_composer_loader.php";
SimpleCodeSnifferRunner::init("ccs");

```

如果你是用composer加载的本库，

依赖中加入

```
    "require" : {
        "princehaku/phpcodecoveragesniffer" : "dev-master"
    },
```

在loader后加上

```
SimpleCodeSnifferRunner::init("ccs");
```

#### 开始使用

----------

在你当前的站点上使用request参数即可开始使用


http://wp.loc.techest.net/?code_collect=true

![image](http://3haku.net/wp-content/uploads/2014/10/QQ20141015-1@2x.png)

然后页面右下角能看到一个**show code coverage graph**

点击后就能看到本次请求的代码逻辑覆盖情况。 (注: 如果报错了，请根据报错信息检查下权限。)

如下图：

![image](http://3haku.net/wp-content/uploads/2014/10/QQ20141015-3@2x.png)

![image](http://3haku.net/wp-content/uploads/2014/10/QQ20141015-4@2x.png)


附表:

参数名|值|作用
---|---|---
code_collect| true\|false | 开启收集模式
collect_mode| NORAML\|APPEND | 收集模式区别，默认是NORMAL，每次请求都会重新收集，不叠加收集数据
