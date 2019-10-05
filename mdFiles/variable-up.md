# 关于提升

### var 和 函数提升

我们知道var和函数声明会存在提升的现象，直接看👀几道题(比较var 和 函数提升)：

```javascript
var a=1;
function test(){
    console.log(a);// undefined
    var a=1;
}
test();
```

```javascript
var b=2;
function test(){
    window.b=3;// 修改了全局变量
    console.log(b);// 输出全局变量 3
}
test();
// 这里没有任何的提升，因为在 test 里面没人任何的变量声明
```

```javascript
c=5; 
function test(){
    window.c=3;// 修改全局变量
    console.log(c);// undefined（由于提升了）
    var c;// 这个变量会提升
    console.log(window.c);// 输出全局变量
}
test();
```

```javascript
function test() {
    console.log(typeof b);// function
    function b() {
    }
    var b = 9;
}
test();
// 下面也一样，只要变量和函数同名，不管谁在前，谁在后，都取函数
// 可以简单理解为，先提升所有变量，再提升所有函数，函数覆盖了变量
function test() {
    console.log(typeof b);// function
    var b = 9;
    function b() {
    }
}
test();
```

- 函数提升优先级高于变量提升（函数和变量同名时，取函数）
- var 声明的变量，重复用var声明，只提升一次，后面的 var 相当于赋值

- 同名函数后面的会覆盖前面的

### let 不提升、存在暂时性死区**、**不允许重复声明

```javascript
function test() {
    console.log(b);// 由于暂时性死区的原因，会报错 ReferenceError: b is not defined
    let b;
}
test();
```

```javascript
function test() {
    var b;
    let b;// let 不允许在相同作用域声明同一个变量：SyntaxError: Identifier 'b' has already been declared
}
test();
```
在没有let 和 const 之前 typeof 是一个绝对安全的操作，永远不会报错，即使这样也是可以的：

```javascript
function test() {
    console.log(typeof a);// undefined
}
test();
```

有了let和const，导致 typeof 不再是一个 100% 安全的操作

```javascript
let a = 1;
function test() {
    console.log(typeof a);// ReferenceError: a is not defined
    let a;
}
test();
```

let 还只在声明时所在的块级作用域有效：

```javascript
if (true) {
    var a = 1;
    let b = 2;
}
console.log(a);// 1
console.log(b);// ReferenceError: b is not defined
```

### const 不但具有let的特性，还必须在声明时就赋值

const 和 let 一样具有：**不提升**、**存在暂时性死区**、**不允许重复声明**、**只在声明时所在的块级作用域有效**、的特性，const 还具有：**一旦声明就必须初始化**，不能留到以后再赋值：

```javascript
function test() {
    const a;// SyntaxError: Missing initializer in const declaration
    a = '123';
}
test();
```

小心 const 的一个坑：const 保存的常量如果是引用类型，它保存的是地址：

```javascript
function test() {
    const a = {};
    a.b = 1;// 这样是没有问题的，因为a的值没有变，还是栈里面的同一个地址，变得是堆里面的对象
    a.c = 2;// 这样是没有问题的，因为a的值没有变，还是栈里面的同一个地址，变得是堆里面的对象
    console.log(a);// {b: 1,c: 2}
}
test();
```

同样下面也是没人任何问题的：

```javascript
function test() {
    const a = [];
    a[0] = 1;
    a.push(2)
    console.log(a);// [1,2]
}
test();
```

但是要是改变了这个地址就会报错了：

```javascript
function test() {
    const a = [];
    a = [];// TypeError: Assignment to constant variable.
}
test();
// 下面也会报错
function test() {
    const a = {};
    a = {};
}
test();
```

### 讨论一个特殊的玩意

Es6 有了块级作用域，并且允许块级作用域的任意嵌套：

下面的👇代码虽然很奇怪，但是跑起来是没什么问题的，随你开心，你可以加很多个{}，但这没啥意思。

```javascript
{{{{{{{{{{{{{{{{{{{{{{{{
    var a = 9;
    console.log(a);
}}}}}}}}}}}}}}}}}}}}}}}}
```

```javascript
{{
    console.log(a);// undefined 由于 var 提升了
    var a = 9;
}}
```

下面的东西自行体会，很有意思的东西

#### 讨论全为 var 的情况

出现下面👇结果的原因：**1. var 会提升**，**2. var 没有块级作用域的概念**，在外面仍然能访问到

```javascript
{
    var a = 8;
    {
        console.log(a);// 8
        var a = 9;
    }
}
console.log(a);// 9
// 其实上述代码相当于：
var a;
{
    a = 8;
    {
        console.log(a);
        a = 9;
    }
}
console.log(a);
```

#### var 混合 let

```javascript
{
    var a = 8;
    {
        let a = 9;
        console.log(a);// 9
    }
}
console.log(a);// 8
```

```javascript
{
    var a = 8;
    {
        let a = 9;// 内层作用域可以定义外层作用域的同名变量
        console.log(a);// 9
    }
    console.log(a);// 8
}
console.log(a);// 8
```

两个 var 或者 两个 let  或者 外面var 里面 let 都不会报错，但是**惊悚**的事情来了：

```javascript
{
    let a = 8;
    {
        var a = 9;// SyntaxError: Identifier 'a' has already been declared
    }
}
```

#### 讨论全为 let 的情况

```javascript
{
    let a = 8;
    {
        console.log(a);// 8 内层作用域可以读取外层作用域的变量
    }
}
```

```javascript
{
    let a = 8;
    {
        let a = 9;// 内层作用域可以定义外层作用域的同名变量
        console.log(a);// 9
    }
    console.log(a);// 8
}
console.log(a);// ReferenceError: a is not defined
```

```javascript
{
    let a = 8;
    {
        console.log(a);// ReferenceError: a is not defined---死区的原因
        let a = 9;
    }
}
```

