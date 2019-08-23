# node下的 eventLoop

```
   ┌───────────────────────┐
┌─>│        timers         │<————— 执行 setTimeout()、setInterval() 的回调
│  └──────────┬────────────┘
|             |<-- 执行所有 Next Tick Queue 以及 MicroTask Queue 的回调
│  ┌──────────┴────────────┐
│  │     pending callbacks │<————— 执行由上一个 Tick 延迟下来的 I/O 回调（待完善，可忽略）
│  └──────────┬────────────┘
|             |<-- 执行所有 Next Tick Queue 以及 MicroTask Queue 的回调
│  ┌──────────┴────────────┐
│  │     idle, prepare     │<————— 内部调用（可忽略）
│  └──────────┬────────────┘     
|             |<-- 执行所有 Next Tick Queue 以及 MicroTask Queue 的回调
|             |                   ┌───────────────┐
│  ┌──────────┴────────────┐      │   incoming:   │ - (执行几乎所有的回调，除了 close callbacks 以及 timers 调度的回调和 setImmediate() 调度的回调，在恰当的时机将会阻塞在此阶段)
│  │         poll          │<─────┤  connections, │ 
│  └──────────┬────────────┘      │   data, etc.  │ 
│             |                   |               | 
|             |                   └───────────────┘
|             |<-- 执行所有 Next Tick Queue 以及 MicroTask Queue 的回调
|  ┌──────────┴────────────┐      
│  │        check          │<————— setImmediate() 的回调将会在这个阶段执行
│  └──────────┬────────────┘
|             |<-- 执行所有 Next Tick Queue 以及 MicroTask Queue 的回调
│  ┌──────────┴────────────┐
└──┤    close callbacks    │<————— socket.on('close', ...)
   └───────────────────────┘
```

- node 的 macro-task(宏任务)：包括**整体代码script**，**setTimeout**，**setInterval**， **setImmediate**， **I/O**

- node 的 micro-task(微任务)：process.nextTick，Promises， `Object.observe`， MutationObserver

其实nodejs与浏览器的区别，就是nodejs的 MacroTask 分好几种，而这好几种又有不同的 task queue，而不同的 task queue 又有顺序区别

> setTimeout/setInterval 属于 timers 类型；
> setImmediate 属于 check 类型；
> socket 的 close 事件属于 close callbacks 类型；
> 其他 MacroTask 都属于 poll 类型。
> process.nextTick 本质上属于 MicroTask，但是它先于所有其他 MicroTask 执行；
> 所有 MicroTask 的执行时机，是不同类型 MacroTask 切换的时候。
> idle/prepare 仅供内部调用，我们可以忽略。
> pending callbacks 不太常见，我们也可以忽略。

> 先执行所有类型为 timers 的 MacroTask，然后执行所有的 MicroTask（注意 NextTick 要优先哦）；
> 进入 poll 阶段，执行几乎所有 MacroTask，然后执行所有的 MicroTask；
> 再执行所有类型为 check 的 MacroTask，然后执行所有的 MicroTask；
> 再执行所有类型为 close callbacks 的 MacroTask，然后执行所有的 MicroTask；
> 至此，完成一个 Tick，回到 timers 阶段；
> ……

看一个例子：

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

### setTimeout && setImmediate 的顺序

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
timers 是在 check 之前的。

另一个解释：当 Node 准备 event loop 的时间大于 1ms 时，进入 timers 阶段时，setTimeout 已经到期，则会先执行 setTimeout；反之，若进入 timers 阶段用时小于 1ms，setTimeout 尚未到期，则会错过 timers 阶段，先进入 check 阶段，而先执行 setImmediate
但有一种情况，它们两者的顺序是固定的：

```javascript
const fs = require('fs')

fs.readFile('test.txt', () => {
  console.log('readFile')
  setTimeout(() => {
    console.log('timeout')
  }, 0)
  setImmediate(() => {
    console.log('immediate')
  })
})
```
和之前情况的区别在于，此时 setTimeout 和 setImmediate 是写在 I/O callbacks 中的，这意味着，我们处于 poll 阶段，然后是 check 阶段，所以这时无论 setTimeout 到期多么迅速，都会先执行 setImmediate。本质上是因为，我们从 poll 阶段开始执行，而非一个 Tick 的初始阶段
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
### poll 阶段
poll 阶段主要有两个功能：

获取新的 I/O 事件，并执行这些 I/O 的回调，之后适当的条件下 node 将阻塞在这里
当有 immediate 或已超时的 timers，执行它们的回调

poll 阶段用于获取并执行几乎所有 I/O 事件回调，是使得 node event loop 得以无限循环下去的重要阶段。所以它的首要任务就是同步执行所有 poll queue 中的所有 callbacks 直到 queue 被清空或者已执行的 callbacks 达到一定上限，然后结束 poll 阶段，接下来会有几种情况：

setImmediate 的 queue 不为空，则进入 check 阶段，然后是 close callbacks 阶段……
setImmediate 的 queue 为空，但是 timers 的 queue 不为空，则直接进入 timers 阶段，然后又来到 poll 阶段……
setImmediate 的 queue 为空，timers 的 queue 也为空，此时会阻塞在这里，因为无事可做，也确实没有循环下去的必要

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

```
1
2
3
```

