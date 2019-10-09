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