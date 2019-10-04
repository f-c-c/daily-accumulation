# 比较 webpack 打包commonjs 和 es6

本文做的事情：采用两个文件`./src/index.js` `./src/test.js`，模块的导入与导出分别采用 `commonjs` 和 `es6规范`，比较`webpack` 打包结果 `./dist/main.js`

### 采用 commonjs 模块

`./src/index.js`：

```javascript
console.log("index.js start");

var mod = require('./test'); 
console.log(mod.counter);  // 3
mod.incCounter();// 尝试修改 counter
console.log(mod.counter); // 还是 3
var mod2 = require('./test'); // 再次引入
console.log(mod2.counter);// 看结果还是 3

console.log("index.js end");
```

`./src/test.js`：

```javascript
console.log('test.js start');

var counter = 3;
function incCounter() {  
    console.log('尝试修改counter++');
	counter++;
}
module.exports = {  
	counter: counter,  incCounter: incCounter
};

console.log('test.js end');
```

输出：

```javascript
index.js start
test.js start
test.js end
3
尝试修改counter++
3
3
index.js end
```

#### 小结论

- 可以看出：经过` webpack` 打包过后，将我们写的具有相互引用关系的两个文件给**抹平了**（没有相互引用关系了），`webpack` 利用了一个` IIFE`，传入了一个对象（这个对象的键对应的就是我们的文件名`./src/index.js` `./src/test.js`，值对应的就是经过`webpack`转换的每一个文件里面的代码）,通过一个闭包 做了一个缓冲（`installedModules`）保证我们多次引用同一个文件时直接走缓存，并且这里我们导入 `test.js`后尝试修改`var counter = 3;`，发现是可以修改的，但是在导出时：`counter: counter` 这里的是基本类型是值的拷贝，内部的变量`var counter`的值再改变是影响不到这里的，这里很坑，很多人说`commonjs`输出的是值的拷贝，再次改变变量影响不到外面（我认为这是有待探讨的）如：我们导出一个引用类型：结果是改变了的（故这里我认为：能不能改变要看导出的是什么类型，如果是基本类型，是不能改变的，应该是值的拷贝，如果导出的是引用类型，是可以改变引用类型里面的属性的）

 ```javascript
    // test.js
    var obj = {
        a: 1
    }
    function change() {  
        obj.a += 1;
    }
    module.exports = {  
        obj,
        change
    };
    // index.js
    var mod = require('./test'); 
    
    console.log('mod.obj.a', mod.obj.a);// 1
    mod.change();// 尝试修改
    console.log('mod.obj.a', mod.obj.a);// 2
 ```


再次针对这个问题衍生一下：(像下面这样直接改obj)也是不可以的，很简单，因为在导出那一刻obj指向的是一个对象，之后再改了 `var obj` 指向别的对象，但是在导出那一刻obj指向是不会变的，这里还是值的拷贝（只不过拷贝的是一个引用地址）

```javascript
// index.js
var mod = require('./test'); 

console.log('mod.obj.a', mod.obj.a);// 1
mod.changeObj();// 尝试修改
console.log('mod.obj.a', mod.obj.a);// 1
// test.js
var obj = {
    a: 1
}
function changeObj() {  
    obj = {
        a: 100
    }
}
module.exports = {  
    obj,
    changeObj
};
```



`./dist/main.js`  webpack 打包结果的关键代码：

 ```javascript
(function (modules) {
  var installedModules = {};
  function __webpack_require__(moduleId) {
    if (installedModules[moduleId]) {
      return installedModules[moduleId].exports;
    }
    var module = installedModules[moduleId] = {
      i: moduleId,
      l: false,
      exports: {}
    };
    modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
    module.l = true;
    return module.exports;
  }
  // Load entry module and return exports
  return __webpack_require__(__webpack_require__.s = "./src/index.js");
})
  ({
    "./src/index.js":
      (function (module, exports, __webpack_require__) {
        eval("console.log(\"index.js start\")\nvar mod = __webpack_require__(/*! ./test */ \"./src/test.js\"); \nconsole.log(mod.counter);  // 3\nmod.incCounter();// 尝试修改 counter\nconsole.log(mod.counter); // 还是 3\nvar mod2 = __webpack_require__(/*! ./test */ \"./src/test.js\"); // 再次引入\nconsole.log(mod2.counter);// 看结果还是 3\n\n\nconsole.log(\"index.js end\")\n\n//# sourceURL=webpack:///./src/index.js?");
      }),

    "./src/test.js":
      (function (module, exports) {
        eval("console.log('test.js start');\nvar counter = 3;\nfunction incCounter() {  \n    console.log('尝试修改counter++');\n\tcounter++;\n}\nmodule.exports = {  \n\tcounter: counter,  incCounter: incCounter\n};\nconsole.log('test.js end');\n\n//# sourceURL=webpack:///./src/test.js?");
      })
  });
 ```

