AppChecker
=============================
Z-Blog应用中心上架检测工具。

## 命令行使用说明

### 设置环境变量
首先需要设置环境变量``ZBP_PATH``为你的Z-BlogPHP所在路径。各sh可无视此节。
cmd: ``SET ZBP_PATH=YOUR_ZBP_PATH``

### 检测已安装的应用

cmd: ``php checker run --bloghost=[YOUR_BLOG_HOST] [YOUR_APP_ID]``
sh: ``ZBP_PATH=YOUR_ZBP_PATH php checker run --bloghost=[YOUR_BLOG_HOST] [YOUR_APP_ID]``

### 解压并检测应用

cmd: ``php checker install --bloghost=[YOUR_BLOG_HOST] [YOUR_ZBA_PATH]``
sh: ``ZBP_PATH=YOUR_ZBP_PATH php checker install--bloghost=[YOUR_BLOG_HOST] [YOUR_ZBA_PATH]``


## 环境要求
GUI: Windows + .NET Framework 4.5

命令行：PHP 5.4+

## 上架标准
http://wiki.zblogcn.com/doku.php?id=app:auditstandard

## 检测内容
1. \! 不规范的全局变量、函数等命名
1. \* [在数据库内使用rand函数](http://bbs.zblogcn.com/forum.php?mod=viewthread&tid=90433&extra=)
1. \* （主题）重复的jQuery（系统已自带，非特殊情况，不允许再引用jQuery）
1. \* （主题）首页W3C验证（主要用于检测未闭合标签）
1. \* curl等网络类函数使用（应使用系统的``Network``类替代）
1. \* eval、system等函数

标注\*的内容代表仅进行初步检测，不检测较为高级的使用方法（如使用``$variable = function_name``来替代直接调用函数）。

标注\!的内容，一经检测到直接停止剩余检测。

## 开源协议

The MIT License