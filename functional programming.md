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
          [...funcs].reverse().forEach(item => { x = item(x);});
          return x;
      }
  }
  var toUpperCase = function(x) { return x.toUpperCase(); };//将输入字符串变为大些
  var exclaim = function(x) { return x + '!'; };//在字符串后面加一个字符： !
  var getLast = function(x) { return x[x.length - 2]; };//取得倒数第二个字符
  let func = compose(getLast, exclaim, toUpperCase);
  console.log(func('qwqwqwx'));
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

* 命令式代码：我们通过编写一条又一条的指令去让计算机执行一些动作，这其中一般都会涉及到**很多繁杂的细节**，而声明式就要优雅很多，我们通过写表达式的方式来声明我们想干什么，而不是通过一步一步的指示

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

尾调用优化是函数式编程的一个重要概念

* 什么是**尾调用**：一句话说清楚，就是指**某个函数的最后一步是调用另一个函数**

* ```javascript
  function f(x){
    return g(x);
  }
  //函数f的最后一步是调用函数g，这就叫尾调用
  ```

* ```javascript
  // 情况一
  function f(x){
    let y = g(x);
    return y;
  }
  //情况一是调用函数g之后，还有别的操作，所以不属于尾调用，即使语义完全一样。
  //情况二也属于调用后还有操作，即使写在一行内。
  // 情况二
  function f(x){
    return g(x) + 1;
  }
  ```

* ```javascript
  function f(x) {
    if (x > 0) {
      return m(x)
    }
    return n(x);
  }
  //尾调用不一定出现在函数尾部，只要是最后一步操作即可
  ```

* 函数调用会在内存形成一个"调用记录"，又称"调用帧"（call frame），保存调用位置和内部变量等信息。如果在函数A的内部调用函数B，那么在A的调用记录上方，还会形成一个B的调用记录。等到B运行结束，将结果返回到A，B的调用记录才会消失。如果函数B内部还调用函数C，那就还有一个C的调用记录栈，以此类推。所有的调用记录，就形成一个["调用栈"]（call stack）

* 尾调用由于是函数的最后一步操作，所以不需要保留外层函数的调用记录，因为外层函数的调用位置、内部变量等信息都不会再用到了，可以销毁其调用栈，只要直接用内层函数的调用记录，取代外层函数的调用记录就可以了

* ```javascript
  function f() {
    let m = 1;
    let n = 2;
    return g(m + n);
  }
  f();
  
  // 等同于
  function f() {
    return g(3);
  }
  f();
  
  // 等同于
  g(3);
  ```

* 如果函数g不是尾调用，函数f就需要保存内部变量m和n的值、g的调用位置等信息。但由于调用g之后，函数f就结束了，所以执行到最后一步，完全可以删除 f() 的调用记录，只保留 g(3) 的调用记录

* 这就叫做"**尾调用优化**"（Tail call optimization），即只保留内层函数的调用记录。如果所有函数都是尾调用，那么完全可以做到每次执行时，调用记录只有一项，这将大大节省内存。这就是"尾调用优化"的意义

#### 尾递归

> 函数调用自身，称为递归。如果尾调用自身，就称为尾递归

递归非常耗费内存，因为需要同时保存成千上百个调用记录，很容易发生"栈溢出"错误（stack overflow）。但对于尾递归来说，由于只存在一个调用记录，所以永远不会发生"栈溢出"错误

```javascript
function factorial(n) {
  if (n === 1) return 1;
  return n * factorial(n - 1);
}

factorial(5) // 120
//是一个阶乘函数，计算n的阶乘，最多需要保存n个调用记录，复杂度 O(n) 
```

```javascript
function factorial(n, total) {
  if (n === 1) return total;
  return factorial(n - 1, n * total);
}
//改写成尾递归，只保留一个调用记录，复杂度 O(1)
factorial(5, 1) // 120
//调用栈如下
//factorial(5, 1) -> factorial(4, 5) -> factorial(3, 20) -> factorial(2, 60) -> factorial(1, 120)
```

"尾调用优化"对递归操作意义重大，所以一些函数式编程语言将其写入了语言规格。ES6也是如此，第一次明确规定，所有 ECMAScript 的实现，都必须部署"尾调用优化"。这就是说，在 ES6 中，只要使用尾递归，就不会发生栈溢出，相对节省内存

