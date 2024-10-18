> Gitee 仓库地址：<a href="https://gitee.com/openstrong/strongshop" target="_blank">https://gitee.com/openstrong/strongshop</a>

> 本次安装环境是 Windows 虚拟机 CentOS7
> lnpm.org 官方安装教程请参考：https://lnmp.org/install.html
> Web环境要求： `MySql 5.7+`,`Nginx 1.10+`,`PHP >= 7.2.5`
> 解除禁用函数 `proc_open`，`symlink`，`proc_get_status`

- 下载并解压 lnmp 安装包
```
cd /usr/local/src
wget http://soft.vpser.net/lnmp/lnmp1.8.tar.gz -cO lnmp1.8.tar.gz
tar zxf lnmp1.8.tar.gz
cd lnmp1.8
```

- 执行 shell 安装脚本
```
./install.sh lnmp
```

- 安装 mysql
```
You have 11 options for your DataBase install.
1: Install MySQL 5.1.73
2: Install MySQL 5.5.62 (Default)
3: Install MySQL 5.6.51
4: Install MySQL 5.7.34
5: Install MySQL 8.0.25
6: Install MariaDB 5.5.68
7: Install MariaDB 10.1.48
8: Install MariaDB 10.2.38
9: Install MariaDB 10.3.29
10: Install MariaDB 10.4.19
0: DO NOT Install MySQL/MariaDB
Enter your choice (1, 2, 3, 4, 5, 6, 7, 8, 9, 10 or 0): 4
You will install MySQL 5.7.34
===========================
Please setup root password of MySQL.
Please enter: 123456
```
选择4（安装 Mysql 5.7.34）

设置mysql密码：123456

- 启用 Mysql InnoDB 存储引擎

```
Do you want to enable or disable the InnoDB Storage Engine?
Default enable,Enter your choice [Y/n]:
```
选择 Y，启用

- 安装 php
```
You have 9 options for your PHP install.
1: Install PHP 5.2.17
2: Install PHP 5.3.29
3: Install PHP 5.4.45
4: Install PHP 5.5.38
5: Install PHP 5.6.40 (Default)
6: Install PHP 7.0.33
7: Install PHP 7.1.33
8: Install PHP 7.2.34
9: Install PHP 7.3.29
10: Install PHP 7.4.21
11: Install PHP 8.0.8
Enter your choice (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11): 9
```
选择9，Install PHP 7.3.29

- 安装内存分配器
```
1: Don't install Memory Allocator. (Default)
2: Install Jemalloc
3: Install TCMalloc
Enter your choice (1, 2 or 3): 1
```
选择 1，不安装

- 回车进行安装
```
Press any key to install...or Press Ctrl+c to cancel
```
安装时长取决于机器性能，本虚拟机配置为4核4G内存，安装时长大约15分钟。

- 运行环境安装成功后检查端口（80，3306）是否正常启动
```
netstat -lpnt
```
输出
```
Active Internet connections (only servers)
Proto Recv-Q Send-Q Local Address           Foreign Address         State       PID/Program name    
tcp        0      0 0.0.0.0:80              0.0.0.0:*               LISTEN      75582/nginx: master 
tcp        0      0 0.0.0.0:22              0.0.0.0:*               LISTEN      1073/sshd           
tcp6       0      0 :::3306                 :::*                    LISTEN      76156/mysqld        
tcp6       0      0 :::22                   :::*                    LISTEN      1073/sshd
```

- 或者浏览器直接访问 ip 地址
```
恭喜您，LNMP一键安装包安装成功！
```

- 解除 PHP 禁用函数
删除禁用函数：`proc_open`，`symlink`，`proc_get_status`
```
vi /usr/local/php/etc/php.ini
```
修改为：
```
disable_functions = passthru,exec,system,chroot,chgrp,chown,shell_exec,popen,ini_alter,ini_restore,dl,openlog,syslog,readlink,popepassthru,stream_socket_server
```

