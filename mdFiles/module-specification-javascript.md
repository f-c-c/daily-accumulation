# Javascript 模块规范

> 目前，较通行的 JS 模块化规范，有：`CommonJS/AMD/CMD` 以及 `ES6 module`
**无模块化时代：全局变量的泛滥**、**命名冲突**、**依赖关系管理混乱**

### IIFE（模块萌芽期）

```javascript
let moduleA = function() {
	let a = 1;
  let b = 2;
	return {
		message: function(c) {
			alert(a + b +c)
		}
	}
}()
moduleA.message(3) // 6

(function(window) {
   ... ...
})(window)
```

IIFE 创建块级（私有）作用域，避免了向全局作用域中添加变量和函数，因此也避免了多人开发中全局变量和函数的**命名冲突**

### CommonJS（Node.js）

最先出来的模块化规范是 CommonJS **CommonJS 是作用于服务器端的规范**

CommonJS规范不适用于浏览器环境，这是因为CommonJS的同步加载策略，这对服务器端不是一个问题，因为所有的模块都存放在本地硬盘，可以同步加载完成，等待时间就是硬盘的读取时间。但是，对于浏览器，这却是一个大问题，因为模块都放在服务器端，等待时间取决于网速的快慢，可能要等很长时间，浏览器处于”假死”状态

因此，浏览器端的模块，不能采用”同步加载”（synchronous），只能采用”异步加载”（asynchronous）。这就是AMD规范诞生的背景

2009年，美国程序员Ryan Dahl创造了node.js项目，将javascript语言用于服务器端编程 node.js的模块系统，就是参照CommonJS规范实现的

在CommonJs规范中：

- 一个文件就是一个模块，拥有单独的作用域；

- 普通方式定义的变量、函数、对象都属于该模块内；

- 通过require来加载模块；

- 通过exports和modul.exports来暴露模块中的内容；

- 所有代码都运行在模块作用域，**不会污染全局作用域**；模块可以多次加载，但只会在第一次加载的时候运行一次，然后运行结果就被缓存了，以后再加载，就直接读取缓存结果；模块的加载顺序，按照代码的出现顺序是**同步加载**的;

- dirname代表当前模块文件所在的文件夹路径，filename代表当前模块文件所在的文件夹路径+文件名

- require（同步加载）基本功能：读取并执行一个JS文件，然后**返回该模块的exports对象**，如果没有发现指定模块会报错

- 模块内的exports：为了方便，node为每个模块提供一个exports变量，其指向module.exports，相当于在模块头部加了这句话：var exports = module.exports，在对外输出时，可以给exports对象添加方法，PS：不能直接赋值（因为这样就切断了exports和module.exports的联系）

```javascript
// CommonJS模块
let { stat, exists, readFile } = require('fs');

// 等同于
let _fs = require('fs');
let stat = _fs.stat;
let exists = _fs.exists;
let readfile = _fs.readfile;
```
实质是整体加载fs模块（即加载fs的所有方法），生成一个对象（_fs），然后再从这个对象上面读取 3 个方法。这种加载称为“**运行时加载**”，因为只有运行时才能得到这个对象，导致完全没办法在编译时做“静态优化”。
### AMD（require.js）

AMD是”Asynchronous Module Definition”的缩写，意思就是”异步模块定义”。它采用异步方式加载模块，模块的加载不影响它后面语句的运行。所有依赖这个模块的语句，都定义在一个回调函数中，等到加载完成之后，这个回调函数才会运行。

AMD也采用require()语句加载模块，但是不同于CommonJS，它要求两个参数

```

require([module], callback);

```

- 第一个参数[module]，是一个数组，里面的成员就是要加载的模块；第二个参数callback，则是加载成功之后的回调函数。

- 目前，主要有两个Javascript库实现了AMD规范：`require.js`和`curl.js`

- AMD 的使用 以 require.js 为例子：

  - 在 html 中只需要引入一个 js 文件 如：

  - `<script src="./node_modules/requirejs/require.js" data-main="main.js"></script>`

  - 意思是引入 require.js 并指定主 js 文件为 main.js,在main中再去引用其他的模块

    ```javascript
    //main.js 异步引入add.js 模块
    require(['./libs/add.js'],function(add){
        alert(add.add(12,23));
    });
    alert('dfsf');//由于是异步的，这行代码会先执行
    //add.js（被main.js引入的模块）
    define(function(param){
        function add(n1,n2){
            return n1 + n2;
        }
        return {"add":add};
    });
    ```

