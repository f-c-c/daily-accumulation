# amazing js

```javascript
for (let i = 0; i < 3; i++) {
    let i;
    console.log(i);
}
```

上述代码会输出三个 `undefined`，for循环()里面的变量是一个外部作用域，{}里面的变量是在一个子级作用域中，也就是说类似于下面的代码：

```javascript
{
    let i = 0;
    {
        let i;
        console.log(i);
    }
}
```

let 不允许在相同作用域中重复声明，但是在子级作用域中是可以的，并且子级的同名变量不会受外面影响

再看下面代码：

```javascript
for (let i = 0; i < 3; i++) {
    var i;// Identifier 'i' has already been declared
    console.log(i);
}

for (let i = 0; i < 3; i++) {
    console.log(i);
    var i = 9;
}
```

类似于：

```javascript
{
    let i = 0;
    {
        var i;// Identifier 'i' has already been declared
    }
}
```

再看下面： 

```javascript
for (let i = 0; i < 3; i++) {
    console.log(i);//i is not defined
    let i = 4;
}
```

类似于：有暂时性死区的问题

```javascript
{
    let i = 0;
    {
        console.log(i);//i is not defined
        let i = 4;
    }
}
```

