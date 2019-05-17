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
常见的拦截方法：
```javascript
    <script>
        let obj = {
            a: 1,
            b: 2
        }
        let proxy = new Proxy(obj, {
            // 拦截 proxy.xxx get操作
            get: (target , prop, receiver) => {
                if (prop === 'a') {
                    return target.b;
                } else if (prop === 'b') {
                    return target.a;
                }
                
            },
            // 拦截 proxy.xxx = xxx; set 操作
            set: (target, prop, value, receiver) => {
                if (prop === 'a') {
                    target.b = value;
                } else if (prop === 'b') {
                    target.a = value;
                }
            },
            //拦截 prop in obj
            has: (target, prop) => {
                if (prop === 'a') {
                    return true;
                } else if (prop === 'b') {
                    return false;
                }
            },
            //拦截delete操作
            deleteProperty: (target, prop) => {
                // 若为a则删除该属性
                if (prop === 'a') {
                    Reflect.deleteProperty(target, prop);
                    return true;
                } else if (prop === 'b') {
                    // 若为b则不删除
                    // Reflect.deleteProperty(target, prop);
                    return false;
                }
            }

        })
        console.log(proxy.a);// 2
        console.log(proxy.b);// 1
        proxy.a = 4;
        console.log('obj', obj);//{a: 1, b: 4}
        console.log('a' in proxy);// true
        console.log('b' in proxy);// false
        console.log(delete proxy.a);// true
        console.log(delete proxy.b);// false
        console.log('obj', obj);//{b: 4}
    </script>
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

### Reflect

为**操作对象**而提供的新API

* 将Object对象的属于语言内部的方法放到Reflect对象上，即从Reflect对象上拿Object对象内部方法

* 将用 老Object方法 报错的情况，改为返回false

* ```javascript
  try {
    Object.defineProperty(target, property, attributes);
    // success
  } catch (e) {
    // failure
  }
  
  if (Reflect.defineProperty(target, property, attributes)) {
    // success
  } else {
    // failure
  }
  ```

* 让Object操作变成函数行为

  * 老写法（命令式写法）`'name' in Object //true`
  * 新写法`Reflect.has(Object,'name') //true`
  
* Reflect与Proxy是相辅相成的，在Proxy上有的方法，在Reflect就一定有

### Reflect的API

* **Reflect.get(target,property,receiver)**
  查找并返回target对象的property属性

```javascript
            let obj = {
                name: 'fcc',
                age: 28
            };
            console.log(Reflect.get(obj, 'name')); // fcc
  
          let obj = {
              get yu() {
                  //this返回的是Reflect.get的receiver参数对象
                  return this.name + this.age
              }
          }
  
          let receiver = {
              name: "fcc",
              age: "hhh",
          }
  
          let result = Reflect.get(obj, "yu", receiver)
          console.log(result) //fcchhh
```

* **Reflect.set(target,propName,propValue,receiver)**

设置target对象的propName属性为propValue

```javascript
        let obj = {
            a: 1,
        }

        let result = Reflect.set(obj, "a", 2);
        console.log(result); // true
        console.log(obj);//{a: 2}
```



> **Reflect** 是一个内置的对象，它提供拦截 JavaScript 操作的方法。这些方法与 `proxy` 的方法相同。`Reflect`不是一个函数对象，因此它是不可构造的。
>
> 与大多数全局对象不同，`Reflect`没有构造函数。你不能将其与一个[new运算符]一起使用，或者将`Reflect`对象作为一个函数来调用。`Reflect`的所有属性和方法都是静态的（就像[`Math`]对象）。

> [`Reflect.apply()`](https://developer.mozilla.org/zh-CN/docs/Web/JavaScript/Reference/Global_Objects/Reflect/apply)
>
> 对一个函数进行调用操作，同时可以传入一个数组作为调用参数。和 [`Function.prototype.apply()`](https://developer.mozilla.org/zh-CN/docs/Web/JavaScript/Reference/Global_Objects/Function/apply) 功能类似。
>
> 静态方法 `Reflect.apply()` 通过指定的参数列表发起对目标(target)函数的调用
>
> ```javascript
> Reflect.apply(target, thisArgument, argumentsList)
> target 目标函数。thisArgument target函数调用时绑定的this对象。argumentsList target函数调用时传入的实参列表，该参数应该是一个类数组的对象。
> 该方法与ES5中Function.prototype.apply()方法类似：调用一个方法并且显式地指定this变量和参数列表(arguments) ，参数列表可以是数组，或类似数组的对象。
> Function.prototype.apply.call(Math.floor, undefined, [1.75]);
> 使用 Reflect.apply 方法会使代码更加简洁易懂
> ```