分析一下这个 `./dist/main.js` 的执行流程 我们就能很清楚上面的输出结果了：

- 第一步执行 `Load entry module and return exports`

  - `return __webpack_require__(__webpack_require__.s = "./src/index.js");`

  - 也就是 `__webpack_require__("./src/index.js")`，调用 webpack 自己封装的 `__webpack_require__` 并传入 入口文件 `"./src/index.js"`，第一次进发现没有缓存，走👇添加缓存：

  - ```javascript
        var module = installedModules[moduleId] = {
          i: moduleId,
          l: false,
          exports: {}
        };
    ```

  - 接着是：`modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);` 

  - 相当于调用了传入整个 `IIFE` 的对象的  `"./src/index.js"`键对应的函数，并绑定`this`为 `module.exports`,传入参数有 `module, module.exports, __webpack_require__`

  - 接着我们就去分析`"./src/index.js"`键对应的函数(也就是那个 `eval`) 整理后的eval里面的代码：

  - ```javascript
            console.log("index.js start");
            var mod = __webpack_require__("./src/test.js");
            console.log(mod.counter);
            mod.incCounter();
            console.log(mod.counter);
            var mod2 = __webpack_require__("./src/test.js");
            console.log(mod2.counter);
            // 看结果还是 3\n\n\n
            console.log("index.js end")//# sourceURL=webpack:///./src/index.js?
    ```

  - `"./src/test.js"`键对应的函数(也就是那个 `eval`) 整理后的eval里面的代码：

  - ```javascript
            console.log('test.js start');
            var counter = 3;
            function incCounter() {
              console.log('尝试修改counter++');
              counter++;
            }
            module.exports = {
              counter: counter, incCounter: incCounter
            };
            console.log('test.js end');//# sourceURL=webpack:///./src/test.js?
    ```

  - 可以清晰的看出，这代码似曾相识，和我们写的 `./src/index.js`,`./src/test.js`很像，但又有一些区别，通过比较我们发现，`webpack` 只是简单的将我们写的 `require` 语句给替换成了它自己封装的函数 `__webpack_require__`尾巴上加上了关于sourcemap的东西`#sourceURL=webpack:///./src/test.js?`

  - 跟随👆的代码逻辑，我们能很清楚的知道一开始的输出结果是怎么来的了，这里不再赘述

#### 采用 es6 模块

`./src/index.js`

```javascript
console.log("index.js start");

import {counter, incCounter} from './test';
console.log(counter);
incCounter();// 尝试修改 counter
console.log(counter); 

console.log("index.js end");
```

`./src/test.js`

```javascript
console.log('test.js start');

export var counter = 3;
export function incCounter() {
    console.log('尝试修改counter++');
    counter++;
}

console.log('test.js end');
```

运行webpack打包后的 `./dist/main.js`输出结果:

```javascript
test.js start
test.js end
index.js start
3
尝试修改counter++
4
index.js end
```

面对这个结果，至少对于我来说，有两个困惑：

1. 为什么会先输出 `test.js start` 和 `test.js end`，明明 `./src/index.js`才是入口文件，为什么 `index.js start` 没有最先输出出来，而是引用的文件先执行了？
2. 为何 3->4 这个可以修改？

呆着这两个问题，我打开了编译结果文件 `./dist/main.js` 慢慢分析（删除无关注释，不重要的代码，取出精华部分）：

