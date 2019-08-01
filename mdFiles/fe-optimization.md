# 前端性能优化

为什么要做性能优化？？？

这里有一些数据：

* 57%的⽤户更在乎网页在3秒内是否完成加载。
* 52%的在线用户认为网页打开速度影响到他们对网站的忠实度。
* 每慢1秒造成页面 PV 降低11%，用户满意度也随之降低16%。
* 近半数移动⽤用户因为在10秒内仍未打开页面从⽽放弃

性能优化就是在挽救我们的用户，就是在创收，恰恰是平时业务中容易忽略的一点

### 下面👇是一些基本的优化点：

* 铁律：合并http请求（5个）合并文件（不能大于28k）

* 多开 cdn 比如`i.meituan.com` 美团就开了 3 个cdn 比如 6 * 5 = 30

  * ```html
    <link href="//p1.meituan.net" rel="dns-prefetch">
    <link href="//p0.meituan.net" rel="dns-prefetch">
    <link href="//ms0.meituan.net" rel="dns-prefetch">
    ```

* 服务器开启 `gzip`， `http1.1 keep-alive` `http2` http2一旦开启 多开 cdn也就没有必要了，但是为了保证向下降级现在还是基本可以用 cdn 我们不能期望服务器都支持 http2
* 把  `js`写入一张图片
* 离线缓存 `localStorage` 可以把 `js` 放在本地里面，里面是字符串，拿出来后 `addScript` 或者 `evil`，这是一个很直接的方法减少请求数量，但是`localStorage` 空间也是比较小的，5M，不能随便的乱用，当然可以本地进行扩容
* 前端 ORM 存储方案 `localForage`

* 雅虎军规
* 缓存策略优先级非常重要 **cache-control > expires > Etag > Last Modify**，清楚过后才知道什么时候去设置 **强缓**，有针对性的和运维沟通优化策略，**业务的东西走离线缓存，库走http强缓（jquery vue react.min 字体库 这些东西）**，业务的东西我们可以用激活的js 去负责所有业务

![cache-priority](../assert/cache-priority.png)

* 根据用户网络类型（检测用户网络）（2g 3g等）提供不同的资源如（2g提供小的资源，网络好则提供高清资源），检测用户网络类型可以用 h5 天生的属性： connection.type 但是呢，这个东西浏览器的支持程度是不怎么好的，我们可以用图片去测试速度 ：速度 = 时间 / 文件大小
  * 这里介绍一种 多普勒 测试，分五次请求计算公式
  * 一旦我们通过各种方法得到了用户的带宽 网速，我们就可以根据网速干我们想干的事，比如网络不好就给一倍图，网络好就给2倍图，等等

上面说到了 http2 : 

浏览器请求//xx.cn/a.js-->解析域名—>HTTP连接—>服务器处理文件—>返回数据-->浏览器解析、渲染文件。Keep-Alive解决的核心问题就在此，一定时间内，同一域名多次请求数据，只建⽴一次HTTP请求，其他请求可复用每一次建立的连接通道，以达到提高请求效率的问题。一定时间是可以配置的，HTTP1.1还是存在效率问题，第一个:串行的⽂件传输。第二个:连接数过多。HTTP/2对同一域名下所有请求都是基于流，也就是说同⼀一域名不管访问多少文件，也只建⽴一路连接。同样Apache的最大连接数为300，因为有了这个新特性，最大的并发就可以提升到300，⽐比原来提升了了6倍!

![http2](../assert/http2.png)

### 渲染🀄️性能优化

浏览器 f12 里面有一个 `performance`表示性能的东西，在这里可以录制页面，可以看重排、重绘的时间

**重排（Rendering）**：摆积木就是重排，**重绘(Painting)**：往积木上面贴纸就是重绘，所以重排一定会引起重绘，但是重绘不一定引起重排

网页加载dom到绘制结束流程：

`gpu.js`可以专门做性能优化，让 js 代码跑在 gpu 里面，比跑在 cpu 里面更快

#### 例子一：下面👇的代码跑起来会不断的引起浏览器的重排和重绘，若网页中这种操作过于多则性能会降低，导致页面卡顿

* 我们利用chrome调试🔧录制10s的页面，可以看到我们的动画矩形在浏览器窗口里面运动，而且是**绿色的**，页面刚刷新的时候 `body` 是绿色的，后面就是这个矩形一直是绿色的，这说明了一个问题：页面刚开始刷新时 body 进行了一次重排和重绘，之后就是矩形一直在重排和重绘

![fe-f12-opt01](../assert/fe-f12-opt01.png)
从上图我们可以看出浏览器一直在进行：

