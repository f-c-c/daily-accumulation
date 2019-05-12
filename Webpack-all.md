# Webpack
> webpack 是一个现代 JavaScript 应用程序的静态模块打包工具

`webpack 4`以上版本要求安装`webpack-cli`

`npm install webpack --save-dev` `npm install webpack-cli --save-dev`


### webpack 🉐️模式

`production` （默认） 、`development`、`none`

```javascript
module.exports = {
  mode: 'production'
};

// or
webpack --mode=production
```

webpack4在`production`模式下，会启用众多优化插件

会将 `DefinePlugin` 中 `process.env.NODE_ENV` 的值设置为 `production`。启用 `FlagDependencyUsagePlugin`, `FlagIncludedChunksPlugin`, `ModuleConcatenationPlugin`, `NoEmitOnErrorsPlugin`, `OccurrenceOrderPlugin`, `SideEffectsFlagPlugin` 和 `TerserPlugin`。

`development`模式下，会将 `DefinePlugin` 中 `process.env.NODE_ENV` 的值设置为 `development`。启用 `NamedChunksPlugin` 和 `NamedModulesPlugin`。

最直观的：**production模式打包时，自动会启用JS Tree Sharking和文件压缩，导致最终的打包结果文件大小小很多** **development模式下不会JS Tree Sharking和文件压缩** 

`import {fn0, fn1} from './a';` 这样写在 `dev`的时候不管是否使用到 fn0 和 fn1 都会被打包进去，在`prod`模式下 如果没使用到就不会打包📦

none模式下，webpack不会使用任何内置优化

### 使用别名

```json
    resolve: {
        alias: {
            '$self-component': path.resolve(__dirname, 'src/component/'),
            '$self-util': path.resolve(__dirname, 'src/util/'),
            '$self-yo-component': path.resolve(__dirname, 'src/yo-component/'),
            '$self-config': path.resolve(__dirname, 'src/config/'),
            '$self-yo-config': path.resolve(__dirname, 'src/yo-config/'),
            '~yo3': path.resolve(__dirname, 'src/yo-config/')
        },
        extensions: [ '.js', '.scss', '.json' ]
    }
```

### 忽略对已知模块解析

```
{
	module: [
		noParse: ['/moment-with-locales/']
	]
}
```

### 将模块暴露在全局

`expose-loader` or `ProvidePlugin` 暴露在全局后在使用时就不需要引入了，全局变量就存在了

### 提起公共代码

`CommonsChunkPlugin` 如何判定是否公共模块：`minChunks`至少被引用多少次就认为是公共模块 

### 配置全局开关

```javascript
plugins: [
	new webpack.DefinePlugin({
		DEBUG: true
	})
]
const Constant = {
	API_HOST: DEBUG ? "http://10.13.xxx.xxx:8080" : ""
}
```

### 单独打包CSS

```json
plugins: [
	new ExtractTextPlugin("[name].[hash:8].css", {
		allChunks: true
	})
]
```

