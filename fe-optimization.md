# 前端性能优化

为什么要做性能优化？？？

这里有一些数据：

* 57%的⽤用户更更在乎⽹网⻚页在3秒内是否完成加载。
* 52%的在线⽤用户认为⽹网⻚页打开速度影响到他们对⽹网站的忠实度。
* 每慢1秒造成⻚页⾯面 PV 降低11%，⽤用户满意度也随之降低降低16%。
* 近半数移动⽤用户因为在10秒内仍未打开⻚页⾯面从⽽而放弃

性能优化就是在挽救我们的用户，就是在创收，恰恰是平时业务中容易忽略的一点

下面👇是一些优化点：

* 铁律：合并http请求（5个）合并文件（不能大于28k）

* 多开 cdn 比如`i.meituan.com` 美团就开了 3 个cdn

  * ```html
    <link href="//p1.meituan.net" rel="dns-prefetch">
    <link href="//p0.meituan.net" rel="dns-prefetch">
    <link href="//ms0.meituan.net" rel="dns-prefetch">
    ```

* 服务器开启 `gzip`，`keep-alive` `http2`
* 把  `js`写入一张图片
* 离线缓存 `localStorage` 可以把 `js` 放在本地里面，里面是字符串，拿出来后 `addScript` 或者 `evil`，这是一个很直接的方法减少请求数量，但是`localStorage` 空间也是比较小的，5M，不能随便的乱用，当然可以本地进行扩容
* 前端 ORM 存储方案 `localForage`

* 雅虎军规
* 缓存策略优先级非常重要 **cache-control > expires > Etag > Last Modify**，清楚过后才知道什么时候去设置 **强缓**，有针对性的和运维沟通优化策略

![cache-priority](./assert/cache-priority.png)