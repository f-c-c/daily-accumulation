# 比较 webpack 打包commonjs 和 es6 规范

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

可以看出：经过` webpack` 打包过后，将我们写的具有相互引用关系的两个文件给**抹平了**（没有相互引用关系了），`webpack` 利用了一个` IIFE`，传入了一个对象（这个对象的键对应的就是我们的文件名`./src/index.js` `./src/test.js`，值对应的就是经过`webpack`转换的每一个文件里面的代码）,通过一个闭包 做了一个缓冲（`installedModules`）保证我们多次引用同一个文件时直接走缓存

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



