# 函数式编程

#### 函数式编程（functional programming） 简介

* 与面向对象编程（Object-oriented programming）和过程式编程（Procedural programming）并列的编程范式
* 函数是**一等公民**（实际上说的是它们和其他对象都一样...所以就是普通公民（坐经济舱的人？）。函数真没什么特殊的，你可以像对待任何其他数据类型一样对待它们——把它们存在数组里，当作参数传递，赋值给变量...等等），所谓一等公民指的是与其他数据类型一样，处于同等地位，可以赋值给其他变量、也可以作为参数传给另一个函数、或者作为别的函数的返回值
* 在函数式编程中，变量是不能被修改的，所有的变量只能被赋值一次
* 总结函数式编程的特点：
  * 函数是`一等公民`（就是所有变量里面的贵族，优先考虑函数）
  * 只用`表达式` ，不用`语句`，在函数式编程里面没有 `if else switch try catch ` 等，是用数学的思维去解决问题
  * 没有副作用、不修改状态、引用透明（函数运行只靠参数）

#### 纯函数
> 纯函数是这样一种函数，即相同的输入，永远会得到相同的输出，而且没有任何可观察的副作用。

* 对于**相同的输入**，**永远得到相同的输出**

  * 何为相同：对于基本数据类型来说（NaN除外，NaN 和 NaN认为是相同的，但他们不恒等），自然是 `===`，对于引用类型（Object，Array）来说，不要求`===`，只要 JSON.stringify 的结果恒等就行(也就是内部结构一模一样的两个不同的对象认为是相同的)。对于 函数，只要两个函数（函数体逻辑相同）

* 没有任何可观察的副作用，不依赖外部环境状态（system state）

* ```javascript
  var a = [12, 5, 8, 1, 2, 3];
  //Array.slice 是纯函数，因为它没有副作用，并且对于相同的输入总能得到相同的输出
  a.slice(0, 3);
  a.slice(0, 3);
  a.slice(0, 3);
  a.slice(0, 3);
  //Array.splice 就不是纯函数，它修改了原来的数组(产生了可观察到的副作用，即这个数组永久地改变了
  //并且每次相同的输入会得到不同的输出
  ```
> 在函数式编程中，我们讨厌这种会改变数据的笨函数。我们追求的是那种可靠的，每次都能返回同样结果的函数，而不是像 splice 这样每次调用后都把数据弄得一团糟的函数，这不是我们想要的。
* `map` 和 `reduce` 就是标准的 纯函数

![image-20181111123207194](./assert/image-20181111123207194.png)

```javascript
//memoize函数接收一个函数作为参数，利用闭包返回一个函数，第一次运行时会计算值并将其缓存，之后会从缓存读取
const memoize = function(fn) {
    const cache = {};
    //利用闭包实现缓存
    return function() {
        const key = JSON.stringify(arguments);
        var value = cache[key];
        if(!value) {
            console.log('新值，执行中...');         // 为了了解过程加入的log，正式场合应该去掉
            value = [fn.apply(this, arguments)];  // 放在一个数组中，方便应对undefined，null等异常情况
            cache[key] = value;
        } else {
            console.log('来自缓存');               // 为了了解过程加入的log，正式场合应该去掉
        }
        return value[0];
    }
}

let a = memoize(function(x){return x*x;});
let result1 = a(5);
let result2 = a(5);
console.log(result1);
console.log(result2);
```



#### 幂等性

* 幂等性是指 函数执行无数次后还具有相同的效果，对于同一个参数，函数运行一次和运行两次结果一致

```javascript
Math.abs(-12);// 这就符合 幂等性
```

#### 柯里化（curry）

* curry 的概念很简单：只传递给函数一部分参数来调用它，让它返回一个函数去处理剩下的参数。

* 柯里化 是将**接受多个参数的函数变成 只接受一个参数的函数**，其实是将参数存起来，原理就是**闭包**

* ```javascript
  // 比如上图中的 函数 checkage 
  var checkage18 = min => (age => age > min);
  ```

