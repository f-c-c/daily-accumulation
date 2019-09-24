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

TypeScript 中，使用 `:`指定变量的类型，`:`的前后有没有空格都可以

### 约定

> 使用 TypeScript 编写的文件以 `.ts`为后缀，用 TypeScript 编写 React 时，以 `.tsx`为后缀

### 编辑器

`VSCode` 它本身也是 TypeScript 写的

### 布尔值

```typescript
let isDone: boolean = false;
```

```typescript
let bool: boolean = new Boolean(true);
// boolean' is a primitive, but 'Boolean' is a wrapper object. Prefer using 'boolean' when possible.
```

```typescript
let boolObject: Boolean = new Boolean(1);
```

### 数值

```typescript
let decLiteral: number = 6;
let hexLiteral: number = 0xf00d;
// ES6 中的二进制表示法
let binaryLiteral: number = 0b1010;
// ES6 中的八进制表示法
let octalLiteral: number = 0o744;
let notANumber: number = NaN;
let infinityNumber: number = Infinity;

// 编译结果：
//  ES6 中的二进制和八进制表示法，它们会被编译为十进制数字。
var decLiteral = 6;
var hexLiteral = 0xf00d;
// ES6 中的二进制表示法
var binaryLiteral = 10;
// ES6 中的八进制表示法
var octalLiteral = 484;
var notANumber = NaN;
var infinityNumber = Infinity;
```

### 字符串

```typescript
let myName: string = 'Tom';
let myAge: number = 25;

// 模板字符串
let sentence: string = `Hello, my name is ${myName}.
I'll be ${myAge + 1} years old next month.`;

// 编译结果：
var myName = 'Tom';
var myAge = 25;
// 模板字符串
var sentence = "Hello, my name is " + myName + ".\nI'll be " + (myAge + 1) + " years old next month.";

```

### 空值

JavaScript 没有空值（Void）的概念，在 TypeScript 中，可以用 `void`表示没有任何返回值的函数：

```typescript
function alertName(): void {
    alert('My name is Tom');
}
// 编译结果：
function alertName() {
    alert('My name is Tom');
}
```
声明一个 void 类型的变量没有什么用，因为你只能将它赋值为 undefined 和 null：
```typescript
let unusable: void = undefined;
let unusable1: void = null;
```

### Null 和 Undefined

```typescript
let u: undefined = undefined;
let n: null = null;
```

与 `void`的区别是，`undefined`和 `null`是所有类型的子类型。下面都是可以的

```typescript

let num: number = undefined;
let num1: number = null;

let str: string = undefined;
let str1: string = null;

let bool: boolean = undefined;
let bool1: boolean = null;

let null0: null = undefined;
let null1: null = null;

let nude0: undefined = undefined;
let unde1: undefined = null;
```

### Any

任意值（Any）用来表示允许赋值为任意类型，一个普通类型，在赋值过程中改变类型是不被允许的

```typescript
let num: number = undefined;
num = '7';
//  error TS2322: Type '"7"' is not assignable to type 'number'.
```

如果是 `any`类型，则允许被赋值为任意类型，就像在js里面一样变量可以随意被改变

```typescript
let num: any = undefined;
num = '7';
num = true;
num = null;
num = 9;
```

在任意值上访问任何属性，调用任何方法都是允许的：

```typescript
let anyThing: any = 'hello';
console.log(anyThing.myName);
console.log(anyThing.myName.firstName);
// 编译结果：可以看出ts编译不会报错，但是执行编译的js是会报错的
var anyThing = 'hello';
console.log(anyThing.myName);
console.log(anyThing.myName.firstName);
```

```typescript
let anyThing: any = 'Tom';
anyThing.setName('Jerry');
anyThing.setName('Jerry').sayHello();
anyThing.myName.setFirstName('Cat');
```

可以认为，**声明一个变量为任意值之后，对它的任何操作，返回的内容的类型都是任意值**。

既然我们都使用 `typescript` 了，再用 `any`就没有意思了。

**变量如果在声明的时候，未指定其类型，那么它会被识别为任意值类型**