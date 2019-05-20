# 服务器日常问题总结

### 1. 长时间未远程登陆远程vps，登陆不上并报以下错误：
  ```shell
  ssh: connect to host 192.168.0.200 port 22: Operation timed out
  ```

`vi /etc/ssh/sshd_config`  #MaxStartups 10，#去掉，修改10为1000，MaxStartups 1000

在连接远程vps的时候经常调，我们可以设置每隔多长时间向服务器发一个保持连接的请求 `-o ServerAliveInterval=30`

`sudo ssh -i "amsterdam_rsa" -o ServerAliveInterval=30 root@xxx.xxx.xxx.xxx`

`ssh -o ServerAliveInterval=30 root@xxx.xxx.xxx.xxx`

重启 `sshd` 服务 `systemctl restart sshd_service`

### 2.有用的一些命令

* 修改服务器的root密码：`passwd` 或者是：`sudo passwd root`
* 往服务器上传压缩文件：`scp course-map.json root@ip:/路径`

### 3.前后台切换进程

- 在 Linux 的前台运行一个进程 `node server.js`
- 在 Linux 的后台运行一个进程 `node server.js &`
- 使用 `ctrl + z` 将强制当前进程转为后台，并使之挂起（暂停）
- `jobs` 显示当前会话终端运行在后台的进程
- `bg %N` 使第 N 个任务在后台运行
- `fg %N` 使第 N 个任务在前台运行
- `ctrl + c` 终止当前 前台 正在执行的程序
- `ctrl + d` 相当于 `exit` 退出会话
- `ps -e` 可以查看进程如 node 的进程 PID
- `kill -9 PID` 杀掉进程
- 后台进程的终止可以 `kill %num`

### 4.Centos 7安装Nginx

* 对于一台干净的 服务器（刚安装的系统没有其他的任何操作），首先**gcc 安装**，安装 nginx 需要先将官网下载的源码进行编译，编译依赖 gcc 环境，如果没有 gcc 环境，则需要安装：`yum install gcc-c++`

* **PCRE pcre-devel 安装** `yum install -y pcre pcre-devel`PCRE(Perl Compatible Regular Expressions) 是一个Perl库，包括 perl 兼容的正则表达式库。nginx 的 http 模块使用 pcre 来解析正则表达式，所以需要在 linux 上安装 pcre 库，pcre-devel 是使用 pcre 开发的一个二次开发库。nginx也需要此库。

* **zlib 安装** `yum install -y zlib zlib-devel`zlib 库提供了很多种压缩和解压缩的方式， nginx 使用 zlib 对 http 包的内容进行 gzip ，所以需要在 Centos 上安装 zlib 库。

* **OpenSSL 安装** `yum install -y openssl openssl-devel`OpenSSL 是一个强大的安全套接字层密码库，囊括主要的密码算法、常用的密钥和证书封装管理功能及 SSL 协议，并提供丰富的应用程序供测试或其它目的使用。
  nginx 不仅支持 http 协议，还支持 https（即在ssl协议上传输http），所以需要在 Centos 安装 OpenSSL 库。
  
* 使用`wget`命令下载 nginx `wget -c https://nginx.org/download/nginx-1.10.1.tar.gz`，（这里当前在哪个目录就是下载到哪个目录）

* 解压

* ```shell
  tar -zxvf nginx-1.10.1.tar.gz
  cd nginx-1.10.1
  ```

* 使用默认配置 `./configure`

* 编译安装：

* ```shell
  make
  make install
  ```

* 查找安装路径：`whereis nginx`安装好nginx后 `/usr/local/nginx`下面有 4 个目录： `conf` `html` `sbin` `logs` 其意义也很明显了，在 conf 目录下是其配置文件，一般配置 `nginx.conf` 在 logs 目录下是其log文件，所有访问的日志都在这里面，这比node打的日志还要全，node的是项目里面的日志，而这里是网络入口的所有日志

* 启动停止 nginx

