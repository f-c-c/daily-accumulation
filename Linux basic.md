# Linux basic 

### `windows` 终端

* `putty`
* `cmder`(强烈推荐)，超级终端，相当于给 `windows` 加了一个 `shell` 安装的时候安装full版本，安装后就可以在 `windows`、下像 `mac` 和 `Linux`、 一样的用了
* `Xshell` 安装教育学生版本

### `Mac` 终端

* 安装 `iterm2` 

### 远程登录命令

* `ssh`
*  比如：`ssh root@xxx.xxx.xxx.xxx`,会提升输入密码，这个时候的秘密是不回显的
### 修改 `hostname`
* 可以帮助我们区别我们究竟在哪个地方：是在哪一个服务器，是服务器还是本地
* `hostnamectl` 什么参数都不加，就会显示该主机的一些信息
* `hostnamectl -h` 就会列出参数选项
* `hostnamectl set-hostname xxx` 就可以改主机名了，可以带下划线和中划线，改完之后需要退出终端从新登陆才会生效 ，通过 `exit` 就可以退出了
### 常用 `Linux` 命令
* `vi` 行编辑器，`i` 进入编辑状态，`esc` 退出编辑状态进入命令状态，`:wq` 保存退出 ，`:q!` 放弃修改并退出，`:w` 只保存不退出 加上 `sudo` 提升权限
  * 在 `vi` 里面 编辑的时候是不是很方便的，鼠标时不管用的
* 服务管理命令：`systemctl`
  * 直接输入 `systemctl` 命令，会列出在这台机器上面的所有服务，这个时候 按下 `q` 就退出列表状态
  * 比如启动 `mysql` 服务：`systemctl start mysqld`
  * 启动`apache` 服务： `systemctl start httpd`
  * 启动 `nginx` 服务：`systemctl start nginx`
  * `systemctl`的子命令： `restart` 是 重启， `stop` 是停止，`disable` 禁用服务（随着操作系统启动时不会启动），`enable` 启用服务（随着操作系统启动就会启动），`disable  enable` 是不影响 `start stop restart`的
  * 可以发现：有些进程名字后面带个 `d`，这是守护进程的意思

* 命令行下载命令
  * `curl` 比如`curl http://www.baidu.com` 不会下载，只会显示内容，要下载必须指定输出文件名：`curl http://www.baidu.com -o xxx.index`
  * `wget` 比如去下载百度的首页： `wget http://www.baidu.com`
  * `wget` 一般下载就够用了，但是 `curl`很强大，可以用来调试协议请求等，可以`curl -h`查看帮助