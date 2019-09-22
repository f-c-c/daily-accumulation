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
// obj.toLocalString() 
// obj.toString()
// obj.valueOf()
```

