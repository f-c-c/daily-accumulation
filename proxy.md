# 拦截

## 对象的 `get set`
* 对象里面的方法，一旦在前面加上 `get` ，就可以当作属性被调用，并拦截属性的读取操作，同样在前面加上 `set` ，也可以当作属性对待，并拦截属性的 赋值操作

```javascript
let a = {
    b: 1,
    c: 2,
    get getB() {
        return this.c;
    },
    set setB(val) {
        this.c = val;
    }
};
console.log(a.getB); //拦截 属性的读取
a.setB = 5;          //拦截属性的设置
console.log(a.getB);

```
## proxy

* ```javascript
  let proxy = new Proxy(target, handler);
  ```

`target`: 表示所要拦截的目标对象（对象、数组、函数等都可以）

`handler`：也是一个对象，用来定制拦截行为，如果`handler`是个空对象，将没有任何拦截效果，访问`proxy`相当于访问`target`


* `Proxy` 用于修改某些操作的默认行为，等同于在语言层面做出修改，所以属于一种“元编程”（`meta programming`），即对编程语言进行编程。
* `Proxy` 可以理解成，在目标对象之前架设一层“拦截”，外界对该对象的访问，都必须先通过这层拦截，因此提供了一种机制，可以对外界的访问进行过滤和改写。`Proxy` 这个词的原意是代理，用在这里表示由它来“代理”某些操作，可以译为“代理器”。
* `get(target, prop, receiver)` 拦截对象属性的读取操作

  * `target`： 目标对象
  * `prop`：属性名
  * `receriver`:` proxy`实例本身(可选参数)
* `set(target, prop, value, receiver)` 拦截属性的赋值操作

  * `target`： 目标对象
  * `prop`：属性名
  * `value`: 属性值
  * `receriver`: `proxy`实例本身（可选参数）
```javascript
//代码对一个空对象架设了一层拦截，重定义了属性的读取（get）和设置（set）行为
var obj = new Proxy({}, {
  //target 目标对象 key 属性 receiver proxy实例本身（可选）
  get: function (target, key, receiver) {
    console.log(`getting ${key}!`);
    return Reflect.get(target, key, receiver);
  },
  set: function (target, key, value, receiver) {
    console.log(`setting ${key}!`);
    return Reflect.set(target, key, value, receiver);
  }
});
//Proxy 实际上重载（overload）了点运算符，即用自己的定义覆盖了语言的原始定义
```

`ES6` 原生提供 `Proxy` 构造函数，用来生成`Proxy` 实例。

```javascript
var proxy = new Proxy(target, handler);
```

`Proxy` 对象的所有用法，都是上面这种形式，不同的只是`handler`参数的写法。其中，`new Proxy()`表示生成一个`Proxy`实例，`target`参数表示所要拦截的目标对象，`handler`参数也是一个对象，用来定制拦截行为。

如果`handler`没有设置任何拦截，那就等同于直接通向原对象

```javascript
var target = {};
var handler = {};
var proxy = new Proxy(target, handler);
proxy.a = 'b';
target.a // "b"
```

`Proxy` 实例也可以作为其他对象的原型对象

```javascript
var proxy = new Proxy({}, {
  get: function(target, property) {
    return 35;
  }
});

let obj = Object.create(proxy);
obj.time // 35
//上面代码中，proxy对象是obj对象的原型，obj对象本身并没有time属性，所以根据原型链，会在proxy对象上读取该属性，导致被拦截
```

Proxy 支持的拦截操作一览，一共 13 种：

>- **get(target, propKey, receiver)**：拦截对象属性的读取，比如`proxy.foo`和`proxy['foo']`。
>- **set(target, propKey, value, receiver)**：拦截对象属性的设置，比如`proxy.foo = v`或`proxy['foo'] = v`，返回一个布尔值。
>- **has(target, propKey)**：拦截`propKey in proxy`的操作，返回一个布尔值。
>- **deleteProperty(target, propKey)**：拦截`delete proxy[propKey]`的操作，返回一个布尔值。
>- **ownKeys(target)**：拦截`Object.getOwnPropertyNames(proxy)`、`Object.getOwnPropertySymbols(proxy)`、`Object.keys(proxy)`、`for...in`循环，返回一个数组。该方法返回目标对象所有自身的属性的属性名，而`Object.keys()`的返回结果仅包括目标对象自身的可遍历属性。
>- **getOwnPropertyDescriptor(target, propKey)**：拦截`Object.getOwnPropertyDescriptor(proxy, propKey)`，返回属性的描述对象。
>- **defineProperty(target, propKey, propDesc)**：拦截`Object.defineProperty(proxy, propKey, propDesc）`、`Object.defineProperties(proxy, propDescs)`，返回一个布尔值。
>- **preventExtensions(target)**：拦截`Object.preventExtensions(proxy)`，返回一个布尔值。
>- **getPrototypeOf(target)**：拦截`Object.getPrototypeOf(proxy)`，返回一个对象。
>- **isExtensible(target)**：拦截`Object.isExtensible(proxy)`，返回一个布尔值。
>- **setPrototypeOf(target, proto)**：拦截`Object.setPrototypeOf(proxy, proto)`，返回一个布尔值。如果目标对象是函数，那么还有两种额外操作可以拦截。
>- **apply(target, object, args)**：拦截 Proxy 实例作为函数调用的操作，比如`proxy(...args)`、`proxy.call(object, ...args)`、`proxy.apply(...)`。
>- **construct(target, args)**：拦截 Proxy 实例作为构造函数调用的操作，比如`new proxy(...args)`。

