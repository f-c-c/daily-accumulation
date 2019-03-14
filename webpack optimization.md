# 19.03.02（webpack 优化、微前端）

## 微前端概念

（Micro Frontend）

Iframe 现存问题：

Webpack 有 chunked 的

微前端交付产物：3种方式

* 有静态资源又有node。 yog2 是基于fis的，比如：一个buy团队，一个 detail团队，两个团队是独立的，git地址都不一样
* ![image-20190302202138901](/Users/qitmac000469/Library/Application Support/typora-user-images/image-20190302202138901.png)

又可以独立部署又可以汇总到总的工程文件里面去

启动父容器，由父容器来管理，mpa多页

* 前端独立发包

## Web Components

可以看看 youtube 的页面，全是web components 原生的。以及css原生支持的变量。





**systemjs** 模块化机制 System.register. 组册机制，如果不是webpack的，就更好用了

跨团队组件

## web pack 优化（分开发层次 和 性能层次）
### 使用层次：

首先需要安装包: `npm i webpack -D` `npm i webpack-cli -D`

webpack只能理解JavaScript和JSON文件

### 优化点：
* cache-loader（能让性能提升3倍）
* Speed-measure-webpack-plugin(能检测哪个慢)
* 打包文件大小分析
* 深度treeshaking
* 开发阶段的优化
* Iterm2 报错提示

webpack 组件 Manifest.json + workbox（google的，流行的一批，是servicework的封装）



pwa quicklink + webpack 优化



1.treeshaking webpack-deep-scope-plugin webpack-parallel-uglify-plugin purifycss-webpack

* （web pack 只能做模块化的 tree shaking, webpack-deep-scope-plugin能分析到函数里面的类的作用域）
* webpack-parallel-uglify-plugin 
* purifycss-webpack -css 的 treeshaking(只适用于多页)

Tree Shaking 对于那些无副作用的模块也会生效了。  package.json 中 指定"sideEffects": false 例如：lodash-es

2.cache-loader 加快编译速度（解决loader慢的问题，和8 配合）

3.progress-bar-webpack-plugin 打包进度展示 

4.更友好的提示错误

friendly-errors-webpack-plugin

webpack-build-notifier 

5.set-iterm2-badge && node-bash-title 标题和窗口内容修改 

6.webpack-manifest-plugin 服务端生成性能配置文件 

7.happypack 多线程编译webpack 不支持的情况下使用thread-loader 

8.speed-measure-webpack-plugin 打包速度分析

9.prepack-webpack-plugin 代码求值 

10.splitchunks公用库的代码拆分 去除打包

11.使用动态 polyfill

<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=Map,Set"></script>

它会根据你的浏览器 UA 头，判断你是否支持某些特性，从而返回给你一个合适的 polyfill。

<script type="module" src="./main.js"></script>

<script nomodule src="main.es5.js"></script>

8.prerender-spa-plugin  渲染首屏 

9.html-webpack-inline-source-plugin 内部资源引入

10.inline-manifest-webpack-plugin 把runtime放到html里

11.webpack-dashboard 增强了 webpack 的输出，包含依赖的大小、进度和其他细节。

   webpack-bundle-analyzer  打包结果分析

   webpack --profile --json > stats.json

   14-1 <http://alexkuz.github.io/webpack-chart/>

   14-2 <http://webpack.github.io/analyse/>

12.多核压缩uglifyjs-webpack-plugin 官方维护 非官方维护webpack-parallel-uglify-plugin

13.cache-loader loader的缓存  => 'babel-loader?cacheDirectory=true'

exclude: /node_modules/, // 排除不处理的目录

  include: path.resolve(__dirname, 'src') // 精确指定要处理的目录 

14.合理的devtool 

​    eval： 生成代码 每个模块都被eval执行，并且存在@sourceURL

​    cheap-eval-source-map： 转换代码（行内） 每个模块被eval执行，并且sourcemap作为eval的一个dataurl

​    cheap-module-eval-source-map： 原始代码（只有行内） 同样道理，但是更高的质量和更低的性能

​    eval-source-map： 原始代码 同样道理，但是最高的质量和最低的性能

​    cheap-source-map： 转换代码（行内） 生成的sourcemap没有列映射，从loaders生成的sourcemap没有被使用

​    cheap-module-source-map： 原始代码（只有行内） 与上面一样除了每行特点的从loader中进行映射

​    source-map： 原始代码 最好的sourcemap质量有完整的结果，但是会很慢

19.移除无用的框架的代码的警告 optimization.nodeEnv: 'production'

20.集成到CI 监控文件的大小 <https://github.com/siddharthkp/bundlesize>

21.lodash-webpack-plugin 自动化去除无用代码 

[22.name-all-modules-plugin](/Users/qitmac000469/Library/Application Support/typora-user-images/55AD7DC8-8CFD-4161-90AE-3F36F7FB7145/22.name-all-modules-plugin) 保证chunkid不变

23.多入口解决方案 

