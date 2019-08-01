# 栈、堆、赋值、浅拷贝、深拷贝？

## **栈和堆的区别**

 * 堆栈空间分配区别
    * 栈（操作系统）：由操作系统自动分配释放 ，存放基本数据类型值
    * 堆（操作系统）： 一般由程序员分配释放
* 堆栈缓存方式区别
    * 栈使用的是一级缓存， 他们通常都是被调用时处于存储空间中，调用完毕立即释放
    * 堆是存放在二级缓存中，生命周期由虚拟机的垃圾回收算法来决定。调用这些对象的速度要相对来得低一些。


## 六种基本数据类型+引用类型（也叫复杂类型）

* 基本数据类型：`（Undefined、Null、Number、String、Bollean、Symbol）` 基本数据类型存 **放在栈中**，基本数据类型值不可变，基本类型的比较是值的比较

* 引用类型：`（Object）如 Array、Function、RegExp、Date、Math` 引用类型存 **放在堆中**，变量实际上是一个存放在**栈内存的指针**，这个指针指向**堆内存中的地址**。每个空间大小不一样，要根据情况进行特定的分配，引用类型的比较是引用的比较，故`{} === {}` 将返回 `false`

## 赋值操作（基本类型和引用类型的区别）

* 基本数据类型的赋值（=）是在内存中新开辟一段栈内存，然后再将值赋值到新的栈中，基本类型的赋值的两个变量是两个独立相互不影响的变量

* 引用类型的赋值是传址。只是改变指针的指向，也就是说引用类型的赋值是**对象保存在栈中的地址的赋值**，这样的话两个变量就指向同一个对象，因此两者之间操作互相有影响

## 赋值（=）和浅、深拷贝的区别

```javascript
let obj1 = {
  name: "zhangsan",
  hobby: ["游泳","乒乓球","羽毛球"]
};
let obj2 = obj1;//赋值得到的obj2
//浅拷贝函数
function shallowCopy (sourceObj) {
  let result = {};
  for (let v in sourceObj) {
    if (sourceObj.hasOwnProperty(v)) {
      result[v] = sourceObj[v];
    } 
  }
  return result;
}
let obj3 = shallowCopy(obj1);//浅拷贝得到的obj3
```

* 对于赋值得到的 `obj2`，其实和 `obj1 `是一样的（保存的都是栈里面的同一个地址，这个地址指向堆里面的同一个对象），因此改变 `obj2` 的 `name `属性和 `hobby `属性，相应的 `obj1 `也会改变
* 浅拷贝得到的 `obj3`，改变 `obj3 `的 `name` 属性时，`obj1` 的 `name` 属性不会改变，而改变 `obj3` 的 `hobby` 属性时， `obj1` 的 `hobby` 会改变，这是因为浅拷贝只复制一层对象的属性，并不包括对象里面的为引用类型的数据。所以就会出现改变浅拷贝得到的 `obj3` 中的引用类型时，会使原始数据得到改变
* 其实 `shallowCopy函数` 、`Object.assign()` 、`扩展运算符（...）` 就是这样的浅拷贝，`[].slice()`方法可以视为数组对象的浅拷贝

|        | 和原数据是否指向同一个对象 | 第一层数据为基本数据类型     | 原数据中包含子对象           |
| ------ | -------------------------- | ---------------------------- | ---------------------------- |
| 赋值   | 是                         | 改变**会**使原数据发生改变   | 改变**会**使原数据发生改变   |
| 浅拷贝 | 否                         | 改变**不会**使原数据发生改变 | 改变**会**使原数据发生改变   |
| 深拷贝 | 否                         | 改变**不会**使原数据发生改变 | 改变**不会**使原数据发生改变 |

##  深拷贝

* 首先，浅拷贝和深拷贝都只针对于像Object， Array这样的复杂对象
* 区别：浅拷贝只复制对象的第一层属性、深拷贝可以对对象的属性进行递归复制，深拷贝是对对象以及对象的所有子对象进行拷贝
* JS没有内置深拷贝方法

### 方案一：JSON序列化 JSON.parse(JSON.stringify())

```javascript
let obj1 = {
  name: "zhangsan",
  hobby: ["游泳","乒乓球","羽毛球"],
  fn: function () {
    console.log("haha");
  }    
};
let obj5 = JSON.parse(JSON.stringify(obj1));
obj5.hobby.push("game");
console.log(obj5);//{ name: 'zhangsan', hobby: [ '游泳', '乒乓球', '羽毛球', 'game' ] }
console.log(obj1);//{ name: 'zhangsan', hobby: [ '游泳', '乒乓球', '羽毛球' ], fn: [Function: fn] }
```

该方案特点：

