AppChecker
=============================
Z-Blog应用中心上架检测工具。


## 使用说明
php appchecker [YOUR_APP_ID]

## 检测内容
1. 不规范的全局变量、函数等命名
1. 未使用网络类，而使用curl的检测
1. （主题）多余的jQuery
1. （主题）首页W3C验证

## 手把手

以下说明仅针对不明白各种工具使用方式的Windows用户。如果你是Linux / OSX用户，记得``composer install``就好了。

1. 确认一下，你的PHP是否已经被正确配置了？如果没有被正确配置，以下任意步骤均会报错。可参考这篇文章进行配置：http://jingyan.baidu.com/article/d2b1d10273e0df5c7e37d4ba.html
1. 点击[Download ZIP](https://github.com/zsxsoft/appchecker/archive/master.zip)，下载zip并解压。
1. 安装[Composer](https://getcomposer.org/)
1. 打开记事本，修改解压出来的``config.json``，把``path``调整为你的Z-BlogPHP实际安装路径（如C:\inetpub\wwwroot，不得有中文），用ANSI格式保存。
1. 双击``install.bat``，稍等片刻。
1. 双击``run.bat``，可以正常使用了！