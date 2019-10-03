# webpack 机制

首先我们安装webpack：`sudo npm install webpack webpack-cli --save-dev`

接着我们在 `./src/index.js`里面写下一点点东西 `console.log('000');`

再接着我们写一个`package.json`命令来启动`webpack`：

```javascript
  "scripts": {
    "dev": "webpack --mode development"
  },
```

连 `webpack.config.js`我们都不需要，就完成上述配置，我们就可以用命令`npm run dev`打包了，会打一个 `dist/main.js`出来，我们去分析一下这个`main.js`，删掉注释该文件大概长这样：

```javascript
(function (modules) {
... ...
})({
    "./src/index.js":
      (function (module, exports) {
        eval("console.log('000');\n\n//# sourceURL=webpack:///./src/index.js?");
      })
  });
```

其实就是一个 立即执行函数，传入了一个对象： `./src/index.js` 作为`key`，一个函数作为值的对象

```
{
    "./src/index.js":
      (function (module, exports) {
        eval("console.log('000');\n\n//# sourceURL=webpack:///./src/index.js?");
      })
  }
```