### CMD（sea.js）

- CMD 是 “Common Module Definition”的缩写，意思就是“通用模块定义”。它与 AMD 有很多相似之处，CMD 支持同步模式和异步模式。目前实行 CMD 的主要是 `sea.js`

- ```javascript
  //html 文件
  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
      <script src="./node_modules/seajs/dist/sea.js"></script>
      <script>seajs.use('./main');</script>
  </head>
  <body>
      
  </body>
  </html>
  //main.js 中去引用(require)模块
  define(function(require,exports,module){
      //同步模式
      // let obj = require('./libs/add');
      // alert(obj.add(1,9));
  
      //异步模式,这里add和sub对象和前面的模块一一对应
      require.async(['./libs/add','./libs/substruction'],function(add,sub){
          alert(add.add(1,9));
          alert(sub.sub(19,9));
          
      });
      alert('weew');//如果为异步模式该行代码会提前执行
  });
  //在add.js中定义(exports)模块
  define(function(require,exports,module){
      function add(n1,n2){
          return n1 + n2;
      }
      exports.add = add;
  });
  //同样的在substruction.js 中定义模块
  define(function(require,exports,module){
      function sub(n1,n2){
          return n1 - n2;
      }
      exports.sub = sub;
  });
  ```

- 在实际的应用中可能 CMD 会用的多一些：有同步和异步模式，可读性很高

### AMD / CMD 异同

* AMD和CMD两者的共同点就是都是一种框架在推广的过程中对模块定义的规范产出；而且他们都是**异步加载模块**。

* AMD (`https://github.com/amdjs/amdjs-api/wiki/AMD`)是外国人发明出来的，其浏览器实现：require.js 主要解决了两个问题：

  * 文件之间的依赖问题

  * 浏览器加载多个JS文件时页面失去响应的问题，响应时间过长

* CMD(`https://github.com/seajs/seajs/issues/242`)CDM规范是国人开发出来的,CMD也有个浏览器的实现-SeaJS

* SeaJS 和 RequireJS 解决的是同样的问题，然而它**模块定义的方式和模块加载时机确是不同**的！！！

* AMD在定义模块的时候要先声明其依赖的模块

  ```javascript
  define(['jquery'],function($){
      var  backButton=$('.backToTop');
     function  animate(){
          $('html,body').animate({
               scrollTop:0
              },800);
          };
      function scroll(){
           if($(window).scrollTop()>$(window).height())
              backButton.fadeIn();
          else
              backButton.fadeOut();
      };
      backButton.on('click',animate);
      $(window).on('scroll', scroll);
      $(window).trigger('scroll');
  return{
      animate:animate,
      scroll:scroll
  };
  });
  ```


* CMD没有这里严格的要求，它只要依赖的模块在附近就可以了

  ```javascript
  // CMD
  define(function(require, exports, module) {
  var a = require('./a')
  a.doSomething()
  // 此处略去 100 行
  var b = require('./b') // 依赖可以就近书写
  b.doSomething()
  // ... 
  })
  ```
  
* AMD推崇**依赖前置**,CMD是**依赖就近**
* AMD加载完模块后，就立马执行该模块；CMD加载完某个模块后没有立即执行而是等到遇到require语句的时再执行
* 两者的不同导致各自的优点是**AMD用户体验好**，因为模块提前执行了；**CMD性能好**，因为只有用户需要的时候才执行

### CommonJS / ES6模块化异同

* CommonJS 模块输出的是一个**值的拷贝**（原来模块中的值改变不会影响已经加载的该值），ES6 模块输出的是值的**引用**（静态分析，动态引用，输出的是值的引用，值改变，引用也改变，即原来模块中的值改变则该加载的值也改变）
* CommonJS 模块是**运行时加载**，ES6 模块是**编译时输出接口**
* CommonJS 加载的是**整个模块**，即将所有的接口全部加载进来，ES6 可**以单独加载其中的某个接口**（方法）
* CommonJS **this 指向**当前模块，ES6 this 指向undefined

