# KOA2 源码分析

安装 koa `npm install --save koa`

可以看见两个核心的文件夹📁：`koa` 和 `koa-compose`

- koa
  - lib
    - application.js
    - context.js
    - request.js
    - response.js

- koa-compose
  - index.js

最简单的启动koa应用的代码：

```javascript
const Koa = require('koa');
const app = new Koa();

app.use(async (ctx, next) => {
  console.log("1");
  await next();
  console.log("2");
});
app.use(async (ctx, next) => {
  console.log("3");
  ctx.body = 'Hello World';
  await next();
  console.log("4");
});

app.listen(3003);
```

跟随这个 `app.use`方法，我们打开 `./node_modules/koa/lib/application.js`找到对于的 `use` 方法：

`use`只是简单的将 函数 放到了 `this.middleware` 里面，`app.use` 就干了这么一件事

```javascript
  use(fn) {
    this.middleware.push(fn);
    return this;
  }
```

我们再看 `app.listen(3003)`：

直接调用的原生的 `http` 模块

```javascript
  listen(...args) {
    const server = http.createServer(this.callback());
    return server.listen(...args);
  }
```

再看 `this.callback`:

`this.callback` 调用了`compose` 传入了所有的中间件，返回了一个函数 `handleRequest`,因为原生的 `http.createServer`就需要传入一个 回调（请求进来了会调用这个函数）

```javascript
  callback() {
    const fn = compose(this.middleware);
    const handleRequest = (req, res) => {
      const ctx = this.createContext(req, res);
      return this.handleRequest(ctx, fn);
    };
    return handleRequest;
  }
```

这个时候我们跟踪到了 `./node_modules/koa-compose/index.js`:

```javascript
'use strict'
module.exports = compose
function compose (middleware) {
  return function (context, next) {
    let index = -1
    return dispatch(0)
    function dispatch (i) {
      if (i <= index) return Promise.reject(new Error('next() called multiple times'))
      index = i
      let fn = middleware[i]
      if (i === middleware.length) fn = next
      if (!fn) return Promise.resolve()
      try {
        return Promise.resolve(fn(context, dispatch.bind(null, i + 1)));
      } catch (err) {
        return Promise.reject(err)
      }
    }
  }
}
```

具体分析如下图：

![](../assert/koa-source.jpg)