- 解除 NGINX open_basedir 限制
找到文件 /usr/local/nginx/conf/fastcgi.conf，注释以下：
```
#fastcgi_param PHP_ADMIN_VALUE "open_basedir=$document_root/:/tmp/:/proc/";
```

- 重启服务
```
lnmp restart
```

---------------------------
### 下面开始下载并安装 strongshop
- 创建目录
```
mkdir -p /www/wwwroot
cd /www/wwwroot
```

- 下载项目
    - 通过 Git 克隆
    ```
    #安装 git
    yum install git
    #安装 compsoer
    yum install composer
    #设置 composer 使用阿里云 composer 镜像
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
    #克隆项目
    git clone https://gitee.com/openstrong/strongshop.git
    #进入项目目录
    cd strongshop
    #安装 composer 包
    composer install
    ```
    > Tip
    > composer 包安装完毕后，检查 strongshop/vendor 目录，如果存在说明包安装成功，否则项目无法运行
    
    - 下载完整安装包（如果无法通过git完成下载和安装，可以使用此方式）
    <a href="/download" target="_blank">点击下载</a>

- 修改项目目录权限
```
chmod 777 -R /www/wwwroot/strongshop/bootstrap
chmod 777 -R /www/wwwroot/strongshop/storage
```

- 创建站点

```
[root@192 strongshop]# lnmp vhost add
+-------------------------------------------+
|    Manager for LNMP, Written by Licess    |
+-------------------------------------------+
|              https://lnmp.org             |
+-------------------------------------------+
Please enter domain(example: www.lnmp.org): www.strongshop.test
 Your domain: www.strongshop.test
Enter more domain name(example: lnmp.org *.lnmp.org): 
Please enter the directory for the domain: www.strongshop.test
Default directory: /home/wwwroot/www.strongshop.test: /www/wwwroot/strongshop/public
Virtual Host Directory: /www/wwwroot/strongshop/public
Allow Rewrite rule? (y/n) y
Please enter the rewrite of programme, 
wordpress,discuzx,typecho,thinkphp,laravel,codeigniter,yii2 rewrite was exist.
(Default rewrite: other): laravel
You choose rewrite: laravel
Enable PHP Pathinfo? (y/n) y
Enable pathinfo.
Allow access log? (y/n) y
Enter access log filename(Default:www.strongshop.test.log): 
You access log filename: www.strongshop.test.log
Create database and MySQL user with same name (y/n) 
Add SSL Certificate (y/n) n

Press any key to start create virtul host...

Create Virtul Host directory......
set permissions of Virtual Host directory......
You select the exist rewrite rule:/usr/local/nginx/conf/rewrite/laravel.conf
Test Nginx configure file......
nginx: the configuration file /usr/local/nginx/conf/nginx.conf syntax is ok
nginx: configuration file /usr/local/nginx/conf/nginx.conf test is successful
Reload Nginx......
Reload service php-fpm  done
================================================
Virtualhost infomation:
Your domain: www.strongshop.test
Home Directory: /www/wwwroot/strongshop/public
Rewrite: laravel
Enable log: yes
Create database: no
Create ftp account: no
================================================
```

- 删除文件 `.user.ini`
```
cd /www/wwwroot/strongshop/public
chattr -i .user.ini
rm -f .user.ini
```

- 进入安装页面 http://www.strongshop.local/install
 <hr/>
 <img style="max-width:500px;" src="/images/install01.jpg" />

> Tip
> 如果无法安装成功，请尝试 <a href="/wiki/installHand">手动安装</a>

- 安装成功
1. 访问站点首页 http://www.strongshop.test
2. 访问站点后台 http://www.strongshop.test/admin/home <br>
超级管理员：`admin` 密码：`123456`

- 配置计划任务（建议一定要配置此项，否则会产生日志爆满问题）
请查看  文档 - 功能&配置 - 计划任务