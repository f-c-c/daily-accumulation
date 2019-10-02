# Loader 原理

先看一个简单的`loader`：`markdown-loader`: 其实就是一个 **导出为一个函数的 node 模块**

```javascript
"use strict";

const marked = require("marked");
const loaderUtils = require("loader-utils");

module.exports = function (markdown) {
    // merge params and default config
    const options = loaderUtils.getOptions(this);

    this.cacheable();

    marked.setOptions(options);

    return marked(markdown);
};
```

- 原理就是：对已有文件->通过正则或者ast(再遍历这个树，修改节点)->返回一个新的字符串

- 大部分现有的 loader 都是用的 ast（静态语法分析树） 转ast的工具有很多种，但都大同小异

- loader 是以相反的顺序执行的，最后的 loader 最早调用，中间的 loader 传入的是上一个loader的处理结果
  - `use: ['bar-loader', 'foo-loader'],`先执行`bar-loader` 的前置钩子(pitch)，再执行`foo-loader`的前置钩子(pitch)，再执行`foo-loader` 再执行`bar-loader`这两 `loader`

- 若修改很简单，我们可以直接正则匹配，但是当改动很复杂时，正则就hold不住了，必须上 ast

- loader一般是成对出现：`less-loader less` `babel-loader babel`

### ast 例子

> ast 就是一颗树（一个对象），把我们写的代码翻译为了一个对象（ast树）

介绍一个ast分析工具：`sudo npm install acorn acorn-walk --save-dev` webpack 里面的转ast也是用的acorn

`testAst.js`  `node testAst.js`

```javascript
const code = "const name = 'liuhao'";
let acorn = require("acorn");
const walk = require("acorn-walk");
const result = acorn.parse(code);// 转为ast
console.log(result);
walk.simple(result,{
    Literal(node) {
        console.log(`found a literal: ${node.value}`);// found a literal: liuhao
    }
})
// 可以看到 acorn 把我们的代码转为了ast ，然后输出了其中一个节点的值
```

下面是输出的 ast ：

```
Node {
  type: 'Program',
  start: 0,
  end: 21,
  body:
   [ Node {
       type: 'VariableDeclaration',
       start: 0,
       end: 21,
       declarations: [Array],
       kind: 'const' } ],
  sourceType: 'script' }
```

再介绍一个ast分析工具：

`sudo npm install esprima --save-dev` esprima 是比较老牌的工具，acorn是比较新的工具

其实看看 这些 工具其内部还是大量的运用了 正则

```javascript
const code = "const name = 'liuhao'";
let esprima = require("esprima");

const result = esprima.parse(code);
console.log(result);

```

ast 结果：(不同的ast工具生成的ast略有不同)

```
Script {
  type: 'Program',
  body:
   [ VariableDeclaration {
       type: 'VariableDeclaration',
       declarations: [
       {
    		type: 'VariableDeclarator',
    		id: Identifier { type: 'Identifier', name: 'name' },
    	init:
     		Literal { type: 'Literal', value: 'liuhao', raw: "'liuhao'" } } 
       ],
       kind: 'const' } ],
  sourceType: 'script' }
```

webpack 编译会走 ast ，就很容易做 tree-shaking 了，

- 上面的 esprima 可以将我们的代码转为 ast ，接着 `npm install estraverse --save-dev` 可以遍历我们的 ast (修改ast树)

- `npm install escodegen --save-dev` 可以将我们的 ast 转回 代码
- 下面👇的代码实现了将`"const name = 'liuhao'"` ->ast树（遍历树，修改节点）->`"var name = 'liuhao'"`,这不就是Babel编译原理吗
- v8 里面在解释js的时候也是用的 ast，没有ast怎么知道我们的代码干了啥，谁知道我们写的是啥

```javascript
const code = "const name = 'liuhao'";
let esprima = require("esprima");
let estraverse = require("estraverse");
let escodegen = require("escodegen");

const result = esprima.parse(code);// 将代码转为 ast 

// 自己写一个小 babel  遍历 ast
estraverse.traverse(result, {
    enter: function (node, parent) {
        if (node.type == 'VariableDeclaration') {
            node.kind = 'var'// 改变ast 遍历判断-》将变量声明全部 修改为 var
        }
    },
    // leave: function (node, parent) {
    //     if (node.type == 'VariableDeclarator')
    //       console.log(node.id.name);
    // }
});
let generated_code = escodegen.generate(result);//利用  escodegen  将ast 吐出来为代码
console.log(generated_code);//var name = 'liuhao';
```

### 自己写一个简单的 loader

- `mkdir my-webpack-loader && cd my-webpack-loader`

- `sudo npm install webpack webpack-cli loader-utils --save-dev`

- `package.json:`

- ```javascript
    "scripts": {
      "dev": "webpack --mode development"
    },
  ```

- `webpack.config.js:`

- ```javascript
  const path = require('path');
  module.exports = {
      module: {
          rules: [
              {
                  test: /\.js$/i,
                  loader: path.join(__dirname, 'loader/index.js'),
                  options: {
                      item: "哈哈哈"
                  }
              }
          ]
      }
  }
  ```

- `src/index.js:`

- ```javascript
  console.log('🐭');
  ```

- `loader/index.js:`

- ```javascript
  const loaderUtils = require('loader-utils');
  module.exports = function(content, map, meta) {
      let myOptions = loaderUtils.getOptions(this);
      console.log('myOptions', myOptions);// myOptions { item: '哈哈哈' }
      // 这里把 acorn 弄进来想干嘛干嘛 
      return content + this.data.value
  }
  // 前置钩子
  module.exports.pitch = function(f,s,data) {
      data.value = '123456'
  }
  ```

- 最后一步：`npm run dev`

- 查看`dist/main.js:`

- ```javascript
  eval("console.log('000');12356\n\n//# sourceURL=webpack:///./src/index.js?");
  ```

- 我们的`loader`生效了，在`console.log('🐭');123` 后面这个123 是我们的loader加上的，当然这只是演示一个`loader`该怎么写，实际情况，我们应该`loader`里面加上面说到的`acorn`等工具，转`ast`再进行操作