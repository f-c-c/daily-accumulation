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

```typescript
let something;
something = 'seven';
something = 7;

something.setName('Tom');
// 等价于
let something: any;
something = 'seven';
something = 7;

something.setName('Tom');
```

### 类型推论

- 在声明的时候没有给初始值时会被识别为任意值类型，其实也启动了类型推断，只是推断为 `any`
- 在声明的时候给了初始值，就会启动类型推断，推断为初始值的类型

```typescript
// 以下代码虽然没有指定类型，但是会在编译的时候报错：
let myFavoriteNumber = 'seven';
myFavoriteNumber = 7;
// 事实上，它等价于：
let myFavoriteNumber: string = 'seven';
myFavoriteNumber = 7;
```

### 联合类型

表示取值可以为多种类型中的一种

```typescript
// 这样是没有问题的
let myFavoriteNumber: string | number;
myFavoriteNumber = 'seven';
myFavoriteNumber = 7;
//这里的 let myFavoriteNumber: string | number 的含义是，允许 myFavoriteNumber 的类型是 string 或者 number，但是不能是其他类型。
```

当 TypeScript 不确定一个联合类型的变量到底是哪个类型的时候，我们**只能访问此联合类型的所有类型里共有的属性或方法**：

```typescript
// length 不是共有的属性，会报错
function getLength(something: string | number): number {
    return something.length;
}
// toString() 是共有方法，这是可以的
function getString(something: string | number): string {
    return something.toString();
}
```

### 接口

我们定义了一个接口 `Person`，接着定义了一个变量 `tom`，它的类型是 `Person`。这样，我们就约束了 `tom`的形状必须和接口 `Person`一致

**定义的变量比接口少了一些属性是不允许的**，**多一些属性也是不允许的**， 可见，**赋值的时候，变量的形状必须和接口的形状保持一致**

```typescript
interface Person {
    name: string;
    age: number;
}

let tom: Person = {
    name: 'Tom',
    age: 25
};
```

有时我们希望不要完全匹配一个形状，那么可以用可选属性,可选属性的含义是该属性可以不存在,这时**仍然不允许添加未定义的属性**

```typescript
interface Person {
    name: string;
    age?: number;
}

let tom: Person = {
    name: 'Tom'
};
```

**任意属性**：有时候我们希望一个接口允许有任意的属性，可以使用如下方式

需要注意的是，**一旦定义了任意属性，那么必须确定属性和可选属性的类型都必须是它的类型的子集**：

```typescript
interface Person {
    name: string;
    age?: number;
    [propName: string]: any;
}

let tom: Person = {
    name: 'Tom',
    gender: 'male'
};
// 下面会报错
interface Person {
    name: string;
    age?: number;
    [propName: string]: string;
}

let tom: Person = {
    name: 'Tom',
    age: 25,
    gender: 'male'
};
```

**只读属性**：

```typescript
interface Person {
    readonly id: number;
    name: string;
    age?: number;
    [propName: string]: any;
}

let tom: Person = {
    id: 89757,
    name: 'Tom',
    gender: 'male'
};
// 使用 readonly 定义的属性 id 初始化后，又被赋值了，所以报错了
tom.id = 9527; //error TS2540: Cannot assign to 'id' because it is a read-only property.
```

**注意，只读的约束存在于第一次给对象赋值的时候，而不是第一次给只读属性赋值的时候**：

报错信息有两处，第一处是在对 `tom`进行赋值的时候，没有给 `id`赋值。

第二处是在给 `tom.id`赋值的时候，由于它是只读属性，所以报错了。

```typescript
interface Person {
    readonly id: number;
    name: string;
    age?: number;
    [propName: string]: any;
}

let tom: Person = {
    name: 'Tom',
    gender: 'male'
};

tom.id = 89757;
```

### 数组类型

**类型 + 方括号 表示法**：

```typescript
let fibonacci: number[] = [1, 1, 2, 3, 5];
```

数组的项中**不允许**出现其他的类型：

```typescript
let fibonacci: number[] = [1, '1', 2, 3, 5];
// error TS2322: Type 'string' is not assignable to type 'number'.
```

```typescript
let fibonacci: number[] = [1, 1, 2, 3, 5];
fibonacci.push('8');
// error TS2322: Type 'string' is not assignable to type 'number'.
```

**数组泛型 表示法**：

```typescript
let fibonacci: Array<number> = [1, 1, 2, 3, 5];
```

**接口表示数组**:

```typescript
interface NumberArray {
    [index: number]: number;
}
let fibonacci: NumberArray = [1, 1, 2, 3, 5];
```

`NumberArray`表示：只要索引的类型是数字时，那么值的类型必须是数字。

虽然接口也可以用来描述数组，但是我们一般不会这么做，因为这种方式比前两种方式复杂多了。

