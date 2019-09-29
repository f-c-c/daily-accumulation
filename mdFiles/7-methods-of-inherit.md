# 7 methods of inherit

### 一，原型链

- 原型链的概念在上一篇文章中已经介绍的比较清楚了，这里再次说一下：
- 每一个构造函数都有一个原型对象 `prototype` ，而在原型对象上又会有一个属性 `constructor` 指回构造函数，在对象实例上又有一个内部属性 `__proto__` 指向了原型对象
- 假如我们让原型对象等于另一个类型的实例会怎么样？ 显然的，此时的原型对象包含一个指向另一个原型的指针（相应的另一个原型对象也包含一个指向另一个构造函数的指针 `constructor` ）。此时如果另一个原型对象又是另一个类型的实例那么上述关系依然存在，如此层层递进就构成了一个原型链条。这就是利用原型链实现继承的基本思想。文字说了这么多不好理解,请看下图：

![](../assert/js-inherit-prototype.jpg)

```javascript
//父类  人类
function Person(){
    this.name = '我是人类';
}
Person.prototype.sayName = function(){
    return this.name;
};
Person.prototype.walk = function(){
    return `${this.name} 在走路`;
};
//子类  男人
function Man(another_name){
    this.another_name = another_name;
}

Man.prototype = new Person();//这行代码即实现了继承

Man.prototype.sayAnotherName = function () {
    return this.another_name;
};
let m1 = new Man('小明');
console.log(m1.another_name);//m1 自己的实例属性
console.log(m1.sayAnotherName());//m1 的原型对象上的方法
console.log(m1.name);//m1 的原型对象上的属性
console.log(m1.walk());//m1 的原型对象 的 原型对象上的方法

```

- 通过原型链实现的继承：原来存在于父类（Person）的实例中的所有属性和方法现在也存在于子类（Man.prototype）中
- 结果就是m1内部的指针 `__proto__` 指向它自己的原型对象 `Man.prototype`。而这个原型对象又有一个内部属性 `__proto__`指向 `Person.prototype`。总结一句话就是：**子类的实例指向子类的原型对象，子类的原型对象指向父类的原型对象**
- 使用 instanceof 操作符去检测实例与原型链中出现过的构造函数，结果都会返回 true
- 给子类原型添加方法的代码一定要放在替换原型的语句之后
- 原型链实现继承存在的问题：1.父类的实例属性顺理成章的变成了子类的原型属性，我们知道子类的原型只有一个，其原型属性也是共享的，问题就出在这个共享上面。一旦一个实例修改了其原型上的引用类型的属性，将会影响到其他的实例。2.不能往父类的构造函数中传递参数。基于原型链实现继承存在的以上问题我们引出借用构造函数实现继承

### 二，借用构造函数

- 基本思想：在子类的构造函数的内部调用父类的构造函数，函数只是在特定环境中执行代码的对象（一切皆对象）
- 通过构造函数定义的属性全都是每一个实例所独有的属性

```javascript
//借用构造函数实现继承
function Person(name){
    this.name = name;
}
function Man(name){
    Person.call(this,name);//利用call和apply借调父类的构造函数
    this.another_name = '我是男人';
}
let m1 = new Man('小明');
console.log(m1.name);
console.log(m1.another_name);
```

- 借用构造函数存在的问题：1.方法都在构造函数中定义，复用无从谈起。2.父类的原型对象中的属性子类也是不可见的。基于以上原因又引出了组合使用原型链和构造函数来实现继承

### 三，组合原型链和借用构造函数

- 通过组合原型链和借用构造函数实现继承：在构造函数中定义每一个实例独立的属性，在原型上定义公共的属性

```javascript
//父类
function Person(name){
    this.name = name;
    this.colors = ['#000','#fff'];
}
Person.prototype.sayColor = function () {
    return this.colors;
};
function Man (name,age){
    Person.call(this,name);//借用构造函数
    this.age = age;
}
Man.prototype = new Person();//原型链式继承
Man.prototype.constructor = Man;

let m1 = new Man('小明',24);
let m2 = new Man('小红',20);

console.log(m1.name);//实例属性 小明
console.log(m1.colors);//实例属性 ['#000','#fff']
console.log(m1.__proto__.name);//原型属性  nudefined
console.log(m1.__proto__.colors);//原型属性 ['#000','#fff']

```

- 在这里注意：m1和m2均拥有自己独立的实例属性name 和 colors 和 age
- 并且m1和m2共享了其原型对象上的两个属性 m1.`__proto__`.name 和 m1.`__proto__`.colors 值分别为undefined 和 [‘#000’,’#fff’]。这里在实例m1，m2上存在两个和其原型上同名的属性name和colors。当这样：m1.name m1.colors这样子实际上访问的是实例上的属性，因为实例上的属性会屏蔽其原型上的同名属性，这就涉及到属性的检索方式的问题了（沿着原型链往上检索，一旦找到该属性，就停止检索）。

### 四，原型式继承

- 原型式继承的关键：必须有一个作为基础的对象，返回的对象以那个基础对象为原型，实际上是对传入其中的对象执行了一次浅复制
- ES5 中规范了原型式继承，提供了一个函数：`Object.create()`

```javascript
//原型式继承
function createObject(o){
    function fn(){}
    fn.prototype = o;
    return new fn();
}
let Person = {
    "name":"我是人类",
    "sayName":function(){return this.name+" 我统治着世界";}
};
let man = createObject(Person);
console.log(man.name);//我是人类
console.log(man.sayName());//我是人类 我统治着世界
```

### 五，寄生式继承

- 寄生式继承是和原型式继承紧密相关的一种继承，同样的需要一个基础对象，寄生式继承会在内部以某种方式去增强对象

```javascript
//寄生式继承
function createObject(o){
    function fn(){}
    fn.prototype = o;
    return new fn();
}

function parasitic (o){
    let obj = createObject(o); 
    //内部增强对象
    obj.sayHello = function(){
        return 'hello';
    };
    return obj;
}

let person = {"name":"我是人类"};//基础对象
let newO = parasitic(person);
console.log(newO.hasOwnProperty('sayHello'));//true
console.log(newO.hasOwnProperty('name'));//false
```

### 六，寄生组合式继承

```javascript
//寄生组合式继承
//寄生式继承  代码
function createObject(o){
    function fn(){}
    fn.prototype = o;
    return new fn();
}
//传入父类和子类的构造函数
function parasiticCombination(Sup,Sub){
    let o = createObject(Sup.prototype);
    o.constructor = Sub;
    Sub.prototype = o;
}

//父类
function Person(name){
    this.name = name;
    this.colors = ['#000','#fff'];
}
Person.prototype.sayName = function(){
    return this.name;
}
//子类
function Man(name,age){
    Person.call(this,name);
    this.age = age;
}

parasiticCombination(Person,Man);
let m1 = new Man('小东',23);
console.log(m1.sayName());//小东
```

- 看以上代码：关键点在于一个中间桥梁对象o，这个o对象是以`Person.prototype` 为原型的。并且o上面没有实例属性，同时，Man是以o对象为原型对象的。串起来就是子类Man的原型对象是o，而o的原型对象是`Person.prototype`。再次强调：o就是一个桥梁作用
- 寄生组合式继承就解决了 在 组合式继承里面父类构造函数调用两次的弊端：一次是构造子类时借调父类构造函数，另一次是实例化父类赋值给子类的原型时，这样就导致了同名属性的出现

### 七，class extend 实现继承