resolve: {

​        alias: {

​            '@': path.resolve(__dirname, '../src')

​        }

}

minify: process.env.NODE_ENV === "development" ? false : {

​    removeComments: true, //移除HTML中的注释

​    collapseWhitespace: true, //折叠空白区域 也就是压缩代码

​    removeAttributeQuotes: true, //去除属性引用

}

24.babelrc 按需引用

babel-plugin-transform-imports 

babel-plugin-transform-modules（可以加载css）

import { Dialog } form 'cube-ui'

"plugins": [

  ["transform-modules", {

​     "cube-ui": {

​       "transform": "cube-ui/lib/${member}",

​       "preventFullImport": true,

​       "kebabCase": true,

​       "style": true

​     }

  }]

]

25.CSS公用提取

 styles: {

​     name: 'styles',

​     test: /\.css$/,

​     chunks: 'all',

​     enforce: true,

​     priority: 20,

}

26.vender

V8  图形学 js多线程





## 19.03.03

腾讯的 omi 看源码（）实现微前端的很有用的一个库

## pwa（二次访问加快）

Quick link  对资源预加载（一次访问加快）用流量换用户体验

* 在浏览器空闲的时候干事

* 可以根据在视窗范围内（的去链接）去预加载

* 网速类型判断（2g 3g就不加载）


dns prefetch
preconect
## pwa（可以断网，像app一样圆润）

断网、推送消息、发送通知、从桌面启动、免安装、

Service worker .做高速缓存，没有大小限制（把资源缓存到本地）

service worker 是web worker 的一种，所以不能在里面操作dom

http-server  起一个服务（断网，网站就访问不了了，单service worker可以离线缓存）

```
if("serviceWorker" in navigator) {
    //第二次生效，第一次是组册用的
    navigator.serviceWorder.register("sw.js")//组册
    .then(function(registration) {
        console.log("service worker 组册成功");
    })
    .catch(function() {
        console.log("service worker 组册失败");
    })
}
```

sw.js

```
var cacheName = "ydPWA-step-vi";//缓存版本的文件戳
//缓存的资源列表
var filesTocache = [
    "/scripts/index.js",
    "/images/timg.jpeg",
    "/scripts/test.js",
    "/",
    "/demo2.html"
];
//1.self 代表当前的 service worker作用域
//2.caches 表示service worker接管的页面
//3.skipWaiting 表示强制当前处在waitting的状态脚本 直接进入activate
//下面的可以拦截浏览器的所有请求
//安装阶段去定义怎么缓存文件
self.addEventListener("install", function() {
	console.log("安装成功");
    enent.waitUntil(updateStaticCache());
})
function updateStaticCache() {
    //对所有的静态资源进行缓存的过程
    return caches.open(cacheName)
    .then(function(cache) {
        return cache.addAll(filesTocache);
    })
    .then(() => {
        self.skipWaiting();
    })
}
//更新缓存的版本
self.addEventListener("activate", function() {
    event.waitUntil(caches.keys()
    	.then(function(keyList) {
            return Promise.all(keyList.map(function(key) {
                if(key !== cacheName){
                    return caches.delete(key);
                }
            }))
    	})
    )
})
self.addEventListener("fetch", function() {
    //event.responseWith(new Response("hello world"));
    event.respondWith(caches.match(event.request)
    .then(function(response) {
        return response || fetch(event.request);
    })
})
```

pwa的核心就是 service worker 

 Offline-plugin 

Worker-precache-webpack-plugin

Sw-precache-webpack-plugin

以上两个是解决文件 版本戳

LAVAS  百度推出的（看pwa的资料 文档很详细）

Google 的workbox 帮助我们更好的管理 service worker

[googlechrome](Https://github.com/googlechrome) 可以看见 google 发布的所有东西

workbox 还有 webpack 插件：workbox-webpack-plugin



## 浏览器js多线程

javascript 线程锁，共享内存视图

```
//postMessage 是属于 marcotask
const worder = new Worker("task.js");
//新建共享内存
const sharedArrayBuffer = new SharedArrayBuffer(Int32Array.RYTES_PER_ELEMENT)
worder.postMessage("")
//新建视图
const SharedArray = new Int32Array(SharedArrayBuffer);
for(let i = 0;i < 10; i++) {
    Atomics.store(sharedArray, i, i+1)
}
const itemValue = Atomics.load(sharedArray, 2);
const result = `yideng${itemValue}`
const queuePos = 1;
Atomics.store()
```

```
//sw.js
self.addEventListener("message", function(event) {
    //等待共享内存地址
    const sharedArrayBuffer = event.data;
    const SharedArray = new Int32Array(SharedArrayBuffer);
    //线程变量锁定🔒
    Atomics.wait(sharedArray, 2, 3);
    console.log("")
})
//只有子线程能🔒，主线程能解
```



sourcemap ： 浏览器的特性：知道



Rescuers hints

* Dns prefetch
* preconnect
* preload
* prerender