* 注意⚠️：node在后续的版本中支持过尾归调用（经过实验6.9.1是支持尾调用优化的，需要在严格模式下：`use strict` 以及 这样去开启`node --harmony_tailcalls aaa.js`），但后续给去掉了（截止目前11.6.0 是一句去掉了`--harmony_tailcalls`）
* ⚠️：浏览器上只有**safari**支持，而其他浏览器上并不支持。所以，这是一个“未真正实现的提议”，仅仅了解下就行，目前还无法普遍用到生产环境中
* 基于以上两点，在 `chrome` 和 `node `里面实验尾调用的代码，依然会出现堆栈溢出的

### 递归函数的改写

下面有3种方法用于递归函数的改写

```javascript
function tailFactorial(n, total) {
  if (n === 1) return total;
  return tailFactorial(n - 1, n * total);
}

function factorial(n) {
  return tailFactorial(n, 1);
}

factorial(5) // 120
```

```javascript
function currying(fn, n) {
  return function (m) {
    return fn.call(this, m, n);
  };
}

function tailFactorial(n, total) {
  if (n === 1) return total;
  return tailFactorial(n - 1, n * total);
}

const factorial = currying(tailFactorial, 1);

factorial(5) // 120
```

```javascript
function factorial(n, total = 1) {
  if (n === 1) return total;
  return factorial(n - 1, n * total);
}

factorial(5) // 120
```


#### 再说闭包
* 别担忧去使用闭包，闭包是有内存泄漏，适当的使用是没有问题的，可以 ` = null` 去手动释放
* 闭包就是 拿到了你本不应该拿到的东西
* 闭包在前端可以随意一点用，在node里面不要随便乱用，一个人泄露一点，人多了就不行了
#### 范畴与容器

* 我们可以把`范畴`想象成一个容器，里面包含两样东西，值（value）、值的变形关系，也就是函数
* 函数不仅可以用于同一个范畴之中值的转换，还可以用于将一个范畴转换成另一个范畴，这就涉及到了 `函子（Functor）`
* `函子`是函数式编程里面最重要的数据类型，也是基本的运算单位和功能单位。它首先是一种范畴，也就是说，是一个容器，包含了值和变形关系。比较特殊的是，它的变形关系可以依次作用于每一个值，将当前容器变形成另一个容器。

![image-20181111162112116](./assert/image-20181111162112116.png)

#### 函子

* 首先 **函子** 是一个**容器**

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
  let Maybe = function(x) {
      this.__value = x;
  }
  //每一个容器都有一个of方法，用来生产一个新的容器
  Maybe.of = function(x) {
      return new Maybe(x);
  }
  //有map方法的容器就叫做：函子
  Maybe.prototype.map = function(f) {
      return this.isNothing() ? Maybe.of(null) : Maybe.of(f(this.__value));
  }
  Maybe.prototype.isNothing = function() {
      return this.__value === undefined || this.__value === null
  }
  Maybe.of(null).map(function (s) {
    return s.toUpperCase();
  });
  ```



##### Either函子

条件运算`if...else`是最常见的运算之一，函数式编程里面，使用 Either 函子表达

Either 函子内部有两个值：左值（`Left`）和右值（`Right`）。右值是正常情况下使用的值，左值是右值不存在时使用的默认值。

```javascript
class Either extends Functor {
  constructor(left, right) {
    this.left = left;
    this.right = right;
  }

  map(f) {
    return this.right ? 
      Either.of(this.left, f(this.right)) :
      Either.of(f(this.left), this.right);
  }
}

Either.of = function (left, right) {
  return new Either(left, right);
};
```

```javascript
var addOne = function (x) {
  return x + 1;
};

Either.of(5, 6).map(addOne);
// Either(5, 7);

Either.of(1, null).map(addOne);
// Either(2, null);
```

上面代码中，如果右值有值，就使用右值，否则使用左值。通过这种方式，Either 函子表达了条件运算。

Either 函子的常见用途是提供默认值

```javascript
Either
.of({address: 'xxx'}, currentUser.address)
.map(updateField);
```

上面代码中，如果用户没有提供地址，Either 函子就会使用左值的默认地址。

Either 函子的另一个用途是代替`try...catch`，使用左值表示错误

```javascript
function parseJSON(json) {
  try {
    return Either.of(null, JSON.parse(json));
  } catch (e: Error) {
    return Either.of(e, null);
  }
}
```

上面代码中，左值为空，就表示没有出错，否则左值会包含一个错误对象`e`。一般来说，所有可能出错的运算，都可以返回一个 Either 函子

##### ap函子

内部的值为一个函数，并有一个  `ap` 方法（接收的是一个函子）

函子里面包含的值，完全可能是函数。我们可以想象这样一种情况，一个函子的值是数值，另一个函子的值是函数

```javascript
function addTwo(x) {
  return x + 2;
}

