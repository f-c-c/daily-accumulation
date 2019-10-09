# 函数防抖和函数截流

### 函数防抖

就是**持续不断调用一个函数，只有最后一次会调用**，就像弹簧一样，我们不断按压弹簧，只有当我们最后松手那一下弹簧才弹起

应用：浏览器的窗口 `resize` 事件持续触发，只有在最后一下才真正触发

```html
<body>
    <button id="debounce">演示防抖</button>
    <script>
        function debounce(fn, tm) {
            let timer = null;
            return function (...argu) {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fn.apply(this, ...argu);
                }, tm);
            }
        }
        function fn() {
            console.log('被防抖的函数，持续点击只有最后一次会执行');
        }
        let debounceEle = document.querySelector("#debounce");
        debounceEle.onclick = debounce(fn, 2000);
    </script>
</body>
```

### 函数截流

就是在一定时间段内，无论怎么调用这个函数，这个函数只执行一次

应用：防止提交按钮多次点击

```html
<body>
    <button id="closure">演示截流</button>
    <script>
        function closure(fn, tm) {
            let canRun = true;
            let timer = null;
            return function (...argu) {
                if (!canRun) return;
                canRun = false;
                timer = setTimeout(() => {
                    canRun = true;
                    timer = null;
                    fn.apply(this, ...argu);
                }, tm);
            }
        }
        function fn() {
            console.log('被截流的函数，一定时间段只能执行一次，无论调用多少次');
        }
        let closureEle = document.querySelector("#closure");
        closureEle.onclick = closure(fn, 2000);
    </script>
</body>
```



