# Strange knowledge of js

### 关于js里面数字问题

```javascript
console.log(isFinite(Number.MAX_VALUE + 1e291)) // true
console.log(isFinite(Number.MAX_VALUE + 1e292)) // false
// 当运算数和数字最大值保持在相同精度维度上时，才可与数字最大值发生运算
console.log(Number.MAX_VALUE+1e291 === Number.MAX_VALUE); //true
console.log(Number.MAX_VALUE+1e292 === Number.MAX_VALUE); //false

// IEEE754格式表示数字，浮点数不是精确值
console.log(0.1 + 0.2); //0.30000000000000004

// 0 +0 -0 都是恒等的
console.log(0 === -0) //true
console.log(0 === +0) //true
console.log(-0 === +0) //true

// 关于 NaN
// 0/0 得到NaN，正数/0 得到正无穷（Infinity），负数/0 得到负无穷（-Infinity）
// NaN 与任何值都不相等包括它自己，任何涉及 NaN 的操作也将返回NaN
console.log(0/0); //NaN
console.log(5/0); //Infinity
console.log(-5/0); //-Infinity
console.log(NaN == NaN);// false
console.log(NaN + 1);// NaN

// 关于将 parseInt 传给数组的 map
console.log(parseInt(1,0)); // 1
console.log(parseInt(2,1)); // NaN
console.log(parseInt(3,2)); // NaN
console.log([1,2,3].map(parseInt));//  [1, NaN, NaN]

// parseInt 对于Boolean、Null、Undefined三种类型 直接返回 NaN
console.log(parseInt(true));// NaN
console.log(parseInt(false));// NaN
console.log(parseInt(null));// NaN
console.log(parseInt(undefined));// NaN
console.log(parseInt([1,2]));// 1 相当于 console.log(parseInt('1,2'));

// Number() parseInt() 的一些差异
// Number() 可以作用于任何数据类型，parseInt()主要用于将字符串转为 整数
console.log(Number('')); // 0
console.log(parseInt('')); // NaN
console.log(Number(null)); // 0
console.log(parseInt(null)); // NaN 
```

### 字符串相关

```javascript
let arr = [1,2,3];
arr.length = 2;
console.log(arr);// [ 1, 2 ]
// 数组的 length 属性是可写的，而字符串的length属性是不可写的
let str = '123';
str.length = 2;
console.log(str);// 123
// null 和 undefined 是没有 toString() 方法的， String() 这个转型函数是可以作用于任何数据类型的

```

### 对象相关

```javascript
// 一个空对象的原型上面都有 下面几个属性or方法：
// obj.constructor 指向该对象的构造函数
// obj.hasOwnProperty('name') 用来判断对象 obj 是否拥有自己的实例属性 name
// obj.isPrototypeOf(obj1) 判断对象 obj 是否在 对象 obj1 的原型连中
// obj.propertyIsEnumerable('name') 判断属性  name 是否是可遍历的（for in）
// obj.toLocalString() 返回字符串表示
// obj.toString() 返回字符串表示
// obj.valueOf() 返回了自己本身

let obj = {a: 1};
console.log(obj.valueOf());// { a: 1 }  返回了自己本身
console.log(obj.toString());// [object Object]  返回字符串表示
console.log(obj.toLocaleString());// [object Object] 返回字符串表示
```

### 自增自减操作符

```javascript
++a --a a++ a--  这四个一元操作符对任何值都适用的
//在操作非 Number 类型值的情况遵循以下规则：
// 1 被操作数为 包含 有效数字字符的字符串时，会将其转为数字，再加或减（被操作数类型会转为 Number）
let a = '123';
a++;
console.log(typeof a, a);// number 124
// 2 被操作数为 不包含 有效数字字符的字符串时，会将其转为NaN，再加或减结果还是NaN（被操作数类型会转为 Number）
let a = '123a';
a++;
console.log(typeof a, a);// number NaN
// 3 对于布尔类型 会将false转为0，true 转为 1 再运算（被操作数类型会转为 Number）
let a = false, b = true;
a++; b++;
console.log(typeof a, a);// number 1
console.log(typeof b, b);// number 2
// 3 下面这个很有趣，说明了 a++ 会先将 a 转为Number类型 0 赋给 b（这个时候 b就是数字类型 0），接着 a 再➕1 变成 1
let a = false;
let b = a++;
console.log(typeof a, a);// number 1
console.log(typeof b, b);// number 0
// 将 a++ 换成 ++a 后
let a = false;
let b = ++a;
console.log(typeof a, a);// number 1
console.log(typeof b, b);// number 1
// 4 作用于对象时,原理：{d:1} 先调用 valueOf() -> {d:1} 再调用 toString() -> [object Object]->应用上面的规则2 -> NaN
let a = {d:1};
let b = ++a;
console.log(typeof a, a);// number NaN
console.log(typeof b, b);// number NaN
// 4 原理：调用a的valueOf 得到 -5 给到 b，a 再加 1得到 -4
let a = {
    d:1,
    valueOf() {
        return -5
    }
};
let b = a++;
console.log(typeof a, a);// number -4
console.log(typeof b, b);// number -5
// 5 作用于数组，原理：[]->[]->''->0
let a = [];
let b = a++;
console.log(typeof a, a);// number 1
console.log(typeof b, b);// number 0
// 5 原理：[5]->[5]->'5'->5
let a = [5];
let b = a++;
console.log(typeof a, a);// number 6
console.log(typeof b, b);// number 5
// 5 原理：[5,3]->[5,3]->'5,3'->NaN
let a = [5,3];
let b = a++;
console.log(typeof a, a);// number NaN
console.log(typeof b, b);// number NaN
// 得出结论：++a --a a++ a-- 会转换变量类型（按照 Number()转型函数来转）
```

### 一元加减操作符

一元加减操作符 和 上面的 自增自减操作符 类似，但是有一点区别（**++a --a a++ a-- 会转变原数据类型**），一元加减操作符 不会改变原数据类型，只会返回一个 `Number` 类型

```javascript
let a = '123';
let b = +a;
console.log(typeof a, a);// string 123
console.log(typeof b, b);// number 123
//---分割线---//
let a = '123a';
let b = +a;
console.log(typeof a, a);// string 123a
console.log(typeof b, b);// number NaN
//---分割线---//
let a = false, b = true;
let c = +a;
let d = -b;
console.log(typeof a, a);// boolean false
console.log(typeof b, b);// boolean true
console.log(typeof c, c);// number 0
console.log(typeof d, d);// number -1
//---分割线---//
let a = {d:1};
let b = +a;
console.log(typeof a, a);// object {d:1}
console.log(typeof b, b);// number NaN
//---分割线---//
let a = {
    d:1,
    valueOf() {
        return -5
    }
};
let b = -a;
console.log(typeof a, a);// object
console.log(typeof b, b);// number 5
//---分割线---//
let a = [];
let b = +a;
console.log(typeof a, a);// object []
console.log(typeof b, b);// number 0
//---分割线---//
let a = [5];
let b = +a;
console.log(typeof a, a);// object [5]
console.log(typeof b, b);// number 5
//---分割线---//
let a = [5,3];
let b = +a;
console.log(typeof a, a);// object [5, 3]
console.log(typeof b, b);// number NaN
```

