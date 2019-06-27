# CSS Work Flow

### CSS 预处理器

> 处理特定格式源文件(sass less stylus)到目标css的处理程序

预处理器应必须具有的功能：变量、混合（Mixmin）Extend、嵌套规则、运算、函数、Namespaces & Accessors、Scope、注释

### CSS后处理器

代码经过了一层预处理器，我们还要再接一层，比如 css 的压缩，css 加浏览器的前缀（Autoprefixer），这个就叫做后处理器。`postcss` 前后通吃，一步搞定。

`postcss` 不止能够浏览器厂商加前缀，它是一个平台（借助于插件机制 plugin system），是一个后处理器。至今为止，sass 和 less为什么会挂，是因为浏览器开始支持这些预处理器具有的功能了： 变量、混合（Mixmin）Extend、嵌套规则。。。 。。。sass less终将退出历史的舞台，但是它们也推动了 css 的发展。这一套东西叫： `css next`，现代版的浏览器都基本支持了

### AST

抽象语法树(Abstract Syntax Tree ，AST)作为程序的一种中间表示形式

`https://cssdb.org`可以查看所有的css浏览器的支持情况

