#### 四种前端的部署方式

1. `Vue` 或者 `React` 经过打包📦 -> 一个 `dist` 目录，再经过 `nginx` 反向代理，再到 `Java`

2. Swig等前端模板引擎 + Node + Vue/React -> Java
   1. 通过 `koa-swig` 前端模板，可以在 node 的路由时渲染前端模板时打入 node 的变量

3. Vue/React （同构化）+ Node -> Java
4. Vue/React + Node(真假路由)真路由走 Node ，假路由走 xxx -> java

实践一下第二种，项目结构划分：

* `npm init fcc-news -y` 就会生成`package.json`，管理所有的依赖包
* `assets`—`node`的静态文件目录
* `config`—`node`的配置文件目录
* `controllers`—`node`的路由目录
* `models`—`node`的数据层
* `tests`—`node`的测试目录
* `views`— 组件目录
* `web`---前端目录

项目结构建好之后，就可以各种的安装包开始干活了：`npm install koa --save` `npm install -D supervisor` `npm install cross-env --save`

`--save`的是上线需要的（生产依赖）`--save-dev`是上线不需要的（开发依赖）

`cross-env`这个模块帮助我们在不同平台更好的设置环境变量的，像下面一样去使用

```javascript
"scripts": {
    "build": "cross-env NODE_ENV=production webpack --config build/webpack.config.js"
 }
```

`supervisor` 这个模块帮助我们运行我们的`node`代码，会`watch`我们的文件改动，然后热启动，像下面去使用：

`supervisor`很霸道，`process.exit();`都退出不了

```javascript
"dev": "cross-env NODE_ENV=development supervisor ./app.js",
```

`Koa`要使用哪些 包，我们可以在其 `GitHub`上的 `middleware list`里面去找，比如一个简单的路由中间件`[koa-simple-router]`  我们去安装一下： `npm install koa-simple-router --save`   `router`肯定是上线需要的，所以装为 `-S`

`koa-static` 指定静态文件目录 `log4js`打日志

`koa-swig`前端模板 `node-fetch`用于node层向真正的后端 如：`java`、`php`发请求

生成文档 `jsdoc` `npm run docs`

```javascript
  "scripts": {
    "docs": "jsdoc ./**/*.js -d ./docs/jsdocs"
  },
```

