@echo off
set input=
set /p input=请输入待检测的应用ID：
php appchecker %input%
echo 请按任意键退出。
pause>nul