const A = Functor.of(2);
const B = Functor.of(addTwo)
```

上面代码中，函子`A`内部的值是`2`，函子`B`内部的值是函数`addTwo`。

有时，我们想让函子`B`内部的函数，可以使用函子`A`内部的值进行运算。这时就需要用到 ap 函子。

ap 是 applicative（应用）的缩写。凡是部署了`ap`方法的函子，就是 ap 函子

```javascript
class Ap extends Functor {
  ap(F) {
    return Ap.of(this.val(F.val));
  }
}
```

注意，`ap`方法的参数不是函数，而是另一个函子。

因此，前面例子可以写成下面的形式

```javascript
Ap.of(addTwo).ap(Functor.of(2))
// Ap(4)
```

ap 函子的意义在于，对于那些多参数的函数，就可以从多个容器之中取值，实现函子的链式操作

```javascript
function add(x) {
  return function (y) {
    return x + y;
  };
}

Ap.of(add).ap(Maybe.of(2)).ap(Maybe.of(3));
// Ap(5)
```

上面代码中，函数`add`是柯里化以后的形式，一共需要两个参数。通过 ap 函子，我们就可以实现从两个容器之中取值。它还有另外一种写法

```javascript
Ap.of(add(2)).ap(Maybe.of(3));
```

#### Monad 函子

* Monad就是一种设计模式，表示将一个运算过程，通过函数拆解成互相连接的多个步骤。你只要提供下一步运算
  所需的函数，整个运算就会自动进行下去

* Promise 就是一种 Monad
* Monad 让我们避开了嵌套地狱，轻松的进行深度嵌套的函数式编程，比如 io 和其他异步任务

```javascript
class Monad extends Container {
    join() {
      return this.val;
    }
    flatMap(f) {
      return this.map(f).join();
    }
    map(fn) {
        return Monad.of(fn(this._val()));
    }
}
Monad.of = (val) => {
    return new Monad(val);
}
```

##### IO函子

* IO 跟前面那几个 Functor 不同的地方在于，它的 __value 是一个函数。它把不纯的操作(比如 IO、网络请求、DOM)包裹到一个函数内，从而延迟这个操作的执行。所以我们认为，IO 包含的是被包裹的操作的返回
  值。
* IO其实也算是惰性求值

```javascript
//Io函子（其 _val 为一个函数）
class Io{
    constructor(val) {
        this._val = val
    }
    join() {
        return this._val;
    }
    map(f) {
        return Io.of(f(this._val()));
    }
    flatMap(f) {
        return this.map(f).join();
    }
}
Io.of = (val) => {
    return new Io(val);
}

//将肮脏的 io 操作  ——>  封装为纯函数
//功能：读取文件
let fs = require('fs');
let readFile = function(filename) {
    return new Io(function() {
        return fs.readFileSync(filename, 'utf-8');
    })
}

//将一个依赖外部变量的函数  -->  封装为纯函数
//功能：在参数末尾连接上一个字符串
let a = "abcdef";//一个外部依赖
let addStr = function(x) {
  return new Io(function() {
    return x + a;
  });
}

//非纯函数->纯函数
//功能： 去掉字符串前 n 位
let n = 5;
let getLast = function(x) {
    return new Io(function() {
        return x.slice(n);
    });
}
console.log(
            //假设文件:aa.js里面的文字为： 123456
            readFile('./aa.js')       //得到一个Io函子(其_val 是一个函数->用于读取一个文件)--这是肮脏的操作，先封装为一个函子，不立即运行
                .flatMap(addStr)      //得到另一个Io函子（其_val 也是一个函数），并且拿到了 上面那个函子 的_val 的运行结果: 123456
                .flatMap(getLast)     //再得到另一个Io函子（其_val 也是一个函数），并且拿到了 上面那个函子 的_val 的运行结果: 123456abcdef
                ._val()               //运行上面那个函子的 _val, 完成功能： 去掉字符串前 n 位。得到最终结果：6abcdef
            );
// 将f(g(k(x))) 的嵌套写法转换为链式： k(x).chain(g).chain(f)
```

#### 函数式编程相关库

* RxJS [官网](https://cn.rx.js.org/) 目前已经不那么🔥了，浏览器已经实现了
* cycleJs
* underscorejs
* lodash
* [Ramda](http://ramda.cn/)

