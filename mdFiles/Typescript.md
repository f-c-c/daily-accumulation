# Typescript

> TypeScript 是 **JavaScript 的类型的超集**，它可以编译成纯 JavaScript。编译出来的 JavaScript 可以运行在任何浏览器上。TypeScript 编译工具可以运行在任何服务器和任何系统上。TypeScript 是开源的。

### TypeScript 的一些优势

> 类型系统实际上是最好的文档
>
> 即使 TypeScript 编译报错，也可以生成 JavaScript 文件
>
> 社区活跃，angular从 2开始 也就是 typescript 写的
>
> TypeScript 拥抱了 ES6 规范，也支持部分 ESNext 草案的规范

### TypeScript 的缺点

>  有一定的学习成本，需要理解接口（Interfaces）、泛型（Generics）、类（Classes）、枚举类型（Enums）等前端工程师可能不是很熟悉的概念

> 短期可能会增加一些开发成本，毕竟要多写一些类型的定义，不过对于一个需要长期维护的项目，TypeScript 能够减少其维护成本

> 集成到构建流程需要一些工作量

### 安装 TypeScript

`npm install -g typescript` 该命令会在全局环境下安装 `tsc`命令，安装完成之后，我们就可以在任何地方执行 `tsc`命令： `tsc hello.ts` 这时候会生成一个编译好的文件 `hello.js` TypeScript 只会进行静态检查，如果发现有错误，编译的时候就会报错，TypeScript 编译的时候即使报错了，还是会生成编译结果

### 约定

> 使用 TypeScript 编写的文件以 `.ts`为后缀，用 TypeScript 编写 React 时，以 `.tsx`为后缀

### 编辑器

`VSCode` 它本身也是 TypeScript 写的

### 布尔值

