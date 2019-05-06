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

- **dirname代表当前模块文件所在的文件夹路径，**filename代表当前模块文件所在的文件夹路径+文件名

- require（同步加载）基本功能：读取并执行一个JS文件，然后返回该模块的exports对象，如果没有发现指定模块会报错

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

    ```
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

- ```
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

  ```
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
  
  ---------------------
  
  本文来自 E_li_na 的CSDN 博客 ，全文地址请点击：https://blog.csdn.net/e_li_na/article/details/72082763?utm_source=copy 
  ```


* CMD没有这里严格的要求，它只要依赖的模块在附近就可以了

  ```
  // CMD
  define(function(require, exports, module) {
  var a = require('./a')
  a.doSomething()
  // 此处略去 100 行
  var b = require('./b') // 依赖可以就近书写
  b.doSomething()
  // ... 
  })
  
  ---------------------
  
  本文来自 E_li_na 的CSDN 博客 ，全文地址请点击：https://blog.csdn.net/e_li_na/article/details/72082763?utm_source=copy 
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

```
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

```
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