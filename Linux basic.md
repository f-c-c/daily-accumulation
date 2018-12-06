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
* `vi` 行编辑器，`i` 进入编辑状态，`esc` 退出编辑状态进入命令状态，`:wq` 保存退出 ，`:q!` 放弃修改退出，`:w` 只保存不退出
