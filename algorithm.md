# 数据结构与算法

概念性的东西：
> 时间频度：一个算法花费的时间与算法中语句的执行次数成正比例，哪个算法中语句执行次数多，它花费时间就多。一个算法中的语句执行次数称为语句频度或时间频度。记为$T(n)$。
> n称为问题的规模，当n不断变化时，时间频度$T(n)$也会不断变化。但有时我们想知道它变化时呈现什么规律。为此，我们引入时间复杂度概念，一般情况下，算法中基本操作重复执行的次数是问题规模n的某个函数，$O(f(n))$ 为算法的渐进时间复杂度，简称时间复杂度
> 算法中语句执行次数为一个常数，则时间复杂度为$O(1)$
> 在时间频度不相同时，时间复杂度有可能相同，如$T(n)=n^2+3n+4$与$T(n)=4n^2+2n+1$它们的频度不同，但时间复杂度相同，都为$O(n^2)$
> 常见的时间复杂度有 常数阶$O(1)$,对数阶$O(log2n)$(以2为底n的对数，下同),线性阶$O(n)$,线性对数阶$O(nlog2n)$,平方阶$O(n^2)$，立方阶$O(n^3)$,...，k次方阶$O(n^k)$,指数阶$O(2^n)$。随着问题规模n的不断增大，上述时间复杂度不断增大，算法的执行效率越低

| 常数阶 | 对数阶       | 线性阶 | 线性对数阶   | 平方阶   | 立方阶   | ……   | K次方阶  | 指数阶   |
| ------ | ------------ | ------ | ------------ | -------- | -------- | ---- | -------- | -------- |
| $O(1)$ | $O(log2 n )$ | $O(n)$ | $O(nlog2 n)$ | $O(n2 )$ | $O(n3 )$ |      | $O(nk )$ | $O(2n )$ |


### 选择排序

复杂度为 $n^2$

* 每次外层循环，将相对最小的元素往左边放
* 外层循环次数为：arr.length - 1
```javascript
function bubbleSort (arr) {
  let len = arr.length;
  for (let i = 0; i <= len - 2; i++) {
    for (let j = i + 1; j <= len - 1; j++) {
      if (arr[i] > arr[j]) {
        swap(arr, i, j);
      }
    }
  }
  function swap (arr, index1, index2) {
    let temp = arr[index1];
    arr[index1] = arr[index2];
    arr[index2] = temp;
  }
}
```

### 冒泡排序

复杂度为 $n^2$

* 每次外层循环，将相对最大的元素往右边放
* 外层循环次数：arr.length - 1

```javascript
function bubbleSort1 (arr) {
  let len = arr.length;
  for (let i = 0; i <= len - 2; i++) {
    for (let j = 0; j <= len - 2 - i; j++) {
      if (arr[j] > arr[j+1]) {
        swap(arr, j, j + 1);
      }
    }
  }
  function swap (arr, index1, index2) {
    let temp = arr[index1];
    arr[index1] = arr[index2];
    arr[index2] = temp;
  }
}
```

### 插入排序

* 从下标第 1 开始循环，大循环次数为 arr.length - 1
* 看每一个元素 应该被插入的位置

```javascript
function insertSort2 (arr) {
  let len = arr.length, temp, j;
  //循环次数 len - 1
  for (let i = 1; i <= len - 1; i++) {
    temp = arr [i];
    j = i;
    while(j - 1 >= 0 && arr[j - 1] > temp) {
      arr[j] = arr[j - 1];
      j--;
    }
    arr[j] = temp;
  }
}
```

### 归并排序

### 快速排序

```javascript
function quickSort (arr) {
  if (arr.length == 0) {
    return [];
  }
  let reference = arr[0];
  let len = arr.length;
  let left = [];
  let right = [];
  for (let i = 1; i <= len - 1; i++) {
    if (arr[i] < reference) {
      left.push(arr[i]);
    } else {
      right.push(arr[i]);
    }
  }
  return arguments.callee(left).concat(reference, arguments.callee(right));
}
```

### 排序算法效率比较

统一用下列代码去比较耗时

```javascript
let arr = [];
for (let i = 0; i < 100000; i++) {
  arr.push(Math.floor((Math.random() * 10000) + 1));
}
let start = Number(new Date());
bubbleSort(arr);
let end = Number(new Date());
console.log(end - start);
```



| 算法     | 数据量 | 耗时       |
| -------- | ------ | ---------- |
| 冒泡排序 | 10万   | 31s 左右   |
| 选择排序 | 10万   | 17s 左右   |
| 插入排序 | 10万   | 5s 左右    |
| 快速排序 | 10万   | 178 ms左右 |
|          |        |            |

## 检索算法

### 顺序查找

就是按照字面意思：按照顺序一个一个的比较，顺序查找一般针对被查找序列是无序的，是一种暴力查找

```javascript
function orderFind (arr, data) {
  let len = arr.length;
  for (let i = 0; i <= len - 1; i++) {
    if (data === arr[i]) return true;
  }
  return false;
}
```

类似的  查找最小值 和 最大值：

```javascript
function findMin (arr) {
  let len = arr.length;
  let min = arr[0];
  for (let i = 1; i <= len - 1; i++) {
    if (arr[i] < min) {
      min = arr[i];
    }
  }
  return min;
}
function findMax (arr) {
  let len = arr.length;
  let max = arr[0];
  for (let i = 1; i <= len - 1; i++) {
    if (arr[i] > max) {
      max = arr[i];
    }
  }
  return max;
}
```

### 二分查找

针对已经去重、排序的列表，二分查找比顺序查找更加高效

```javascript
function binSearch (arr, data) {
  let len = arr.length;
  let left = 0;
  let right = len - 1;
  while(left <= right) {
    let mid = Math.floor((left + right) / 2);
    if (arr[mid] < data) {
      left = mid + 1;
    } else if (arr[mid] > data) {
      right = mid - 1;
    } else {
      return mid
    }
  }
  return -1;
}
```