**合成Layers->更新Layers->计算Style->回流Layout->重绘Paint**

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<device-width>, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>渲染中优化</title>
    <style>
        .container{
            position: relative;
        }
        .ball{
            width: 100px;
            height: 100px;
            border: 2px solid #f60;
            position: absolute;
            top:0;
            left: 0;
        }
        .ball-running {
            animation: run-aroud 4s infinite;
        }

        @keyframes run-aroud 
        {
            0% {
                top: 0;
                left: 0;
            }
            25% {
                top: 0;
                left: 200px;
            }
            50% {
                top: 200px;
                left: 200px;
            }
            75% {
                top:200px;
                left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="ball" class="ball">

        </div>
    </div>

    <script>
        let ball = document.getElementById('ball')
        ball.classList.add('ball-running');
    </script>
</body>

</html>
```

现在我们将代码做改动：

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<device-width>, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>渲染中优化</title>
    <style>
        .container{
            position: relative;
        }
        .ball{
            width: 100px;
            height: 100px;
            border: 2px solid #f60;
            position: absolute;
            top:0;
            left: 0;
        }
        .ball-running {
            animation: run-aroud 4s infinite;
        }

        @keyframes run-aroud 
        {
            0% {
                /* top: 0;
                left: 0; */
                transform: translate(0, 0);
            }
            25% {
                /* top: 0;
                left: 200px; */
                transform: translate(0, 200px);
            }
            50% {
                /* top: 200px;
                left: 200px; */
                transform: translate(200px, 200px);
            }
            75% {
                /* top:200px;
                left: 0; */
                transform: translate(200px, 0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="ball" class="ball">

        </div>
    </div>

    <script>
        let ball = document.getElementById('ball')
        ball.classList.add('ball-running');
    </script>
</body>

</html>
```

同样的，我们利用chrome调试🔧录制10s的页面，结果如下：

可以看出，我们修改后的代码已经**没有页面重排和重绘**的步骤了，这是因为采用了 gpu 的原因

![fe-f12-opt02](../assert/fe-f12-opt02.png)

下面的操作会导致元素单独成层，但是单独成层的不一定会让 gpu 参与 

绝对定位 transform 半透明 滤镜 canvas video overflow bfc

下面的会让gpu参与：

**css3d** **video webgl transform css滤镜**

下面是一些 关于渲染时的一些概念：

![some-concept](../assert/some-concept.png)

每一个网页的 FMP 都不是一样的，不同的业务可以去定义自己的FMP (对于我们的业务来讲，什么重要的东西展现出来了就是 FMP)

FP:仅有一个 div 根节点。 对应 vue 的 created

FCP 对应 mounted FMP 对应 updated

页面白屏就是因为处于FP FCP阶段，怎么去解决呢？做ssr, 在FP的时候就把内容灌入

#### 监控页面的 FP 和 FCP

```html
<body>
    <div class="container">
        <div id="ball" class="ball">
            qqq
        </div>
    </div>

    <script>
        let ball = document.getElementById('ball')
        ball.classList.add('ball-running');
        const observer = new PerformanceObserver((list) => {
            for (let entry of list.getEntries()) {
                console.log(entry)
            }
        })
        observer.observe({entryTypes: ["paint"]})
    </script>
</body>
```

上面代码如果没有内容 qqq 就只会触发 FP 而不会触发 FCP ,如果有内容 才会两个都触发,如下图：

![FP-FCP](../assert/FP-FCP.png)

#### 监控页面的 FMP

通过自己添加 mark，但是这种添加 mark 的方法已经侵入业务了，不是那么的好

```html
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<device-width>, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>渲染中优化</title>
    <style>
        .container{
            position: relative;
        }
        .ball{
            width: 100px;
            height: 100px;
            border: 2px solid #f60;
            position: absolute;
            top:0;
            left: 0;
        }
        .ball-running {
            animation: run-aroud 4s infinite;
        }

        @keyframes run-aroud 
        {
            0% {
                top: 0;
                left: 0;
                /* transform: translate(0, 0); */
            }
            25% {
                top: 0;
                left: 200px;
                /* transform: translate(0, 200px); */
            }
            50% {
                top: 200px;
                left: 200px;
                /* transform: translate(200px, 200px); */
            }
            75% {
                top:200px;
                left: 0;
                /* transform: translate(200px, 0); */
            }
        }
    </style>
    <script>
        performance.mark('css done');
    </script>
</head>

<body>
    <div class="container">
        <div id="ball" class="ball">
            qqq
            <script>
                    performance.mark('text done');
            </script>
        </div>
    </div>

    <script>
        let ball = document.getElementById('ball')
        ball.classList.add('ball-running');
        const observer = new PerformanceObserver((list) => {
            for (let entry of list.getEntries()) {
                console.log(entry)
            }
        })
        observer.observe({entryTypes: ["paint"]})


        let perfEntries = performance.getEntriesByType('mark')
        for (let entry of perfEntries) {
                console.log(entry)
        }
    </script>
</body>
```

#### 监控 longtask

任何在浏览器中执行超过 **50 ms** 的任务，都是 long task。

long task 会长时间占据主线程资源，进而阻碍了其他关键任务的执行/响应，造成页面卡顿。

```javascript
        const observer = new PerformanceObserver((list) => {
            for (let entry of list.getEntries()) {
                console.log(entry)
            }
        })
        observer.observe({entryTypes: ["paint","longtask"]})
				// 我们去构造一个 long task
        for(let i = 0; i< 1000000000; i++){

        }
```



![longtask](../assert/longtask.png)

#### 监控 TTI

`npm install tti-polyfill --save-dev`

```html
<body>
    <div class="container">
        <div id="ball" class="ball">
            qqq
            <script>
                    performance.mark('text done');
            </script>
        </div>
    </div>
    <script src="./node_modules/tti-polyfill/tti-polyfill.js"></script>
    <script>
        window.onload = function() {
            console.log('onload');
        }
        ttiPolyfill.getFirstConsistentlyInteractive().then((tti) => {
            // 统计数据
            // navigator.sendBeacon("");
            console.log(tti);//这里输出的是整个网页全部执行完毕的时间 单位为 s tti 是比 window.onload要晚的
        });
    </script>
</body>
```

#### 在系统空闲（Idle）时干活

requestIdleCallback + webwork. 在这个 idle 的时间段做什么事情嫩？ 做一点耗时认为，系统一点不会卡