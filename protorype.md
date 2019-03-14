# 理解原型链

先上一张神图：

我们在理解下图时⚠️主要以下几点：

* **万物皆对象**
* 构造函数当成函数来看的时候其内部有一个`prototype`属性指向**实例的原型对象**
* 构造函数被当成对象看待时，内部有一个`__proto__`属性指向它的原型对象
* 原型对象是谁的：是实例（对象）的，不是构造函数的，找一个对象的的原型对象跟着它的`__proto__`走

![prototype](./assert/prototype.png "prototype")

构造函数的实例（比喻为孙子）的原型链：
```javascript
function Foo() {}
let f1 = new Foo();
//下面几个是成立的，查找原型链跟着__proto__走
f1.__proto__ === Foo.prototype
Foo.prototype.__proto__ === Object.prototype;
Object.prototype.__proto__ === null
```

构造函数（类比为爹）的原型链：（为什么构造函数也有原型对象---因为构造函数也是对象呀，一切皆对象）：

```javascript
function Foo() {}
//下面几个是成立的
Foo.__proto__ === Function.prototype
Function.prototype.__proto__ === Object.prototype
Object.prototype.__proto__ === null
```

Function（类比为爷爷）的原型链：

```javascript
Function.__proto__ === Function.prototype
```

* 引擎自己创建了 `Object.prototype` 和 `Function.prototype`  以及 `Object` 和 `Function`我们 打印`Function.prototype` 时输出的是：`ƒ () { [native code] }` 打印`Object.prototype`时上面挂了一些方法： `toString toLocaleString hasOwnProperty isPrototypeOf ` 这就充分说明了是引擎自己创建了的，并用`__proto__`将二者联系了起来
* 如何理解 `Function.__proto__ === Function.prototype`： 难道是 自己创建了自己？个人理解：引擎创建了`Object.prototype`、`Function.prototype`、`Function`、`Object`