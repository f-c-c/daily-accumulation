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

### 三，构造函数模式

- 构造函数模式解决了对象的识别问题，但是也存在问题：每一个方法都要在每一个实例上重新创建一遍，不同实例上的同名函数（不同的对象）是不相等的，创建多个完成同样任务的函数是没有必要的，由此引出原型模式

```javascript
//构造函数
function Person(name,age) {
    this.name = name;
    this.age = age;
    this.sayName = function () {
        console.log(this.name);
    }
}
let p1 = new Person('zhangsan',23);
let p2 = new Person('lisi',45);

console.log(p1.name,p1.age);
p1.sayName();

console.log(p2.name,p2.age);
p2.sayName();

//能识别到  Person  和  Object
console.log(p1 instanceof Person);//true
console.log(p1 instanceof Object);//true
console.log(p2 instanceof Person);//true
console.log(p2 instanceof Object);//true

```

### 四，原型模式

- 原型上的属性和方法是每一个实例所共有的
- 不能通过对象实例去重写原型中的值（但是原型中的数组可以在对象实例中执行push等操作，会影响到每一个对象实例）
- `Person === p1.constructor === Person.prototype.constructor`
- `Person.prototype === p1.__proto__ === Object.getPrototypeOf(p1)`
- 原型对象上的属性可以这样去修改：`Object.getPrototypeOf(p1).name = 'xxx'`或者这样：`Person.prototype.name = 'xxx'`
- 如果对象实例中存在一个属性和原型中的一个属性同名，这个属性会屏蔽原型中的那个属性
- `hasOwnProperty()` 可以检测一个属性是在对象实例上还是在其原型上
- `alert("name" in p1)` in 操作符可以检测一个属性在对象实例上 **或** 原型链⛓️上
- 确定一个属性为原型链属性，并且不在实例上：`!object.hasOwnProperty('name') && ('name' in object)`
- 原型对象的问题：原型的最大问题是由其共享的本质决定的,这种共享对于函数和那些包含基本类型值的属性来说是没问题的,但是对于引用类型值入数组来说就存在问题了。由此引出了 组合使用构造函数和原型模式

```javascript
function Person() {

}
Person.prototype.name = '公共的名字';
Person.prototype.age = '1212';
Person.prototype.sayName = function () {
    return this.name;
}
let p1 = new Person();
let p2 = new Person();

console.log(p1.name,p2.age);
console.log(p1.sayName());
```

### 五，组合使用构造函数模式和原型模式

- 构造函数中定义私有的属性和方法，在原型上添加共享的属性和方法
- 从代码可以看出私有的属性arr在每一个对象实例中都是独立存在的，一个的更改不会对其余的造成影响

```javascript
function Person(name,age){
    this.name = name;
    this.age = age;
    this.arr = [1,2,3];
    this.sayName = function(){
        return this.name;
    };
}
Person.prototype = {
    'constructor':Person,
    'color':'white',
    'sayAge':function(){
        return this.age;
    }
}
let p1 = new Person('zhangsan','24');
let p2 = new Person('lisi','34');
p1.arr.push(4);
console.log(p1.arr);
console.log(p2.arr);
```

### 六，寄生构造函数模式

通常前面几种创建对象的方法y已经能够使用大部分的场景，寄生构造函数模式和工厂模式很相像

- 寄生构造函数模式使用 new 操作符去调用，并把函数叫做构造函数，而工厂模式是直接使用函数名调用
- 使用该模式要注意：返回的对象实例与构造函数以及构造函数的原型对象都是没有关系的,不能使用 instanceOf 操作符来确定实例类型
- 建议在可以使用其他模式的时候，尽量不要使用该模式

```javascript
function createPerson(name,age){
    let o = new Object();
    o.name = name;
    o.age = age;
    o.sayName = function(){
        return this.name;
    }
    return o;
}
let p1 = new createPerson('zhangsan','23');
console.log(p1.sayName());

```

### 七，稳妥构造函数模式

- 可以看出稳妥构造函数模式和工厂模式也是很相像的
- 稳妥构造函数的私有成员是没有挂载在返回的对象实例上的，并且也不用 new 去调用
- 稳妥构造函数模式适用于某些安全的执行环境（这些环境可能会禁用this 和 new）
- 同样的注意和寄生构造函数模式一样的是：对象实例和构造函数之间也是没有关联的，不能使用 instanceOf 去检查对象类型

```javascript
function Person(name, age, job) {
    var o = new Object();
    // private members
    var nameUC = name.toUpperCase();
    // public members
    o.sayName = function() {
        return name;
    };
    o.sayNameUC = function() {
        return nameUC;
    };
    return o;
}
let p1 = Person('zhangsan',23,'coder');
console.log(p1.sayName(),p1.sayNameUC());

```

