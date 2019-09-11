# 容错

### 每一个<script></script>是独立的

下面👇 `console.log('1');` 不会被执行，`console.log('2');`会被执行，这两个`script`是**独立的**

```javascript
    <script>
        error
        console.log('1');
    </script>
    <script>
        console.log('2');
    </script>
```

### try {} catch () {}

下面👇的运行时错误`try {} catch () {}`是能捕获到的，会输出

```
知道错误了
ReferenceError: error is not defined
    at text.html:21
over
```

```javascript
    <script>
        // 运行时错误
        try {
            error //未定义变量
        } catch (e) {
            console.log('知道错误了');
            console.log(e);
        }
        console.log('over');
    </script>
```

语法错误是`catch`不住的，浏览器会直接爆红 `Uncaught SyntaxError: Invalid or unexpected token`，直接挂掉，后面的 `over`也不会执行

```javascript
    <script>
        try {
            let error = 'error'； //语法错误(中文分号)
        } catch (e) {
            console.log('这里感知不到');
            console.log(e);
        }
				console.log('over');
    </script>
```

下面👇的异步错误 也是`catch`不住的 会输出： 

```
over
text.html:21 Uncaught ReferenceError: error is not defined
    at text.html:21
```

```javascript
    <script>
        try {
            setTimeout(() => {
                error //异步错误
            })
        } catch (e) {
            console.log('我也感知不到');
            console.log(e);
        }
        console.log('over');
    </script>
```

结论： `try {} catch () {}` 能力有限，只能捕捉到 **运行时非异步错误**，对于语法错误和异步错误就显得无能为力，捕捉不到。

### window.onerror

> `window.onerror` 捕捉异常能力稍微比 `try {} catch () {}` 强一些，无论是异步还是非异步，`window.onerror`都能捕捉到**运行时错误**

`window.onerror` 捕获异步运行时错误：

```javascript
    <script>
        window.onerror = function(msg, url, row, col, error) {
            console.log('知道错额');
            console.log({
                msg,url,row,col,error
            });
            return true;
        }
        setTimeout(() => {
            error //异步错误
        })
        console.log('over');
    </script>
```

页面不会爆红，会输出：

 ```
over
知道错额
{msg,col,error,row,url}
 ```


结论：

* `window.onerror`主要是用来捕获预料之外的错误，而`try {} catch () {}`则是用来在可预见的情况下捕获错误
* `window.onerror` 只有在 返回 `true`的情况下，异常才不会向上抛出

* 当我们遇到`<img src="./404.png" />`报 `404`网络请求异常的时候，我们用 `window.onerror`可以捕获到网络请求异常，但是无法判断 `http` 的状态`400` 或者 `500`，所以还需要配合服务器端日志排查分析

**网络请求异常不会事件冒泡**，但是下面的🐱能捕获到 404 ：

```javascript
<body>
    <img src="./404.png" alt='' />
    <script>
        window.addEventListener('error', (msg,url,row,col,error) => {
            console.log('我知道错误了');
            return true;
        }, true)  // 这个 true  很关键，只有为true才捕捉得到
				// 下面不会捕获到
 				// window.onerror = (msg,url,row,col,error) => {
        //     console.log('我知道错误了');
        //     return true;
        // };
    </script>
</body>
```

如果 `404` 了给吐一张🐶可爱的图片

### 捕获 promise 错误

为了避免每一个都去 `catch`

```javascript
<body>
    <script>
        window.addEventListener('unhandledrejection', (e) => {
            e.preventDefault();
            console.log('我知道 promise 错误了');
            console.log(e.reason);
            return true;
        }, false); // 如果不捕获的话，会爆红的
        Promise.reject('promise error').catch((err)=>{
            console.log(err);
        })
        new Promise((resolve, reject) => {
            reject('promise error');
        }).catch((err)=>{
            console.log(err);
        })
        new Promise((resolve) => {
            resolve();
        }).then(() => {
            throw 'promise error'
        });
        new Promise((resolve, reject) => {
            reject(123);
        })
    </script>
</body>
```


### 遇到跨域的就需要用 node 设置 头

```javascript
        const Koa = require('koa');
        const path = require('path');
        const cors = require('koa-cors');
        const app = new Koa();
        app.use(cors());
        app.use(require('koa-static')(path.resolve(__dirname, './public')));
        app.listen(8081, () => {
            console.log('koa app listening at 8081');
        })

        <script>
        window.onerror = function(msg, url, row, col, error) {
            console.log('我知道错误了，也知道错误信息');
            console.log({
                msg,
                url,
                row,
                col,
                error
            })
            return true;
        };
        </script>
        <script src="http://localhost:8081/test.js" crossorigin></script>
        // http://localhost:8081/test.js setTimeout(() => { console.log(error); }); //后端的node
        <script>
        const Koa = require('koa');
        const path = require('path');
        const cors = require('koa-cors');
        const app = new Koa();

        app.use(cors());
        app.use(require('koa-static')(path.resolve(__dirname, './public')));

        app.listen(8081, () => {
            console.log('koa app listening at 8081')
        });
        </script>
```

### ifram

```javascript
<iframe src="./iframe.html" frameborder="0"></iframe>
<script>
  window.frames[0].onerror = function (msg, url, row, col, error) {
    console.log('我知道 iframe 的错误了，也知道错误信息');
    console.log({
      msg,  url,  row, col, error
    })
    return true;
  };
</script>
```

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>跨域错误</title>
</head>

<body>
    结果显示，跨域之后window.onerror根本捕获不到正确的异常信息，而是统一返回一个Script error，

    解决方案：对script标签增加一个crossorigin=”anonymous”，并且服务器添加Access-Control-Allow-Origin。

    <script src="http://cdn.xxx.com/index.js" crossorigin="anonymous"></script>
</body>

</html>
```

### source

sourceMap 绝对不能上线，一旦 sourceMap 上线了就面临了源代码的泄露

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>sourceMap分析</title>
</head>
<body>
    
</body>
</html>
```

自己做 监控平台：用rollup去打包，umd规范 参考（fundebug ）