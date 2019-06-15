# obj.a = 的几种情况

#### 一 对象的上一层 `__proto__` 🈶️同名属性 `a` 且`writable: true`

这种情况下，会在对象 `obj` 上创建一个屏蔽属性 `a` ，值为 2

```javascript
let otherObj = {
};
Reflect.defineProperty(otherObj, 'a', {
    writable: true,
    enumerable: true,
    value: 1,
    configurable: true
});
let obj = Object.create(otherObj);
obj.a = 2;
console.log(obj);// {a: 2}
```
#### 二 对象的上一层 `__proto__` 🈶️同名属性 `a` 且`writable: false`

如果运行在严格模式下，代码会抛出一个错误。否则，这条赋值语句会被忽略，总之不会发生屏蔽

这种情况只存在 赋值操作中，在 `Reflect.defineProperty()`是不受影响的

```javascript
let otherObj = {
};
Reflect.defineProperty(otherObj, 'a', {
    writable: false,
    enumerable: true,
    value: 1,
    configurable: true
});
let obj = Object.create(otherObj);
obj.a = 2;
console.log(obj);// {}
```

#### 三 对象的上一层 `__proto__` 🈶️ setter

那就一定会调用这个 `setter` ，属性不会被添加到 `obj` 上，也不会给 `obj` 添加 `setter`

```javascript
let otherObj = {
    set b(value) {
        this.c = value
    }
};
let obj = Object.create(otherObj);
obj.c = 5;
obj.b = 3;
console.log(obj); {c: 3}
```

#### 一个奇怪的东西

```javascript
let otherObj = {
   a: 1
};
let obj = Object.create(otherObj);
console.log('otherObj.a', otherObj.a); // 1
console.log('obj.a', obj.a);// 1
otherObj.a++
obj.a++;
console.log('otherObj.a', otherObj.a);// 2
console.log('obj.a', obj.a);// 3
```

