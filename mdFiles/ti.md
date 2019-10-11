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
  // 去掉 ]
  let str0 = source.replace(/(\])/g, () => {
      return '';
  });
  // 将 [ 换为 .
  let str1 = str0.replace(/\[/g, () => {
      return '.';
  });
  // 去掉 ' 和 "
  let str2 = str1.replace(/'|"/g, () => {
      return '';
  });
  // 分割为数组
  let result = str2.split(".");
  console.log('result', result);// ["a", "0", "b", "cd", "e"]
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

