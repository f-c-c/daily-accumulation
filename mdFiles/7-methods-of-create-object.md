# 7 methods of create object

本文介绍了对象字面量、工厂模式、构造函数模式、原型模式、组合构造函数模式和原型模式、寄生构造函数模式、稳妥构造函数模式共 7 种创建对象的方式，并且介绍了每一种的优缺点和适用的场景

### 一，对象字面量形式

```javascript
let person = {'name':'zhangsan','age':34};
```

### 二，工厂模式

- 工厂模式解决了创建多个相似对象的问题，但是没有解决**对象识别的问题**，在实例的原型链上只能识别到Object ，由此引出构造函数模式

```javascript
let createPerson = (name,age)=>{
    let o = new Object();
    o.name = name;
    o.age = age;
    o.sayName = function (){
        console.log(this.name);
    };
    return o;
};
let p1 = createPerson('zhangsan',34);
let p2 = createPerson('lisi',45);

console.log(p1.name,p1.age);
p1.sayName();

console.log(p2.name,p2.age);
p2.sayName();

console.log(p1 instanceof Object);//true

```