* ![image-20181111130914708](./assert/image-20181111130914708.png)
* 当我们谈论纯函数的时候，我们说它们接受一个输入返回一个输出。curry 函数所做的正是这样：每传递一个参数调用函数，就返回一个新函数处理剩余的参数。这就是一个输入对应一个输出啊。

#### 函数组合（compose）

* 有时候我们很容易写出洋葱式代码：`h(f(g(x)))`，这时可以考虑函数组合

* ```javascript
  const conpose = (f, g) => (x => f(g(x)));
  var first = arr => arr[0];
  var reverse = arr => arr.reverse();
  var last = conpose(first, reverse);
  console.log(last([1, 2, 3, 4, 5]));//5
  ```
  f 和 g 都是函数，x 是在它们之间通过**“管道”**传输的值。
  组合看起来像是在饲养函数。你就是饲养员，选择两个有特点又遭你喜欢的函数，让它们结合，产下一个崭新的函数。
  在 compose 的定义中，g 将先于 f 执行，因此就创建了一个**从右到左**的数据流。这样做的可读性远远高于嵌套一大堆的函数调用，**从右到左**执行更加能够反映数学上的含义`(f(g(x))`

  ```javascript
  compose(toUpperCase, compose(head, reverse));
  // 或者
  compose(compose(toUpperCase, head), reverse);//效果相同
  ```

  下面的compose函数可以接受多个参数（函数），同时组合多个函数，依然是从右到左的数据流：

  ```javascript
  //组合函数-数据流从右到左
  function compose() {
      let funcs = arguments;
      return function(x) {
          let temp = x;
          let result = null;
          for(let i = funcs.length - 1; i >= 0; i--){
              result = funcs[i](temp);
              temp = result;
          }
          return result;
      }
  }
  var toUpperCase = function(x) { return x.toUpperCase(); };//将输入字符串变为大些
  var exclaim = function(x) { return x + '!'; };//在字符串后面加一个字符： !
  var getLast = function(x) { return x[x.length - 2]; };//取得倒数第二个字符
  let func = compose(getLast, exclaim, toUpperCase);//函数组合
  console.log(func('qwqwqwx'));//X
  ```

  

## Pointfree风格 