* ```shell
  cd /usr/local/nginx/sbin/   //进入到这个目录再执行下面的命令
  ./nginx   // 启动 nginx
  ./nginx -s stop   // 停止 nginx
  ./nginx -s quit   // 停止 nginx
  ./nginx -s reload   // 重启 nginx
  ```

* ```shell
  ./nginx -s quit:此方式停止步骤是待nginx进程处理任务完毕进行停止。
  ./nginx -s stop:此方式相当于先查出nginx进程id再使用kill命令强制杀掉进程。
  ```

* 查询nginx进程 `ps aux|grep nginx` 或者 查看是否成功     `ps -ef | grep nginx` (如果能看到两个相邻ID的进程，说明启动成功)

* 重启nginx :
  * 1.先停止再启动（推荐）：
    对 nginx 进行重启相当于先停止再启动，即先执行停止命令再执行启动命令。如下：

    ```shell
    ./nginx -s quit
    ./nginx
    ```
  
  * 2.重新加载配置文件：
    当 ngin x的配置文件 nginx.conf 修改后，要想让配置生效需要重启 nginx，使用`-s reload`不用先停止 ngin x再启动 nginx 即可将配置信息在 nginx 中生效，如下：`./nginx -s reload`

* 配置防火墙，永久打开 80端口 [命令这里有](http://love.haozigege.com/2017/12/12/over-the-wall-with-ss/#安装并配置防火墙（开启对应端口）)

* 这是在浏览器打入 ip地址一个能访问到欢迎页面了 `Welcome to nginx!`

* Nginx 的安装目录 `/usr/local/nginx`

### 5.Centos 7 源码安装 Node.js(没有成功过，好想是因为gcc版本低)

* 1.安装gcc，make，openssl，wget `yum install -y gcc make gcc-c++ openssl-devel wget`
* yum -y install gcc gcc-c++ kernel-devel make openssl-devel wget

* 下载源代码包 `wget https://nodejs.org/dist/v10.15.3/node-v10.15.3.tar.gz` ,源码可以在 node.js 官网上找到链接
* 3.解压源代码包 `tar -xf node-vx.x.x.tar.gz`

* 4.编译 进入源代码所在路径 `cd node-vx.x.x` 先执行配置脚本 `./configure` 编译与部署 `make && make install` 接着就是等待编译完成…
* 5.测试： `node -v` `npm -v`
* ⚠️： 我linux的gcc版本低，升级花好长时间都不成功导致 源码安装node没有成功过

 ### 6.Centos 7 安装 git 客户端

* `yum install -y git`

* ```
  git config --global user.name "你的名字"
  git config --global user.email "你的邮箱"
  ssh-keygen -t rsa -C "你的邮箱"
  ```

* 添加公钥到 github

* 就可以 从 github 克隆代码了，接着`npm i` 启动项目

### 7.Centos 7 安装 Node

* `curl -sL https://rpm.nodesource.com/setup_10.x | sudo bash -`
* `sudo yum install nodejs`
* `node --version`
* `npm --version`
* 参考 [这里](https://linux4one.com/how-to-install-node-js-with-npm-on-centos-7/)

### 8.Centos 7 安装 jenkins

`Jenkins`依赖`Java`，如果你的系统没有安装的话，需要先安装`Java`，已安装的话，可以忽略。使用以下命令

```
yum install java
java -version
```

安装`jenkins`

```
sudo wget -O /etc/yum.repos.d/jenkins.repo https://pkg.jenkins.io/redhat/jenkins.repo
sudo rpm --import https://pkg.jenkins.io/redhat/jenkins.io.key
yum install jenkins
```

启动 `systemctl start jenkins`

记得防火墙打开 `8080` 并且重启防火墙

```
firewall-cmd --zone=public --add-port=8080/tcp --permanent
firewall-cmd --reload
```

打开浏览器输入ip:8080 然后输入密码，再安装推荐插件

`admin`    ` /var/lib/jenkins/secrets/` `2d92d38abc124de6a52be96cbd94645b`

这里有登陆密码和凭证