AppChecker
=============================
Z-Blog应用中心上架检测工具。

## 初次使用
1. 安装[Composer](https://getcomposer.org/)
2. 修改``config.json``，把``path``调整为你的Z-BlogPHP实际安装路径，保存。
3. 运行以下命令
```bash
composer install
```

## 使用说明
php appchecker [YOUR_APP_ID]

## 检测内容
1. 不规范的全局变量、函数等命名
1. 未使用网络类，而使用curl的检测
1. （主题）多余的jQuery
1. （主题）首页W3C验证