CommonJS 模块输出的是值的拷贝，也就是说，一旦输出一个值，模块内部的变化就影响不到这个值。ES6 模块的运行机制与 CommonJS 不一样。JS 引擎对脚本静态分析的时候，遇到模块加载命令import，就会生成一个只读引用。等到脚本真正执行时，再根据这个只读引用，到被加载的那个模块里面去取值。ES6 模块不会缓存运行结果，而是动态地去被加载的模块取值，并且变量总是绑定其所在的模块。

CommonJs模块化：

```javascript
// lib.js
var counter = 3;
function incCounter() {  
	counter++;
}
module.exports = {  
	counter: counter,  incCounter: incCounter
};
// main.js
var mod = require('./lib'); 
console.log(mod.counter);  // 3
mod.incCounter();
console.log(mod.counter); // 3
```

ES6模块化

```javascript
// lib.js
export let counter = 3;
export function incCounter() {
  counter++;
}
 
// main.js
import { counter, incCounter } from './lib';
console.log(counter); // 3
incCounter();
console.log(counter); // 4
```

我们看出，CommonJS 模块输出的是值的拷贝，也就是说，一旦输出一个值，模块内部的变化就影响不到这个值。而ES6 模块是动态地去被加载的模块取值，并且变量总是绑定其所在的模块。

另外CommonJS 加载的是一个对象（即module.exports属性），该对象只有在脚本运行完才会生成。而 ES6 模块不是对象，它的对外接口只是一种静态定义，在代码静态解析阶段就会生成。

目前阶段，通过 Babel 转码，CommonJS 模块的require命令和 ES6 模块的import命令，可以写在同一个模块里面，但是最好不要这样做。因为import在静态解析阶段执行，所以它是一个模块之中最早执行的。

### es6 模块化
> 主要由两个命令构成：export和import。export命令用于规定模块的对外接口，import命令用于输入其他模块提供的功能
#### export 命令
```javascript
//写法一： 一个一个变量的导出
export var firstName = 'Michael';
export var lastName = 'Jackson';
export var year = 1958;

//写法二：一起导出
var firstName = 'Michael';
var lastName = 'Jackson';
var year = 1958;

export {firstName, lastName, year};
// 上面两种写法等价，优先考虑使用第二种写法。因为这样就可以在脚本尾部，一眼看清楚输出了哪些变量。
// export命令除了输出变量，还可以输出函数或类（class）
export function multiply(x, y) {
  return x * y;
};
// 通常情况下，export输出的变量就是本来的名字，但是可以使用as关键字重命名。
function v1() { ... }
function v2() { ... }
// 重命名后，v2可以用不同的名字输出两次。
export {
  v1 as streamV1,
  v2 as streamV2,
  v2 as streamLatestVersion
};
// 报错
export 1;

// 报错
var m = 1;
export m
// 正确的写法
// 写法一
export var m = 1;

// 写法二
var m = 1;
export {m};

// 写法三
var n = 1;
export {n as m};
// 报错
function f() {}
export f;

// 正确
export function f() {};

// 正确
function f() {}
export {f};
```

另外，`export`语句输出的接口，与其对应的值是动态绑定关系，即通过该接口，可以取到模块内部实时的值。

```javascript
export var foo = 'bar';
setTimeout(() => foo = 'baz', 500);
```

上面代码输出变量`foo`，值为`bar`，500 毫秒之后变成`baz` 这一点与 CommonJS 规范完全不同。CommonJS 模块输出的是值的缓存，不存在动态更新

#### import 命令
> 使用export命令定义了模块的对外接口以后，其他 JS 文件就可以通过import命令加载这个模块。

```javascript
// main.js
import {firstName, lastName, year} from './profile.js';

function setName(element) {
  element.textContent = firstName + ' ' + lastName;
}
```

用于加载`profile.js`文件，并从中输入变量。`import`命令接受一对大括号，里面指定要从其他模块导入的变量名。大括号里面的变量名，必须与被导入模块（`profile.js`）对外接口的名称相同

如果想为输入的变量重新取一个名字，`import`命令要使用`as`关键字，将输入的变量重命名。

```javascript
import { lastName as surname } from './profile.js';
```

`import`命令输入的变量都是只读的，因为它的本质是输入接口。也就是说，不允许在加载模块的脚本里面，改写接口