* 对象/数组中的普通对象和数组都能拷贝
* 然而`date`对象成了字符串
* 函数直接就不见了
* 正则成了一个空对象 `{}`

### 方案二：`for in` + 递归

```javascript
//待深拷贝的对象
let obj1 = {
  name: "zhangsan",
  hobby: ["游泳","乒乓球","羽毛球"],
  fn: function () {
    console.log("df");
  }    
};
//针对对象和数组进行深拷贝
function deepCopy (sourceObj) {
  if ((typeof sourceObj) !== "object") {
    return;
  } else {
    let result = Object.prototype.toString.call(sourceObj) === "[object Array]" ? [] : {};
    for (index in sourceObj) {
      result[index] = (typeof sourceObj[index]) === "object" ? deepCopy(sourceObj[index]) : sourceObj[index];
    }
    return result;
  }
}
let obj2 = deepCopy(obj1);//递归实现的深拷贝
obj2.hobby.push("打游戏");
console.log(obj2);//{ name: 'zhangsan', hobby: [ '游泳', '乒乓球', '羽毛球', '打游戏' ] }
console.log(obj1);//{ name: 'zhangsan', hobby: [ '游泳', '乒乓球', '羽毛球' ] }
console.log(obj2.fn === obj1.fn);//true
```

该方案特点：

* 对象/数组中的普通对象和数组都能拷贝
* `date` 和正则都变成了 空对象 `{}`
*  对于函数其实执行的是**浅拷贝**

### 深拷贝之 环 的问题
思考以下代码的深拷贝问题：

```javascript
let obj1 = {
    name: "zhangsan"
};
obj1.obj = obj1;
```

使用 `for in`递归 和 `JSON.parse(JSON.stringify())` 分别会报以下的❌

```
RangeError: Maximum call stack size exceeded
TypeError: Converting circular structure to JSON
```

为了解决这个循环引用的深拷贝，可以用到 `WeakMap`：

```javascript
function deepCopyWeakMap(obj, hash = new WeakMap()) {
    if(hash.has(obj)) return hash.get(obj)
    let cloneObj = Array.isArray(obj) ? [] : {}
    hash.set(obj, cloneObj)
    for (let key in obj) {
        cloneObj[key] = (typeof obj[key]) === 'object' ? deepCopyWeakMap(obj[key], hash) : obj[key];
    }
    return cloneObj
}
```

### 针对特殊对象如：`new Date()` `new RegExp()`的拷贝

上述的 方案一和方案二都不能很好的处理 特殊对象的拷贝，为此可以用到 **结构化拷贝**

#### 利用 history API 实现结构化克隆

```javascript
const structuralClone = obj => {
    const oldState = history.state;
    history.replaceState(obj, document.title);
    const copy = history.state;
    history.replaceState(oldState, document.title);
    /* 可以在这里进行原型、DOM、Function 等不支持的类型的完善 */
    /* 不过基本就够用了 */
    /* copy = morePerfectCopy(copy) */
    return copy;
}
```

上述结构化的深拷贝 只在浏览器中起作用。因为在 `node`环境是没有 `history`的

`history.replaceState` `history.pushState()`再看看 还有 `history.state`

先看看这两个 `api`

* 添加和修改历史记录中的条目  [history.pushState()](https://developer.mozilla.org/en-US/docs/Web/API/History/pushState) 和 [history.replaceState()](https://developer.mozilla.org/en-US/docs/Web/API/History_API#The_replaceState()_method) 

目前支持的类型有：

- 除 symbols 之外的所有原始类型
- Boolean 对象
- String 对象
- Date
- RegExp （lastIndex 字段不会被保留。）
- Blob
- File
- FileList
- ArrayBuffer
- ArrayBufferView
- ImageData
- Array
- Object
- Map
- Set

不支持：

- 原型链
- Error 对象
- Function
- DOM
#### Notification API

```javascript
const deepCloneByNotificationAPI = obj => {
    return new Notification('', {data: obj, silent: true}).data;
}
```
### 一些库的实现
#### lodash 的cloneDeep

lodash 的 cloneDeep 实现可以深拷贝 `function`、`Date`、`RegExp`、以及对象的原型链

```javascript
//只引入 lodash 的一个函数，以便在打包时减小包的大小
//import _ from 'lodash' 这样是加载完整的库，我测试过 生产环境下的包，单个引入和全量引入差距 52kb左右
import cloneDeep from 'lodash/cloneDeep'
let obj1 = {
    name: "zhangsan",
    color: ['red', 'green', 'blue'],
    obj: {a: 1, b: 2},
    fun: function(x) {
    return x+1;
    },
    date: new Date(),
    reg: /a[bc]d/g
};
let a = cloneDeep(obj1);
console.log(a)
console.log(obj1)
```