```javascript
(function (modules) {
  var installedModules = {};
  function __webpack_require__(moduleId) {
    if (installedModules[moduleId]) {
      return installedModules[moduleId].exports;
    }
    var module = installedModules[moduleId] = {
      i: moduleId,
      l: false,
      exports: {}
    };
    modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
    module.l = true;
    // Return the exports of the module
    return module.exports;
  }
  __webpack_require__.m = modules;
  __webpack_require__.c = installedModules;
  __webpack_require__.d = function (exports, name, getter) {
    if (!__webpack_require__.o(exports, name)) {
      Object.defineProperty(exports, name, { enumerable: true, get: getter });
    }
  };
  __webpack_require__.r = function (exports) {
    if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
      Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
    }
    Object.defineProperty(exports, '__esModule', { value: true });
  };
  __webpack_require__.t = function (value, mode) {
    if (mode & 1) value = __webpack_require__(value);
    if (mode & 8) return value;
    if ((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
    var ns = Object.create(null);
    __webpack_require__.r(ns);
    Object.defineProperty(ns, 'default', { enumerable: true, value: value });
    if (mode & 2 && typeof value != 'string') for (var key in value) __webpack_require__.d(ns, key, function (key) { return value[key]; }.bind(null, key));
    return ns;
  };
  __webpack_require__.n = function (module) {
    var getter = module && module.__esModule ?
      function getDefault() { return module['default']; } :
      function getModuleExports() { return module; };
    __webpack_require__.d(getter, 'a', getter);
    return getter;
  };
  __webpack_require__.o = function (object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
  __webpack_require__.p = "";
  return __webpack_require__(__webpack_require__.s = "./src/index.js");
})
  ({
    "./src/index.js":
      (function (module, __webpack_exports__, __webpack_require__) {
        "use strict";
        eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _test__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./test */ \"./src/test.js\");\nconsole.log(\"index.js start\");\n\n\nconsole.log(_test__WEBPACK_IMPORTED_MODULE_0__[\"counter\"]);\nObject(_test__WEBPACK_IMPORTED_MODULE_0__[\"incCounter\"])();// 尝试修改 counter\nconsole.log(_test__WEBPACK_IMPORTED_MODULE_0__[\"counter\"]); \n\nconsole.log(\"index.js end\");\n\n//# sourceURL=webpack:///./src/index.js?");
      }),
    "./src/test.js":
      (function (module, __webpack_exports__, __webpack_require__) {
        "use strict";
        eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"counter\", function() { return counter; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"incCounter\", function() { return incCounter; });\nconsole.log('test.js start');\n\nvar counter = 3;\nfunction incCounter() {\n    console.log('尝试修改counter++');\n    counter++;\n}\n\nconsole.log('test.js end');\n\n//# sourceURL=webpack:///./src/test.js?");
      })
  });
```

有了 先前的经验，我们对这个 webpack 打出来的文件 已经很熟悉了：一个IIFE传了一个对象进去。

我们直接研究一下两个 `eval` 里面的东西：

`./src/index.js`对应的`eval`:

```javascript
        var _test__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("./src/test.js");
        console.log("index.js start");
        console.log(_test__WEBPACK_IMPORTED_MODULE_0__["counter"]);
        Object(_test__WEBPACK_IMPORTED_MODULE_0__["incCounter"])();
        console.log(_test__WEBPACK_IMPORTED_MODULE_0__["counter"]);
        console.log("index.js end");
```

`./src/test.js`对应的`eval`:

```javascript
      __webpack_require__.d(__webpack_exports__, "counter", function() { return counter; });
        __webpack_require__.d(__webpack_exports__, "incCounter", function() { return incCounter; });
        console.log('test.js start');
        var counter = 3;
        function incCounter() {
          console.log('尝试修改counter++');
          counter++;
        }
        console.log('test.js end');
        //# sourceURL=webpack:///./src/test.js?");
```

- 首先：webpack 把我们的 `import {counter, incCounter} from './test';` 换成了 `__webpack_require__("./src/test.js");`，并且放到代码头部了（在任何其他代码），这就很好的解释了之前我们的困惑1
- 代码流程：
  - 最先是运行 `./src/index.js` 的 `eval`,里面第一句调用了`__webpack_require__("./src/test.js")`,故而转而运行 `./src/test.js` 的 `eval`，这里面。`__webpack_require__.d`函数的作用：给 `module.exports` 利用 `Object.defineProperty()`添加了拦截，拦截了`get`操作。
  - 接着输出`console.log('test.js start');`
  - 输出：`console.log('test.js end');`
  -  `./src/test.js` 的 `eval`执行完毕，其结果 `module.exports`返回到了 `./src/index.js` 的 `eval`:`var _test__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("./src/test.js");`,现在代码开始再往下执行：
  - 输出： `console.log("index.js start");`
  - `_test__WEBPACK_IMPORTED_MODULE_0__["counter"]`触发了 get 返回 `var counter `的值 3
  - `Object(_test__WEBPACK_IMPORTED_MODULE_0__["incCounter"])();` 这里的也触发了 `incCounter`属性的 `get`操作，返回一个函数并执行了改函数（改变了`var counter `的值 为4），至于这里为何将一个函数用 `Object()` 包一下，有何好处？这里webpack给包一下的目的性我还不是很清楚。
  - `_test__WEBPACK_IMPORTED_MODULE_0__["counter"]`又触发了 counter 的get操作 返回 `var counter `的值 4
  - 最后输出 `console.log("index.js end");` 代码运行结束
  

到此为止，两个困惑均得到解决：
- 其实第一个困惑主要是 webpack 将我们的 `import `语法替换并且提前到代码头行了；
- 第二个困惑主要是 webpack 利用了`Object.definePropeoty` 做了拦截 get 操作，每次读取时都返回`var counter `的值，而这个变量的值当然是可以动态修改的

