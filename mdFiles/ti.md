-  分析`["1", "3", "10"].map(parseInt)`

   输出 `1 NaN 2` 相当于`parseInt("1", 2)` `parseInt("3", 2)` `parseInt("10", 2)`，parseInt 的第二个参数默认为 2

- 下面👇的输出

```js
var n = 10;
function fn() {
  console.log(this.n);
}
var obj = {
  n: 2,
  show: function(fn) {
    this.n = 3;
    fn();
    arguments[0]();
  }
};
obj.show(fn);
```
​	输出`10 undefined`
- 下面的 inner 的实际高度：

```html
    <style>
        .outer {
            width: 200px;
            height: 100px;
        }
        .inner {
            width: 60px;
            height: 60px;
            padding-top: 20%;
        }
    </style>
    <body>
    		<div class="outer"><div class="inner"></div></div>
		</body>
```

​	实际是 `60 + 200px * 20% = 100px`，实际就是这个 `padding-top: 20%;` 是按照父元素的 `width`计算的

- delete 数组的 item，数组的 length 是否会 -1

  不会，会是空，如果取值的话会得到`undefined`

- 使用 `node app.js` 开启服务，如何让它在后台运行

  `node app.js &`

- 尽可能写出更多的数组副作用方法

  `splice、push、pop、shift、unshift、sort、fill、reverse`

- 给定一个符合`JavaScript`对象取值的字符串，得到路径数组（可以考虑正则、AST、Proxy）`const source  = "a[0].b['cd'].e"; getPath(source);  // ['a', '0', 'b', 'cd', 'e']`

  ```javascript
  // 利用正则
  let source = 'a[0].b["cd"].e';
  function fn(str) {
      // 去掉 ] 和 ' 和 " 再将 [ 换为 . 再按照.分割数组
      return str.replace(/([\]'"])/g, '').replace(/\[/g, '.').split(".");
  }
  console.log(fn(source));// ["a", "0", "b", "cd", "e"]
  ```

- 反转 dom 子元素

  ```javascript
  输入
  
  <div id="container">
    <div>1</div>
    <div>2<div>xxx</div><div>4</div></div>
    <div>3</div>
  </div>
  
  
  输出
  
  <div id="container">
    <div>3</div>
    <div>2</div>
    <div>1</div>
  </div>
  ```

  ```javascript
  let container = document.getElementById("container");
  let childDivs = container.children;
  // 如果子节点有非 元素节点的子节点（就删除）
  for (let i = 0; i < childDivs.length; i++) {
      let childs = childDivs[i].children;
      let len = Array.from(childs).length;
      while(len--){
          if(childs[len].nodeType === 1) {
              childDivs[i].removeChild(childs[len]);
          }
      }
  }
  let childDivsArr = Array.from(childDivs).reverse();// 转为真数组
  let str = '';
  for (let i = 0; i < childDivsArr.length; i++) {
    str += childDivsArr[i].outerHTML;
  }
  container.innerHTML = str;
  ```

- 模板字符串的替换

```javascript
let str = "<% name >, 你好";
let obj = { name: "zhangsan" };
function fn(str, obj) {
    return str.replace(/<%(.+)>/g, (m, p) => obj[p.trim()]);
}
console.log(fn(str,obj));// zhangsan, 你好
```

- 怎样使 `a == 0 && a == 1 && a == 2` 返回 `true`

```javascript
// 利用对象的valueOf
let a = {
    count: 0,
    valueOf: function() {
        return a.count++;
    }
}

console.log(a == 0 && a == 1 && a == 2);//true 这里如果改为 === 是不成立的
```

```javascript
// 利用 Object.defineProperty 拦截
let a = {
    count: 0
}
Object.defineProperty(a, 'num', {
    get: () => a.count++
});

console.log(a.num === 0 && a.num === 1 && a.num === 2);//true 这里 === 也是成立的
```
```javascript
// 利用对象的 get
let a = {
    count: 0,
    get num() {
        return a.count++
    }
};
console.log(a.num === 0 && a.num === 1 && a.num ===2);//true 这里 === 也是成立的
```

```javascript
// 利用了Proxy
let a = {
    count: 0
};
let b = new Proxy(a, {
    get: (target , prop) => target.count++
});
console.log(b.num === 0 && b.num === 1 && b.num === 2);// true 这里 === 也是成立的
```

```javascript
//骚操作
let a = [0,1,2];
a.join = a.shift;
console.log(a == 0 && a == 1 && a == 2);//true === 是不成立的
```

- 查找子串 `Hi` 在字符串 `"Hi你好啊，Hi，防抖防抖Hi东方的是非得失"`里面出现的次数

```javascript
let str = "Hi你好啊，Hi，防抖防抖Hi东方的是非得失";
console.log(str.match(/Hi/g).length);//3
```

```javascript
let str = "Hi你好啊，Hi，防抖防抖Hi东方的是非得失";
let i = 0;
let reg = /Hi/g;
while(reg.exec(str)) {// 这里不能写成 while(/Hi/g.exec(str)) 这将导致无限循环
    i++
}
console.log(i);
```

