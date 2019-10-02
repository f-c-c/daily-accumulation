# webpack plugin

先看官网对 plugin 的描述：

> webpack **插件**是一个具有 [apply] 方法的 JavaScript 对象。`apply` 方法会被 webpack compiler 调用，并且 compiler 对象可在**整个**编译生命周期访问。

一个打日志的简单插件：**ConsoleLogOnBuildWebpackPlugin.js**:

```javascript
const pluginName = 'ConsoleLogOnBuildWebpackPlugin';

class ConsoleLogOnBuildWebpackPlugin {
  apply(compiler) {
    compiler.hooks.run.tap(pluginName, compilation => {
      console.log('webpack 构建过程开始！');
    });
  }
}
```

初次看到这个代码，是很懵的，apply方法为什么会被webpack compiler 调用？？？ compiler 又是什么鬼？？？

compiler.hooks.run.tap 这一串又是啥玩意？？？ 不懂