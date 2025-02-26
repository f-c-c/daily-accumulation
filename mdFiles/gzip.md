前端生产环境中将js、css、图片等文件进行压缩的好处显而易见，通过减少数据传输量减小传输时间，节省服务器网络带宽，提高前端性能。

前端自己打包压缩的有grunt，gulp，webpack，这些压缩也很重要，基本上能压缩50%以上

**gzip能在压缩的基础上再进行压缩50%以上！！！** 压缩通常能减少响应70%左右的大小

### 浏览器如何查看资源是否启用了gzip压缩

`Cmd + alt + i` 打开浏览器开发工具->切换到`Network`->在Name\Status\Type这个header区域右键选中`Response Headers`下面的 `Content-Encoding`，这个时候在header区域就会多一栏目（Content-Encoding）,指定了资源是否是经过了压缩的以及是什么压缩方法

### 哪些资源需要被gzip压缩

scripts脚本和stylesheet样式文件是值得 压缩的，图片和PDF文件不应该被压缩，因为他们已经是压缩的了，试着压缩他们会浪费CPU资源而且可能潜在增加文件大小。

压缩有一项成本:它会带来额外的服务器端压缩和客户端解压缩的CPU资源。为了权衡利弊，你需要去考虑响应的大小，网络的带宽，以及服务器和浏览器之间的网络距离等因素，这些信息通常是不容易获取的。所以，一般对于超过1k或者2k的文件都是值得去压缩的。

### 什么时候压缩

| 压缩时机             | 流程                                                         | 说明                                                         |
| -------------------- | ------------------------------------------------------------ | ------------------------------------------------------------ |
| 服务端响应请求时压缩 | 服务端接收到请求后->找到响应文件->压缩->将压缩后的文件作为内容返回给客户端 | 压缩等级越高效果越好，同时 cpu 的消耗也越大，压缩时间也越长，so，为了不违背减少响应时间这个最终目的，服务端响应请求时压缩等级不宜过高 |
| 构建时压缩           | 在项目构建时，生成压缩文件->发布时是带压缩文件的             | 如通过 `webpack` 在打包时压缩，这不会占用响应时间，可以现在较高的压缩等级，将生成压缩后的静态资源一起部署到服务器 |
| 结合使用             | 请求根据文件类型，大小，等对压缩做策略选择                   | 构建时对需要压缩的文件进行压缩，服务器端在收到请求后先查找对应的已压缩文件是否存在，不存在才使用服务器端压缩 |

### http协议如何支持压缩文件的传输

* 1、浏览器请求数据时，通过Accept-Encoding说明自己可以接受的压缩方法
  * Accept-Encoding: gzip, deflate, br

* 2、服务端接收到请求后，选取Accept-Encoding中的一种对响应数据进行压缩

* 3、服务端返回响应数据时，在Content-Encoding字段中说明数据的压缩方式
  * Content-Encoding: gzip

* 4、浏览器接收到响应数据后根据Content-Encoding对结果进行解压

* 注：如果服务器没有对响应数据进行压缩，则不返回Content-Encoding，浏览器也不进行解压

### 代理缓存

当浏览器发送经过代理的请求，情况相对会变得复杂。假设来自不支持gzip的浏览器发起第一次请求到代理服务器，因为是第一次请求，所以代理服务器的缓存是空的，代理把请求转发给web服务器，web服务器作一个未压缩的响应。这个未压缩的响应被代理缓存并且发送给浏览器。现在，假第二个支持gzip压缩的请求发送到代理，代理从缓存中取出未压缩的数据作为响应，这样就丢失了使用gzip的能力。

更糟糕的是，如果第一次请求来自支持gzip的浏览器而第二次请求来自一个不支持的浏览器，这种情况下，代理缓存中有一个内容的压缩版本并且为不管是否支持gzip压缩的浏览器都提供服务。

解决这个问题的方法是在你的web服务响应头中添加一个Vary header字段，web服务器通知代理基于一个或多个请求头来做不同的缓存响应。因为是否压缩是基于Accept-Encoding头，所有有理由在web服务器的响应的vary头字段中包含Accept-Encoding

