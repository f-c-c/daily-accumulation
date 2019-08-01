# quicklink

> Faster subsequent page-loads by prefetching in-viewport links during idle time
>
> 通过在空闲的时间预取视图内的链接来加快后续页面的访问速度

## quick link 是如何工作的

* 检测视图内的链接

* 等待浏览器空闲

  * ```javascript
    window.requestIdleCallback(callback[, options])
    ```

* 检测用户是否处于慢速网络

  * `navigator.connection.effectiveType`

* 预取`url`链接
## 用法
```javascript
npm install --save quicklink
import quicklink from "quicklink/dist/quicklink.mjs";
quicklink();
```