AppChecker
=============================
Z-Blog应用中心上架检测工具。

## 命令行使用说明
设置环境变量``ZBP_PATH``为你的Z-BlogPHP所在路径，后执行
``php checker run [YOUR_APP_ID]``

如使用各类``sh``，可以直接：
``ZBP_PATH=YOUR_ZBP_PATH php checker run [YOUR_APP_ID]``

## 环境要求
GUI: Windows + .NET Framework 4.5

命令行：PHP 5.4+

## 检测内容
1. 不规范的全局变量、函数等命名
1. 未使用网络类，而使用curl的检测
1. （主题）多余的jQuery
1. （主题）首页W3C验证

## 开源协议

The MIT License