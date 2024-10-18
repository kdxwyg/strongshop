> Gitee 仓库地址：<a href="https://gitee.com/openstrong/strongshop" target="_blank">https://gitee.com/openstrong/strongshop</a>

> 这里以 PhpStudy 集成环境为例，下载安装 PhpStudy：<a href="http://www.xp.cn" target="_blank">http://www.xp.cn</a>
> Web环境要求： `MySql 5.7+`,`Nginx 1.10+`,`PHP >= 7.2.5`

- 下载项目
    - 通过 Git 克隆（推荐）
    1. 安装 Git <a href="https://git-scm.com/download/win" target="_blank">https://git-scm.com/download/win</a>
    2. 安装 Composer <a href="https://getcomposer.org/download" target="_blank">https://getcomposer.org/download</a>
    ```
    #设置 composer 使用阿里云 composer 镜像
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
    ```
    > Tip
    > 安装 composer 时请选择 >=php7.2.5 的 exe 执行程序

    ```
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

- 创建网站
<hr/>
<img style="max-width:650px;" src="/images/install02.jpg" />
> Tip
> 项目运行目录为 strongshop/public
> 请勾选 同步hosts，如果 hosts 因为本地电脑权限问题未同步成功，请手动把 `127.0.0.1 www.strongshop.local` 放入 HOSTS 文件内

- 伪静态 （必须配置，否则无法访问）

    - Nginx 伪静态
    ```
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    ```

    - Apache 伪静态
    ```
        Options +FollowSymLinks -Indexes
        RewriteEngine On

        RewriteCond %{HTTP:Authorization} .
        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    ```

- 进入安装页面 http://www.strongshop.local

<hr/>
 <img style="max-width:500px;" src="/images/install01.jpg" />

> Tip
> 如果无法安装成功，请尝试 <a href="/wiki/installHand">手动安装</a>

- 安装成功
1. 访问站点首页 http://www.strongshop.local
2. 访问站点后台 http://www.strongshop.local/admin/home <br>
超级管理员：`admin` 密码：`123456`