* 详细阐述可以看 [这里](http://www.ruanyifeng.com/blog/2017/03/pointfree.html)

* 帮助我们减少不必要的命名，保持代码的简介和通用，下面部分内容引了阮一峰的博客

* **程序的本质：**

![bg2017031202](./assert/bg2017031202.png)



上图是一个编程任务，左侧是数据输入（input），中间是一系列的运算步骤，对数据进行加工，右侧是最后的数据输出（output）。一个或多个这样的任务，就组成了程序。

输入和输出（统称为 I/O）与键盘、屏幕、文件、数据库等相关，这些跟本文无关。这里的关键是，中间的运算部分不能有 I/O 操作，应该是纯运算，即通过纯粹的数学运算来求值。否则，就应该拆分出另一个任务。

I/O 操作往往有现成命令，大多数时候，编程主要就是写中间的那部分运算逻辑。现在，主流写法是过程式编程和面向对象编程，但是我觉得，最合适纯运算的是函数式编程。

* **函数的拆分和合成**

![bg2017031203](./assert/bg2017031203.png)

* ```javascript
  const compose = (f, g) => (x => f(g(x))); // 先运行 g函数，得到的结果再传给 f
  var uoUpperCase = word => word.toUpperCase();
  var split = x => (str => str.split(x));
  var f = compose(split(" "), uoUpperCase);
  var result = f("abcd efgh"); // 先将字符串转为大写，再安装空格分割
  console.log(result); // [ 'ABCD', 'EFGH' ]
  ```

#### 声明式代码和命令式代码

* 命令式代码：我们通过编写一条又一条的指令去让计算机执行一些动作，这其中一般都会涉及到很多繁杂的细节，而声明式就要优雅很多，我们通过写表达式的方式来声明我们想干什么，而不是通过一步一步的指示

```javascript
// 命令式
let RDs = [];//存放 开发人员的数组
for (var i = 0; i< companies.length; i++) {
    RDs.push(companies[i].rd);
}
// 声明式
let RDs = companies.map(c => c.rds);
```

#### 高阶函数

> 高阶函数：参数或返回值为函数的函数

* 函数当参数，把传入的函数做一个封装，然返回这个封装函数，达到更高程度的抽象

* ```javascript
  var add = function(a, b) {
      return a + b;
  }
  function math(func, array) {
      return func(array(0), array(1));
  }
  math(add, [1,2]);
  ```

* 高阶函数的总结：

    1. 它是一等公民（因为是函数）
    2. 它以一个函数作为参数
    3. 以一个函数作为返回结果

* 堆栈溢出和死循环的区别（爆栈浏览器会报错，而死循环式根本没有办法执行其他的东西）

#### 尾递归调用
* 未完待续
#### 再说闭包
* 别担忧去使用闭包，闭包式有内存泄漏，适当的使用是没有问题的，可以 ` = null` 去手动释放
* 闭包就是 拿到了你本不应该拿到的东西
#### 范畴与容器

* 我们可以把`范畴`想象成一个容器，里面包含两样东西，值（value）、值的变形关系，也就是函数
* 函数不仅可以用于同一个范畴之中值的转换，还可以用于将一个范畴转换成另一个范畴，这就涉及到了 `函子（Functor）`
* `函子`是函数式编程里面最重要的数据类型，也是基本的运算单位和功能单位。它首先是一种范畴，也就是说，是一个容器，包含了值和变形关系。比较特殊的是，它的变形关系可以依次作用于每一个值，将当前容器变形成另一个容器。

![image-20181111162112116](./assert/image-20181111162112116.png)

#### 函子

* 首先 **函子** 是一个容器

* 任何具有 `map` 方法的数据结构，都可以当作函子的实现

* 下面的 `Container` 叫容器，有了 `map` 过后叫 函子

* ```javascript
  // 这是一个容器
  var Container = function(x) {
      this.__value = x;
  }
  // 函数式编程一般约定，函子又一个 of 方法，为了区别于 oop
  Container.of = x => new Container(x);
  // 一般还约定，函子的标志就是容器具有 map 方法，该方法将容器里面的每一个值，映射到另一个容器里面
  // map 方法接受一个 变形关系 f
  Container.protorype.map = function(f) {
      return Container.of(f(this.__value));
  }
  Container.of(3)
  .map(x => x + 1)	// 通过变形关系得到一个新的函子 Container(4)
  .map(x => 'Result is' + x); // 得到另一个函子 Container('Result is 4')
  ```

* ```javascript
  // 写成 es6 如下
  class Functor{
      constructor(val){
          this.val = val;
      }
      nap(f){
          return new Functor(f(this.val));
      }
  }
  ```

* 上面的 `Functor` 就是一个 函子，它的 `map` 方法接受一个 变形方法，返回一个新的函子

* 再次重申一下：一般约定，函子的标志就是容器具有 `map` 方法。该方法将容器里面的每一个值，映射到另一个容器

* 函数式编程里面的运算，都是通过函子完成的，学习函数式编程，实际上就是学习函子的各种运算，就是运用不同的函子，解决实际问题

##### maybe 函子

* 用 三元去做 或者 `||`

* 函子接受各种函数，处理容器内部的值。这里就有一个问题，容器内部的值可能是一个空值（比如`null`），而外部函数未必有处理空值的机制，如果传入空值，很可能就会出错。

* ```javascript
  Functor.of(null).map(function (s) {
    return s.toUpperCase();
  });
  // TypeError
  ```

* 

##### Either函子

##### ap函子

##### IO函子

#### 函数式编程相关库

* RxJS
* underscorejs
* lodash
* [Ramda](http://ramda.cn/)

