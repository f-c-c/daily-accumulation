#  Nginx 反向代理与负载均衡

Nginx 是一个轻量的服务器，虽然轻量但是能够承载大量的并发，一般很少用node去直接作为服务器给用户去访问，一般前面用 nginx 挡一层。`brew search nginx` mac 

## 什么是反向代理与负载均衡

* 正向代理：比如我们自己的计算机A想访问国外的网站B，访问不了，这时有一台中间服务器C可以访问到B，那么我们可以用自己的电脑💻访问服务器C，通过C来访问B，这个时候，服务器C就被称为代理服务器，这种访问方式叫做正向代理
* 反向代理：代理服务器来接受internet上的连接请求，比如我们有一个服务器集群，并且集群中的每台服务器的内容是一样的，同样我们不能直接访问服务器集群中的服务器，这是也有一个第三方服务器，能够访问集群，这时我们通过第三方服务器去访问集群的内容，但是此时我们**并不知道是哪一台服务器提供的内容**，这种代理方式称为反向代理
* 负载均衡：当一台服务器的单位时间内的访问量越大的时候，服务器的压力也会越大。当一台服务器压力大🉐️超过了自身承受能力的时候，服务器就会崩溃。为了避免服务器的崩溃，我们通常通过负载均衡的方式来分担服务器的压力。我们可以建立很多很多的服务器，这些服务器组成一个服务器集群，然后，当用户访问我们的网站的时候，先访问一个中间服务器，再让这个中间服务器在服务器集群中选择一个压力较小的服务器，然后再将访问请求引入该选择的服务器。这样，用户每次访问，都会保证服务器集群中的每个服务器压力趋于平衡，分担了服务器压力，避免了服务器崩溃的情况

## Nginx负载均衡的实现

Nginx是一款可以通过反向代理实现负载均衡的服务器，使用Nginx服务实现负载均衡的时候，用户的访问首先会访问到Nginx服务器，然后Nginx服务器再从服务器集群表中选择压力较小的服务器，然后将该访问请求引向该服务器。若服务器集群中的某个服务器崩溃，那么从待选服务器列表中将该服务器删除，也就是说一个服务器加入崩溃了，那么Nginx就肯定不会将访问请求引入该服务器了。

## HTTP Upstream模块

* 什么是HTTP Upstream模块：
  * Upstream 模块是Nginx服务器的一个重要模块。Upstream模块实现在轮询和客户端ip之间实现后端的负载均衡。常用的指令有ip_hash指令、server指令和upstream指令等
* ip_hash指令
  * 能让同一个用户每次落在同一台服务器
* server指令
  * 设置服务器权重
* Upstream指令及相关变量

## nginx.conf 配置文件

```nginx
worker_processes  1;
events{
    worker_connections  1024;
}

http{
		upstream test{
			ip_hash;
			server xxx.xxx.xxx.xxx:80 weight=2;
			server xxx.xxx.xxx.xxx:8080;
		}
		server{
			listem 80;
			location / {
				proxy_pass http://test;
			}
		}
}
```

带有 `ip_hash;`作用： 让用户第一次落在了某个服务器后，以后都落在这个服务器

带有`weight`作用：权重

## 部署NodeJs上线步骤及nginx相关命令
1. 打开`https://brew.sh/index_zh-cn.html`
2. `brew search nginx ` `brew install nginx`
3. `brew info nginx`
4. `nginx -v`查看`nginx`信息
5. 启动 `sudo brew services start nginx` (默认端口8080)，或者直接 `nginx`也能启动Mac下 可能弹一个允许的框
6. `sudo brew services stop nginx/nginx` 或者 `nginx -s stop` 停止`nginx`
7. `nginx -s reload` `nginx -s stop`
8. 打开 `nginx`具体安装目录，查看配置文件： `/usr/local/etc/nginx/`，在配置文件里面可以配置`gzip` ``e-tag` 等性能优化参数
9. 验证配置文件 `nginx -t -c 自己的配置文件地址`
10. 拷贝配置文件到 `Node` 项目目录 重新修改
11. 服务器端的 `nginx` 地址

