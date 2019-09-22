# Strange knowledge of js

### 关于js里面数字问题

```javascript
console.log(isFinite(Number.MAX_VALUE + 1e291)) // true
console.log(isFinite(Number.MAX_VALUE + 1e292)) // false
// 当运算数和数字最大值保持在相同精度维度上时，才可与数字最大值发生运算
console.log(Number.MAX_VALUE+1e291 === Number.MAX_VALUE); //true
console.log(Number.MAX_VALUE+1e292 === Number.MAX_VALUE); //false

// IEEE754格式表示数字，浮点数不是精确值
console.log(0.1 + 0.2); //0.30000000000000004

// 0 +0 -0 都是恒等的
console.log(0 === -0) //true
console.log(0 === +0) //true
console.log(-0 === +0) //true

// 关于 NaN
// 0/0 得到NaN，正数/0 得到正无穷（Infinity），负数/0 得到负无穷（-Infinity）
// NaN 与任何值都不相等包括它自己，任何涉及 NaN 的操作也将返回NaN
console.log(0/0); //NaN
console.log(5/0); //Infinity
console.log(-5/0); //-Infinity
console.log(NaN == NaN);// false
console.log(NaN + 1);// NaN

// 关于将 parseInt 传给数组的 map
console.log(parseInt(1,0)); // 1
console.log(parseInt(2,1)); // NaN
console.log(parseInt(3,2)); // NaN
console.log([1,2,3].map(parseInt));//  [1, NaN, NaN]

// parseInt 对于Boolean、Null、Undefined三种类型 直接返回 NaN
console.log(parseInt(true));// NaN
console.log(parseInt(false));// NaN
console.log(parseInt(null));// NaN
console.log(parseInt(undefined));// NaN
console.log(parseInt([1,2]));// 1 相当于 console.log(parseInt('1,2'));
```

