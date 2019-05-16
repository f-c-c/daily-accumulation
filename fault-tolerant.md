容错

### try {} catch () {}

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

下面👇的运行时错误是能捕获到的，会输出

```
知道错误了
ReferenceError: error is not defined
    at text.html:21
over
```

```javascript
    <script>
        // 允许时错误
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

结论： `try {} catch () {}` 能力有限，只能捕捉到 **运行时非异步错误**

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

* `window.onerror`主要是用来捕获预料之外的错误，而`try {} catch () {}`则是用来在可预见的情况下区捕获错误
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



```javascript
<body>
    <script>
        window.addEventListener('unhandledrejection', (e) => {
            e.preventDefault();
            console.log('我知道 promise 错误了');
            console.log(e.reason);
            return true;
        }, false); // 如果不捕获的话，会爆红的
        Promise.reject("promise error1");
        new Promise((resolve, reject) => {
            reject(reject);
        })
        new Promise((resolve, reject) => {
            resolve();
        }).then(() => {
            throw 'promise error2'
        })
    </script>
</body>
```

输出：

```
我知道 promise 错误了
promise error1
我知道 promise 错误了
ƒ () { [native code] }
我知道 promise 错误了
promise error2
```

