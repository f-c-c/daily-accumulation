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
### Immutable 的几种数据类型
1. `List`: 有序索引集，类似JavaScript中的Array。
2. `Map`: 无序索引集，类似JavaScript中的Object。
3. `OrderedMap`: 有序的`Map`，根据数据的set()进行排序。
4. `Set`: 没有重复值的集合。
5. `OrderedSet`: 有序的`Set`，根据数据的add进行排序。
6. `Stack`: 有序集合，支持使用unshift（）和shift（）添加和删除。
7. `Range()`: 返回一个Seq.Indexed类型的集合，这个方法有三个参数，start表示开始值，默认值为0，end表示结束值，默认为无穷大，step代表每次增大的数值，默认为1.如果start = end,则返回空集合。
8. `Repeat()`: 返回一个vSeq.Indexe类型的集合，这个方法有两个参数，value代表需要重复的值，times代表要重复的次数，默认为无穷大。
9. `Record`: 一个用于生成Record实例的类。类似于JavaScript的Object，但是只接收特定字符串为key，具有默认值。
10. `Seq`: 序列，但是可能不能由具体的数据结构支持。
11. `Collection`: 是构建所有数据结构的基类，不可以直接构建。

用的最多就是**List和Map**，主要介绍这两种数据类型的API

### Immutable.js 的常用API

* **fromJS()**
> 将一个js数据转换为Immutable类型的数据，`fromJS(value, converter)` value是要转变的数据，converter是要做的操作。第二个参数可不填，默认情况会将数组准换为List类型，将对象转换为Map类型，其余不做操作

* **toJS()**
> 将一个Immutable数据转换为JS类型的数据 `value.toJS()`

```javascript
const Immutable = require('immutable')
let arr = Immutable.fromJS([1,2,3,4,5]).toJS()
console.log(arr) // [ 1, 2, 3, 4, 5 ]
```

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

* **merge()**
> 浅合并，新数据与旧数据对比，旧数据中不存在的属性直接添加，旧数据中已存在的属性用新数据中的覆盖

```javascript
 const Map1 = Immutable.fromJS({a:111,b:222,c:{d:333,e:444}});
 const Map2 = Immutable.fromJS({a:111,b:222,c:{e:444,f:555}});

 const Map3 = Map1.merge(Map2);
  //Map {a:111,b:222,c:{e:444,f:555}}
 const Map4 = Map1.mergeDeep(Map2);
  //Map {a:111,b:222,c:{d:333,e:444,f:555}}
```

* **set()**
> 设置第一层key、index的值
> 对于数据的修改，是对原数据进行操作后的值赋值给一个新的数据，并不会对原数据进行修改，因为Immutable是不可变的数据类型。List在使用的时候，将index为number值设置为value。Map在使用的时候，将key的值设置为value。
```javascript
const originalList = Immutable.List([ 0 ])
const resultList = originalList.set(1, 1)
console.log(originalList.toJS()); // [0]
console.log(resultList.toJS()); //[0, 1]

const originalMap = Immutable.Map({"a": 1})
const resultMap = originalMap.set("b", 2)
console.log(originalMap.toJS()); // {a: 1}
console.log(resultMap.toJS()); // {a: 1, b: 2}
```

* **setIn()**

> 设置深层结构中某属性的值，用法与set()一样，只是第一个参数是一个数组，代表要设置的属性所在的位置

* **delete()**
> 用来删除第一层结构中的属性

```javascript
const originalMap = Immutable.Map({"a": 1})
const resultMap = originalMap.set("b", 2).delete("a")
console.log(originalMap.toJS()); // {a: 1}
console.log(resultMap.toJS()); // {b: 2}
```

* **deleteIn()**

> 用来删除深层数据，用法参考setIn， deleteAll() (Map独有，List没有)

```javascript
const originalMap = Immutable.Map({"a": 1})
const resultMap = originalMap.set("b", 2).set("c", 3).deleteAll(["a", "b"])
console.log(originalMap.toJS()); // {a: 1}
console.log(resultMap.toJS()); // {c: 3}
```

* **update()**

> 对对象中的某个属性进行更新

```javascript
const list = Immutable.List([ 'a', 'b', 'c' ])
const result = list.update(2, val => val.toUpperCase())
console.log(list.toJS()); //  [ 'a', 'b', 'c' ]
console.log(result.toJS()); //  [ 'a', 'b', 'C' ]
```

