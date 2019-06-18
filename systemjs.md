# systemjs 万能模块加载器

```javascript
class Test{
    init() {
        console.log("初始化");
    }
}
export default Test;
```
* 首先，针对上述代码，如果我们直接用 babel 编译，html 文件直接引用的话，浏览器会报错的：

```
Uncaught ReferenceError: exports is not defined
```

webpack 打包后浏览器怎么会认识？

这个时候我们需要用 systemjs 去编译babel编译的东西，以后做微前端时非常有用，浏览器认为 es6 module

`npm install --save-dev @babel/plugin-transform-modules-systemjs`

并且添加到 babel 的配置文件 .babelrc

```json
{
    "presets": ["@babel/preset-env"],
    "plugins": ["@babel/plugin-transform-modules-systemjs"]
}
```

```json
  "scripts": {
    "test": "echo \"Error: no test specified\" && exit 1",
    "dev": "webpack --mode development",
    "build": "babel ./src/index.js --out-file ./src/index-compiled.js"
  },
```

我们再。`npm run build`用 babel 编译一下，再引用编译后的文件就没有问题了。这里我们用 `http-server`起一个服务，访问 html 不会出现跨域

这个东西非常非常有用，如果整个网站不上webpack这些东西，这个就可以作为整个网站的模块加载器了

```javascript
<body>
    <script type="module">
        import Test from "./src/index.js";
        new Test().init();
    </script>
    <script nomodule src="./src/index-compiled.js"></script>
    <script nomodule>
         System.import('./src/index-compiled.js')
             .then((_) => {
                 let test = new _.default();
                 console.log(test.init());
             })
    </script>
</body>
```

type="module" 表示浏览器是否支持 es6 的 module 如果支持则直接 import 

如果不支持（比如在ie中）则采用system去加载 babel编译后的文件，这就是活啊 