# js 一些重要的 api

### 数组 api `reduce`

reduce() 对于空数组是不会执行回调函数的

没有初始值：下面的代码最初 `result` 为 1，`curr`为 2，`index` 为1，迭代四次结束，得到最终结果 15

```javascript
var arr = [1,2,3,4,5];
let res = arr.reduce((result, curr, index, arr) => {
    console.log(result, curr, index);
    return result + curr
});
console.log(res);
```

给定初始值5：下面的代码最初 `result` 为5，`curr`为 1，`index` 为0，迭代五次结束，得到最终结果 20

```javascript
var arr = [1,2,3,4,5];
let res1 = arr.reduce((result, curr, index, arr) => {
    console.log(result, curr, index);
    return result + curr
}, 5);
console.log(res1);
```

结论：不给初始值时 `curr` 从 第二个元素开始，迭代此数为 `arr.length - 1 `，给定初始值时  `curr` 从 第一个元素开始，迭代次数为 `arr.length`

在 `redux` 源码中有一个 compose 函数：用于组合函数

`funcs.reduce((a, b) => (...args) => a(b(...args)))` 是其中核心的代码，每次迭代不会去运行任何函数，而是把两个函数给包起来，最后返回的函数是包含了`funcs`  里面所有函数的，指向最后返回的函数时，顺序是`funcs`从右到左，一个函数的 结果传递给下一个函数作为参数。

```javascript
export default function compose(...funcs) {
  if (funcs.length === 0) {
    return arg => arg
  }

  if (funcs.length === 1) {
    return funcs[0]
  }

  return funcs.reduce((a, b) => (...args) => a(b(...args)))
}
```