```
Vary: Accept-Encoding
```

这会导致代理会基于请求头Accept-Encoding字段的每个一个值缓存不同的响应内容版本。在我们之前的例子中，代理将缓存两个每个响应的两个版本:当Accept-Encoding是gzip时的压缩内容，和Accept-Encoding完全没有指定时的非压缩版本。当浏览器发起一个带Accept-Encoding：gzip的请求到代理，代理会取缓存中压缩的版本并作为响应到浏览器，如果没有Accept-Encoding:gzip头，浏览器将受到未压缩的版本。

### 如何开启gzip压缩

> 启用gzip需要客户端和服务端的支持，如果客户端支持gzip的解析，那么只要服务端能够返回gzip的文件就可以启用gzip了，现在来说一下几种不同的环境下的服务端如何配置

#### node端(待⌛️实践)

node端很简单，只要加上compress模块即可，代码如下

```
var compression = require('compression')
var app = express();

//尽量在其他中间件前使用compression
app.use(compression());
```

这是基本用法，如果还要对请求进行过滤的话，还要加上

```
app.use(compression({filter: shouldCompress}))

function shouldCompress (req, res) {
  if (req.headers['x-no-compression']) {
    // 这里就过滤掉了请求头包含'x-no-compression'
    return false
  }

  return compression.filter(req, res)
}
```

更多用法请移步[compression文档](https://github.com/expressjs/compression)
如果用的是koa，用法和上面的差不多

```
const compress = require('koa-compress');
const app = module.exports = new Koa();
app.use(compress());
```

因为node读取的是生成目录中的文件，所以要先用webpack等其他工具进行压缩成gzip，webpack的配置如下

```
const CompressionWebpackPlugin = require('compression-webpack-plugin');
plugins.push(
    new CompressionWebpackPlugin({
        asset: '[path].gz[query]',// 目标文件名
        algorithm: 'gzip',// 使用gzip压缩
        test: new RegExp(
            '\\.(js|css)$' // 压缩 js 与 css
        ),
        threshold: 10240,// 资源文件大于10240B=10kB时会被压缩
        minRatio: 0.8 // 最小压缩比达到0.8时才会被压缩
    })
);
```

plugins是webpack的插件

#### nginx

gzip使用环境:http,server,location,if(x),一般把它定义在nginx.conf的http{…..}之间

- **gzip on**
  on为启用，off为关闭
- **gzip_min_length 1k**
  设置允许压缩的页面最小字节数，页面字节数从header头中的Content-Length中进行获取。默认值是0，不管页面多大都压缩。建议设置成大于1k的字节数，小于1k可能会越压越大。
- **gzip_buffers 4 16k**
  获取多少内存用于缓存压缩结果，‘4 16k’表示以16k*4为单位获得
- **gzip_comp_level 5**
  gzip压缩比（1~9），越小压缩效果越差，但是越大处理越慢，所以一般取中间值;
- **gzip_types text/plain application/x-javascript text/css application/xml text/javascript application/x-httpd-php**
  对特定的MIME类型生效,其中'text/html’被系统强制启用
- **gzip_http_version 1.1**
  识别http协议的版本,早起浏览器可能不支持gzip自解压,用户会看到乱码
- **gzip_vary on**
  启用应答头"Vary: Accept-Encoding"
- **gzip_proxied off**
  nginx做为反向代理时启用,off(关闭所有代理结果的数据的压缩),expired(启用压缩,如果header头中包括"Expires"头信息),no-cache(启用压缩,header头中包含"Cache-Control:no-cache"),no-store(启用压缩,header头中包含"Cache-Control:no-store"),private(启用压缩,header头中包含"Cache-Control:private"),no_last_modefied(启用压缩,header头中不包含"Last-Modified"),no_etag(启用压缩,如果header头中不包含"Etag"头信息),auth(启用压缩,如果header头中包含"Authorization"头信息)
- **gzip_disable msie6**
  (IE5.5和IE6 SP1使用msie6参数来禁止gzip压缩 )指定哪些不需要gzip压缩的浏览器(将和User-Agents进行匹配),依赖于PCRE库