不过有一种情况例外，那就是它常用来表示类数组。

**类数组**:

```typescript
function sum() {
    let args: number[] = arguments;
}
//  error TS2740: Type 'IArguments' is missing the following properties from type 'number[]': pop, push, concat, join, and 15 more.
```

上例中，`arguments`实际上是一个类数组，不能用普通的数组的方式来描述，而应该用接口：

```typescript
function sum() {
    let args: {
        [index: number]: number;
        length: number;
        callee: Function;
    } = arguments;
}
```

事实上常用的类数组都有自己的接口定义，如 `IArguments`, `NodeList`, `HTMLCollection`等：

```typescript
function sum() {
    let args: IArguments = arguments;
}
```

其中 `IArguments`是 TypeScript 中定义好了的类型，它实际上就是：

```typescript
interface IArguments {
    [index: number]: any;
    length: number;
    callee: Function;
}
```

**any 在数组中的应用**:

```typescript
let list: any[] = ['a', 25, { a: 123 }];
```

### 函数

**函数声明**：注意，**输入多余的（或者少于要求的）参数，是不被允许的**

```typescript
function sum1(x: number, y: number): number {
    return x + y;
}
// 下面两种都是会报错的
function sum2(x: number, y: number): number {
    return x + y;
}
sum(1, 2, 3);
function sum3(x: number, y: number): number {
    return x + y;
}
sum(1);
```

**函数表达式：**

```typescript
let mySum = function (x: number, y: number): number {
    return x + y;
};
```

这是可以通过编译的，不过事实上，上面的代码只对等号右侧的匿名函数进行了类型定义，而等号左边的 `mySum`，是通过赋值操作进行类型推论而推断出来的。如果需要我们手动给 `mySum`添加类型，则应该是这样：

```typescript
let mySum: (x: number, y: number) => number = function (x: number, y: number): number {
    return x + y;
};
```

注意不要混淆了 TypeScript 中的 `=>`和 ES6 中的 `=>`

在 TypeScript 的类型定义中，`=>`用来表示函数的定义，左边是输入类型，需要用括号括起来，右边是输出类型

在 ES6 中，`=>`叫做箭头函数

**用接口定义函数的形状:**

```typescript
interface SearchFunc {
    (source: string, subString: string): boolean;
}

let mySearch: SearchFunc;
mySearch = function(source: string, subString: string) {
    return source.search(subString) !== -1;
}
```

**可选参数:**

```typescript
function buildName(firstName: string, lastName?: string) {
    if (lastName) {
        return firstName + ' ' + lastName;
    } else {
        return firstName;
    }
}
let tomcat = buildName('Tom', 'Cat');
let tom = buildName('Tom');
```

需要注意的是，可选参数必须接在必需参数后面。换句话说，**可选参数后面不允许再出现必需参数了**：

```typescript
function buildName(firstName?: string, lastName: string) {
    if (firstName) {
        return firstName + ' ' + lastName;
    } else {
        return lastName;
    }
}
let tomcat = buildName('Tom', 'Cat');
let tom = buildName(undefined, 'Tom');

// index.ts(1,40): error TS1016: A required parameter cannot follow an optional parameter.
```

**参数默认值:**

在 ES6 中，我们允许给函数的参数添加默认值，**TypeScript 会将添加了默认值的参数识别为可选参数**：

```typescript
function buildName(firstName: string, lastName: string = 'Cat') {
    return firstName + ' ' + lastName;
}
let tomcat = buildName('Tom', 'Cat');
let tom = buildName('Tom');
```

此时就不受「可选参数必须接在必需参数后面」的限制了：

```typescript
function buildName(firstName: string = 'Tom', lastName: string) {
    return firstName + ' ' + lastName;
}
let tomcat = buildName('Tom', 'Cat');
let cat = buildName(undefined, 'Cat');
```

**剩余参数:** 注意，rest 参数只能是最后一个参数

```typescript
function push(array: any[], ...items: any[]) {
    items.forEach(function(item) {
        array.push(item);
    });
}

let a = [];
push(a, 1, 2, 3);
```

### 类型断言

语法： *<类型>值*  或 *值 as类型*

在需要断言的变量前加上 `<Type>`即可

```typescript
function getLength(something: string | number): number {
    if ((<string>something).length) {
        return (<string>something).length;
    } else {
        return something.toString().length;
    }
}
```

**类型断言不是类型转换，断言成一个联合类型中不存在的类型是不允许的**：

```typescript
function toBoolean(something: string | number): boolean {
    return <boolean>something;
}

// index.ts(2,10): error TS2352: Type 'string | number' cannot be converted to type 'boolean'.
//   Type 'number' is not comparable to type 'boolean'.
```

