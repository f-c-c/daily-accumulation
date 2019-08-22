# eventLoop

> Javascript是单线程的，但是却能执行异步任务，这主要是因为 JS 中存在`事件循环`（Event Loop）和`任务队列`（Task Queue）。
>
> js 里面执行的代码分为**同步任务(Synchronous)**和**异步执行队列(Asynchronous)**，异步执行队列又细分为**宏任务（macroTask）**和**微任务（microTask）**
>
> **任务队列**：异步操作会将相关回调添加到任务队列中。而不同的异步操作添加到任务队列的时机也不同，如`onclick`, `setTimeout`,`ajax`处理的方式都不同，这些异步操作是由浏览器内核的`webcore`来执行的，`webcore`包含下图中的3种 webAPI，分别是`DOM Binding`、`network`、`timer`模块。
>
> - **DOM Binding** 模块处理一些DOM绑定事件，如`onclick`事件触发时，回调函数会立即被`webcore`添加到任务队列中。
> - **network** 模块处理`Ajax`请求，在网络请求返回时，才会将对应的回调函数添加到任务队列中。
> - **timer** 模块会对`setTimeout`等计时器进行延时处理，当时间到达的时候，才会将回调函数添加到任务队列中。
>
> 在浏览器中，我们讨论事件循环，是以“从宏任务队列中取一个任务执行，再取出微任务队列中的所有任务”来分析执行代码的。
>
> **主线程**：JS 只有一个线程，称之为主线程。而事件循环是主线程中执行栈里的代码执行完毕之后，才开始执行的。所以，主线程中要执行的代码时间过长，会阻塞事件循环的执行，也就会阻塞异步操作的执行。只有当主线程中执行栈为空的时候（即同步代码执行完后），才会进行事件循环来观察要执行的事件回调，当事件循环检测到任务队列中有事件就取出相关回调放入执行栈中由主线程执行。
>
> - js引擎首先从macrotask queue中取出第一个任务，执行完毕后，将microtask queue中的所有任务取出，按顺序全部执行；
> - 然后再从**macrotask queue**（宏任务队列）中取下一个，执行完毕后，再次将**microtask queue**（微任务队列）中的全部取出；
> - 循环往复，直到两个queue中的任务都取完。

- macro-task(宏任务)：包括**整体代码script**，**setTimeout**，**setInterval**， **setImmediate**， **I/O**

- micro-task(微任务)：process.nextTick，Promises， `Object.observe`， MutationObserver

不同类型的任务会进入对应的Event Queue：

* macro-task 会进入 宏任务的 异步执行队列
* micro-task 会进入 微任务的 异步执行队列

> 事件循环的顺序，决定js代码的执行顺序。进入整体代码(宏任务)后，开始第一次循环。接着执行所有的微任务。然后再执行下一个宏任务(按照**添加的先后顺序**进行执行)，再执行所有的微任务
>
> macroTask被套在 其他宏任务或者微任务里面时，要搞清楚宏任务被添加到macroTask异步执行队列的**顺序**，事件循环会按照这个顺序进行（重要）
>
> microTask被套在 其他宏任务时相当于在当前tick立即执行
>
> microTask被套在 其他microTask时相当于在当前tick立即执行

`setTimeout`有一个最小的时间间隔限制：

定时器最小时间间隔：在苹果机上的最小时间间隔是**10ms**，在Windows系统上的最小时间间隔大约是**15ms**。Firefox中定义的最小时间间隔是**10ms**，而HTML5规范中定义的最小时间间隔是**4ms**。

![eventloop1](../assert/eventloop1.jpeg)

看一个例子：

```javascript
setTimeout(_ => console.log(4))

new Promise(resolve => {
  resolve()
  console.log(1)
}).then(_ => {
  console.log(3)
  Promise.resolve().then(_ => {
    console.log('before timeout')
  }).then(_ => {
    Promise.resolve().then(_ => {
      console.log('also before timeout')
    })
  })
})

console.log(2)
```

输出：

```
1
2
3
before timeout
also before timeout
4
```

再看一个：

```javascript
console.log('1');

setTimeout(function() {
    console.log('2');
    setTimeout(function() {
        console.log('100');
    })
    process.nextTick(function() {
        console.log('3');
        setTimeout(function() {
            console.log('200');
        })
    })
    new Promise(function(resolve) {
        console.log('4');
        resolve();
    }).then(function() {
        console.log('5')
        Promise.resolve().then(() => {
            console.log('5+');
            process.nextTick(function() {
                console.log('5++')
                process.nextTick(function() {
                    console.log('5+++')
                    Promise.resolve().then(() => {
                        console.log('5++++')
                    })
                })
            })

        })
    })
})
process.nextTick(function() {
    console.log('6');
})
new Promise(function(resolve) {
    console.log('7');
    resolve();
}).then(function() {
    console.log('8')
})

setTimeout(function() {
    console.log('9');
    process.nextTick(function() {
        console.log('10');
    })
    new Promise(function(resolve) {
        console.log('11');
        resolve();
    }).then(function() {
        console.log('12')
    })
    setTimeout(function() {
        console.log('300');
    })
})
```

输出：

```
1
7
6
8
2
4
3
5
5+
5++
5+++
5++++
9
11
10
12
100
200
300
```

继续：

```
setTimeout(function() {
　　console.log(1);
}, 0);

console.log(2);

let end = Date.now() + 1000*5;

while (Date.now() < end) {
}

console.log(3);

end = Date.now() + 1000*5;

while (Date.now() < end) {
}

console.log(4);
```

输出：(说明了：异步代码是在所有同步代码执行完毕以后才开始执行的)

```
2
3
4
1
```

### setTimeout && setImmediate

```javascript
setImmediate(function(){
    console.log(1);
});
setTimeout(function(){
    console.log(2);
});
```

在Node中`setTimeout`和`setImmediate`执行顺序是随机性的：解释如下：

setTimeout的优先级高于setImmediate，但是因为setTimeout的after被**强制修正为1**，这就可能存在下一个tick触发时，耗时尚不足1ms，setTimeout的回调依然未超时，因此setImmediate就先执行了！这可以通过在本次tick中加入一段耗时较长的代码来来保证本次tick耗时必须超过1ms来检测

再看一个类似的：

```javascript
setTimeout(() => {
    console.log('这里肯定是先于xixi的');
    setImmediate(() => {
        console.log('setImmediate');
    });
    setTimeout(() => {
        console.log('setTimeout');
    }, 0);
}, 0);

setTimeout(() => {
    console.log('xixi');
}, 0);
```

这个代码有可能输出： （这表明电脑性能很好，第一个）

```
这里肯定是先于xixi的
setImmediate
xixi
setTimeout
```

也有可能输出：

```
这里肯定是先于xixi的
xixi
setImmediate
setTimeout
```

有一点可以确定： setTimeout 会 在xixi 之后

### process.nextTick

它是将回调函数加入到 当前执行栈的尾部，而不是任务队列的尾部

```javascript
setTimeout(function C() {
    console.log(3);
}, 0)
process.nextTick(function A() {
    console.log(1);
    process.nextTick(function B(){console.log(2);});
});
```

 输出：

