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