# immutable

JavaScript 中的对象一般是可变的（Mutable），Immutable Data 就是一旦创建，就不能再被更改的数据。对 Immutable 对象的任何修改或添加删除操作都会返回一个新的 Immutable 对象。Immutable 实现的原理是 **Persistent Data Structure（持久化数据结构）**，也就是使用旧数据创建新数据时，要保证**旧数据同时可用且不变**。为了避免 deepCopy 把所有节点都复制一遍带来的性能损耗，Immutable 使用了 Structural Sharing（结构共享），即如果对象树中一个节点发生变化，只修改这个节点和受它影响的父节点，其它节点则进行共享。

```javascript
// Immutable
const map = Map({ a: 1, b: 2 });
const list = List([1,2,3]);

// 原生js
const obj = { a: 1, b: 2 };
const arry = [1,2,3];

// 取值方式对比
console.log(map.get('a'));
console.log(list.get(0));
console.log(obj.a);
console.log(arry[0]);
```

### Immutable.js 的常用API

* **fromJS()**
> 将一个js数据转换为Immutable类型的数据，`fromJS(value, converter)` value是要转变的数据，converter是要做的操作。第二个参数可不填，默认情况会将数组准换为List类型，将对象转换为Map类型，其余不做操作

* **toJS()**
> 将一个Immutable数据转换为JS类型的数据 `value.toJS()`

* **List() 和 Map()**
> 用来创建一个新的List/Map对象
 ```javascript
//List
Immutable.List(); // 空List
Immutable.List([1, 2]);

//Map
Immutable.Map(); // 空Map
Immutable.Map({ a: '1', b: '2' });
 ```

* **is()**
> 对两个对象进行比较，`is(map1,map2)`和js中对象的比较不同，在js中比较两个对象比较的是地址，但是在Immutable中比较的是这个对象hashCode和valueOf，只要两个对象的hashCode相等，值就是相同的，避免了深度遍历，提高了性能
```javascript
import { Map, is } from 'immutable'
const map1 = Map({ a: 1, b: 1, c: 1 })
const map2 = Map({ a: 1, b: 1, c: 1 })
map1 === map2   //false
Object.is(map1, map2) // false
is(map1, map2) // true
```

* **List.isList() 和 Map.isMap()**
> 判断一个数据结构是不是List/Map类型
```javascript
List.isList([]); // false
List.isList(List()); // true

Map.isMap({}) // false
Map.isMap(Map()) // true
```
* **size**

> 作用：属性，获取List/Map的长度，等同于ImmutableData.count();

* **get() 、 getIn()**

> 获取数据结构中的数据
```javascript
//获取List索引的元素
ImmutableData.get(0);

// 获取Map对应key的value
ImmutableData.get('a');

// 获取嵌套数组中的数据
ImmutableData.getIn([1, 2]);

// 获取嵌套map的数据
ImmutableData.getIn(['a', 'b']);
```

* **has() 、 hasIn()**
> 判断是否存在某一个key

```javascript
Immutable.fromJS([1,2,3,{a:4,b:5}]).has('0'); //true
Immutable.fromJS([1,2,3,{a:4,b:5}]).hasIn([3,'b']) //true
```

**includes()**
> 判断是否存在某一个value

```javascript
Immutable.fromJS([1,2,3,{a:4,b:5}]).includes(2); //true
Immutable.fromJS([1,2,3,{a:4,b:5}]).includes('2'); //false 不包含字符2
Immutable.fromJS([1,2,3,{a:4,b:5}]).includes(5); //false 
Immutable.fromJS([1,2,3,{a:4,b:5}]).includes({a:4,b:5}) //false
Immutable.fromJS([1,2,3,{a:4,b:5}]).includes(Immutable.fromJS({a:4,b:5})) //true
```