```javascript
import {a} from './xxx.js'

a = {}; // Syntax Error : 'a' is read-only;
```

上面代码中，脚本加载了变量`a`，对其重新赋值就会报错，因为`a`是一个只读的接口。但是，如果`a`是一个对象，改写`a`的属性是允许的。

```javascript
import {a} from './xxx.js'

a.foo = 'hello'; // 合法操作
```

建议凡是输入的变量，都当作完全只读，轻易不要改变它的属性。

`import`后面的`from`指定模块文件的位置，可以是相对路径，也可以是绝对路径，`.js`后缀可以省略

注意，`import`命令具有提升效果，会提升到整个模块的头部，首先执行

```javascript
foo();

import { foo } from 'my_module';
```

上面的代码不会报错，因为`import`的执行早于`foo`的调用。这种行为的本质是，`import`命令是编译阶段执行的，在代码运行之前

由于`import`是静态执行，所以不能使用表达式和变量，这些只有在运行时才能得到结果的语法结构。

```javascript
// 报错
import { 'f' + 'oo' } from 'my_module';

// 报错
let module = 'my_module';
import { foo } from module;

// 报错
if (x === 1) {
  import { foo } from 'module1';
} else {
  import { foo } from 'module2';
}
```

上面三种写法都会报错，因为它们用到了表达式、变量和`if`结构。在静态分析阶段，这些语法都是没法得到值的。

最后，`import`语句会执行所加载的模块，因此可以有下面的写法。

```javascript
import 'lodash';
```

上面代码仅仅执行`lodash`模块，但是不输入任何值，如果多次重复执行同一句`import`语句，那么只会执行一次，而不会执行多次

```javascript
import 'lodash';
import 'lodash';
```

```javascript
import { foo } from 'my_module';
import { bar } from 'my_module';

// 等同于
import { foo, bar } from 'my_module';
```

目前阶段，通过 Babel 转码，CommonJS 模块的`require`命令和 ES6 模块的`import`命令，可以写在同一个模块里面，但是最好不要这样做。
#### 模块的整体加载

> 除了指定加载某个输出值，还可以使用整体加载，即用星号（`*`）指定一个对象，所有输出值都加载在这个对象上面。

```javascript
/ circle.js

export function area(radius) {
  return Math.PI * radius * radius;
}

export function circumference(radius) {
  return 2 * Math.PI * radius;
}
```

```javascript
// main.js

import { area, circumference } from './circle';

console.log('圆面积：' + area(4));
console.log('圆周长：' + circumference(14));
```

上面写法是逐一指定要加载的方法，整体加载的写法如下

```javascript
import * as circle from './circle';

console.log('圆面积：' + circle.area(4));
console.log('圆周长：' + circle.circumference(14));
```

块整体加载所在的那个对象（上例是`circle`），应该是可以静态分析的，所以不允许运行时改变。下面的写法都是不允许的

```javascript
import * as circle from './circle';

// 下面两行都是不允许的
circle.foo = 'hello';
circle.area = function () {};
```

#### export default 命令

> 从前面的例子可以看出，使用`import`命令的时候，用户需要知道所要加载的变量名或函数名，否则无法加载。但是，用户肯定希望快速上手，未必愿意阅读文档，去了解模块有哪些属性和方法

```javascript
// export-default.js
export default function () {
  console.log('foo');
}
```

上面代码是一个模块文件`export-default.js`，它的默认输出是一个函数

其他模块加载该模块时，`import`命令可以为该匿名函数指定任意名字。

```javascript
// import-default.js
import customName from './export-default';
customName(); // 'foo'
```

上面代码的`import`命令，可以用任意名称指向`export-default.js`输出的方法，这时就不需要知道原模块输出的函数名。需要注意的是，这时`import`命令后面，不使用大括号。

```javascript
// export-default.js
export default function foo() {
  console.log('foo');
}

// 或者写成

function foo() {
  console.log('foo');
}

export default foo;
```

```javascript
// 第一组
export default function crc32() { // 输出
  // ...
}

import crc32 from 'crc32'; // 输入

// 第二组
export function crc32() { // 输出
  // ...
};

import {crc32} from 'crc32'; // 输入
```

第一组是使用`export default`时，对应的`import`语句不需要使用大括号；第二组是不使用`export default`时，对应的`import`语句需要使用大括号。

本质上，`export default`就是输出一个叫做`default`的变量或方法，然后系统允许你为它取任意名字。所以，下面的写法是有效的

```javascript
// modules.js
function add(x, y) {
  return x * y;
}
export {add as default};
// 等同于
// export default add;

// app.js
import { default as foo } from 'modules';
// 等同于
// import foo from 'modules';
```

正是因为`export default`命令其实只是输出一个叫做`default`的变量，所以它后面不能跟变量声明语句。

```javascript
// 正确
export var a = 1;

// 正确
var a = 1;
export default a;

// 错误
export default var a = 1;
```

```javascript
// 正确
export default 42;

// 报错
export 42;
```

有了`export default`命令，输入模块时就非常直观了，以输入 lodash 模块为例。

```javascript
import _ from 'lodash';
```

如果想在一条`import`语句中，同时输入默认方法和其他接口，可以写成下面这样

```javascript
import _, { each, forEach } from 'lodash';
```

对应上面代码的`export`语句如下。

```javascript
export default function (obj) {
  // ···
}

export function each(obj, iterator, context) {
  // ···
}

export { each as forEach };
```

`export default`也可以用来输出类。

```javascript
// MyClass.js
export default class { ... }

// main.js
import MyClass from 'MyClass';
let o = new MyClass();
```

#### export 与 import 的复合写法

如果在一个模块之中，先输入后输出同一个模块，`import`语句可以与`export`语句写在一起。

```javascript
export { foo, bar } from 'my_module';

// 可以简单理解为
import { foo, bar } from 'my_module';
export { foo, bar };
```

export`和`import`语句可以结合在一起，写成一行。但需要注意的是，写成一行以后，`foo`和`bar`实际上并没有被导入当前模块，只是相当于对外转发了这两个接口，导致当前模块不能直接使用`foo`和`bar

```javascript
// 接口改名
export { foo as myFoo } from 'my_module';

// 整体输出
export * from 'my_module';
```

```javascript
export { default } from 'foo';
```

```javascript
export { es6 as default } from './someModule';

// 等同于
import { es6 } from './someModule';
export default es6;
```

```javascript
export { default as es6 } from './someModule';
```

下面三种`import`语句，没有对应的复合写法。

```javascript
import * as someIdentifier from "someModule";
import someIdentifier from "someModule";
import someIdentifier, { namedIdentifier } from "someModule";
```

#### 跨模块常量

`const`声明的常量只在当前代码块有效。如果想设置跨模块的常量（即跨多个文件），或者说一个值要被多个模块共享，可以采用下面的写法。

```javascript
// constants.js 模块
export const A = 1;
export const B = 3;
export const C = 4;

// test1.js 模块
import * as constants from './constants';
console.log(constants.A); // 1
console.log(constants.B); // 3

// test2.js 模块
import {A, B} from './constants';
console.log(A); // 1
console.log(B); // 3
```

如果要使用的常量非常多，可以建一个专门的`constants`目录，将各种常量写在不同的文件里面，保存在该目录下。

```javascript
// constants/db.js
export const db = {
  url: 'http://my.couchdbserver.local:5984',
  admin_username: 'admin',
  admin_password: 'admin password'
};

// constants/user.js
export const users = ['root', 'admin', 'staff', 'ceo', 'chief', 'moderator'];
```

然后，将这些文件输出的常量，合并在`index.js`里面。

```javascript
// constants/index.js
export {db} from './db';
export {users} from './users';
```

使用的时候，直接加载`index.js`就可以了。

```javascript
// script.js
import {db, users} from './constants/index';
```

```javascript
// 报错
if (x === 2) {
  import MyModual from './myModual';
}
```

上面代码中，引擎处理`import`语句是在编译时，这时不会去分析或执行`if`语句，所以`import`语句放在`if`代码块之中毫无意义，因此会报句法错误，而不是执行时错误。也就是说，`import`和`export`命令只能在模块的顶层，不能在代码块之中（比如，在`if`代码块之中，或在函数之中）。

```javascript
const path = './' + fileName;
const myModual = require(path);
```

上面的语句就是动态加载，`require`到底加载哪一个模块，只有运行时才知道。`import`命令做不到这一点。