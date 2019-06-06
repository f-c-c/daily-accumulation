# 十大经典排序算法总结

概念性的东西：
> **时间频度**：一个算法花费的时间与算法中语句的执行次数成正比例，哪个算法中语句执行次数多，它花费时间就多。一个算法中的语句执行次数称为语句频度或时间频度。记为$T(n)$。
>
> **n称为问题的规模**，当n不断变化时，时间频度$T(n)$也会不断变化。但有时我们想知道它变化时呈现什么规律。为此，我们引入时间复杂度概念，一般情况下，算法中基本操作重复执行的次数是问题规模n的某个函数，$O(f(n))$ 为算法的渐进时间复杂度，简称时间复杂度
>
> 算法中语句执行次数为一个常数，则时间复杂度为$O(1)$
>
> 在时间频度不相同时，时间复杂度有可能相同，如$T(n)=n^2+3n+4$与$T(n)=4n^2+2n+1$它们的频度不同，但时间复杂度相同，都为$O(n^2)$
>
> 常见的时间复杂度有 常数阶$O(1)$,对数阶$O(log2n)$(以2为底n的对数，下同),线性阶$O(n)$,线性对数阶$O(nlog2n)$,平方阶$O(n^2)$，立方阶$O(n^3)$,...，k次方阶$O(n^k)$,指数阶$O(2^n)$。随着问题规模n的不断增大，上述时间复杂度不断增大，算法的执行效率越低

| 常数阶 | 对数阶       | 线性阶 | 线性对数阶   | 平方阶   | 立方阶   | ……   | K次方阶  | 指数阶   |
| ------ | ------------ | ------ | ------------ | -------- | -------- | ---- | -------- | -------- |
| $O(1)$ | $O(log2 n )$ | $O(n)$ | $O(nlog2 n)$ | $O(n2 )$ | $O(n3 )$ |      | $O(nk )$ | $O(2n )$ |


> **稳定**：如果a原本在b前面，而a=b，排序之后a仍然在b的前面
> **不稳定**：如果a原本在b的前面，而a=b，排序之后a可能会出现在b的后面
> **内排序**：所有排序操作都在内存中完成
> **外排序**：由于数据太大，因此把数据放在磁盘中，而排序通过磁盘和内存的数据传输才能进行
> **时间复杂度**：对排序数据的总的操作次数。反映当n变化时，操作次数呈现什么规律
> **空间复杂度**：是指算法在计算机内执行时所需存储空间的度量，它也是数据规模n的函数

#### 一. 冒泡排序(Bubble Sort)

> 冒泡排序是一种简单的排序算法。它重复地走访过要排序的数列，一次比较两个元素，如果它们的顺序错误就把它们交换过来。走访数列的工作是重复地进行直到没有再需要交换，也就是说该数列已经排序完成。这个算法的名字由来是因为越小的元素会经由交换慢慢“浮”到数列的顶端

算法描述：
- 时间复杂度为$O(n2 )$
- 比较相邻的元素。如果第一个比第二个大，就交换它们两个；
- 对每一对相邻元素作同样的工作，从开始第一对到结尾的最后一对，这样在最后的元素应该会是最大的数；
- 针对所有的元素重复以上的步骤，除了最后一个；
- 重复步骤1~3，直到排序完成。
* 每次外层循环，将相对最大的元素往右边放
* 外层循环次数：arr.length - 1

![bubble-sort](./assert/bubble-sort.gif)

```javascript
function bubbleSort(arr) {
    let len = arr.length;
    // 交换数组中两个元素
    function swap(arr, a, b) {
        let temp = arr[a];
        arr[a] = arr[b];
        arr[b] = temp;
    }
    for (let i = 0; i < len - 1; i++) {
        for (let j = 0; j < len - 1 - i; j++) {
            if (arr[j] > arr[j + 1]) {
                swap(arr, j, j + 1);
            }
        }
    }
    return arr;
}
```
#### 二. 选择排序(Selection Sort)
> 选择排序(Selection-sort)是一种简单直观的排序算法。工作原理：首先在未排序序列中找到最小（大）元素，存放到排序序列的起始位置，然后，再从剩余未排序元素中继续寻找最小（大）元素，然后放到已排序序列的末尾。以此类推，直到所有元素均排序完毕
>
> 表现最稳定的排序算法之一，因为无论什么数据进去都是$O(n2)$的时间复杂度，所以用到它的时候，数据规模越小越好。唯一的好处可能就是不占用额外的内存空间

算法描述：
* 时间复杂度为 $n^2$
* 每次外层循环，将相对最小的元素往左边放
* 外层循环次数为：arr.length - 1
* n-1趟结束，数组有序化了

![select-sort](./assert/select-sort.gif)
```javascript
function selectSort(arr) {
    let len = arr.length;
    // 交换数组中两个元素
    function swap(arr, a, b) {
        let temp = arr[a];
        arr[a] = arr[b];
        arr[b] = temp;
    }
    for (let i = 0; i < len - 1; i++) {
        let minIndex = i; //最小值的索引
        for (let j = i + 1; j < len; j++) {
            if (arr[j] < arr[minIndex]) {
                minIndex = j;
            }
        }
        swap(arr, i, minIndex);
    }
    return arr;
}
```

#### 三. 插入排序(Insertion-Sort)

> 插入排序（Insertion-Sort）的算法描述是一种简单直观的排序算法。它的工作原理是通过构建有序序列，对于未排序数据，在已排序序列中从后向前扫描，找到相应位置并插入
> 插入排序在实现上，通常采用in-place排序（即只需用到O(1)的额外空间的排序），因而在从后向前扫描过程中，需要反复把已排序元素逐步向后挪位，为最新元素提供插入空间

算法描述：
一般来说，插入排序都采用in-place在数组上实现。具体算法描述如下：
* 从第一个元素开始，该元素可以认为已经被排序；
* 取出下一个元素，在已经排序的元素序列中从后向前扫描；
* 如果该元素（已排序）大于新元素，将该元素移到下一位置；
* 重复步骤3，直到找到已排序的元素小于或者等于新元素的位置；
* 将新元素插入到该位置后；
* 重复步骤2~5。
* 从下标第 1 开始循环，大循环次数为 arr.length - 1
* 看每一个元素 应该被插入的位置

![insert-sort](./assert/insert-sort.gif)
```javascript
function insertSort(arr) {
    let len = arr.length;
    let current, preIndex;
    for (let i = 1; i < len; i++) {
        current = arr[i];
        preIndex = i - 1;
        while(preIndex >= 0 && arr[preIndex] > current) {
            arr[preIndex + 1] = arr[preIndex];
            preIndex--;
        }
        arr[preIndex + 1] = current;
    }
    return arr;
}
```

#### 四. 希尔排序(Shell Sort)
> 1959年Shell发明，第一个突破O(n2)的排序算法，是简单插入排序的改进版。它与插入排序的不同之处在于，它会优先比较距离较远的元素。希尔排序又叫缩小增量排序。

![shell-sort](./assert/shell-sort.jpg)

```javascript
function shellSort(arr) {
    let len = arr.length;
    let temp, gap = Math.trunc(len / 2);
    while (gap > 0) {
        // 实际操作是多个分组交替执行
        for (let i = gap; i < len; i++) {
            temp = arr[i];
            let preIndex = i - gap;
            while (preIndex >= 0 && temp < arr[preIndex]) {
                arr[preIndex + gap] = arr[preIndex];
                preIndex -= gap;
            }
            arr[preIndex + gap] = temp;
        }
        gap = Math.trunc(gap / 2);
    }
    return arr;
}
```

#### 五. 归并排序(Merge Sort)

> 和选择排序一样，归并排序的性能不受输入数据的影响，但表现比选择排序好的多，因为始终都是O(n log n）的时间复杂度。代价是需要额外的内存空间。
>
> 归并排序是建立在归并操作上的一种有效的排序算法。该算法是采用分治法（Divide and Conquer）的一个非常典型的应用。归并排序是一种稳定的排序方法。将已有序的子序列合并，得到完全有序的序列；即先使每个子序列有序，再使子序列段间有序。若将两个有序表合并成一个有序表，称为2-路归并

![merge-sort](./assert/merge-sort.gif)

```javascript
// 归并排序
function mergeSort(arr) {
    // 归并函数
    function merge(left, right) {
        let result = [];
        while (left.length > 0 && right.length > 0) {
            if (left[0] > right[0]) {
                result.push(right.shift());
            } else {
                result.push(left.shift());
            }
        }
        while (left.length > 0) {
            result.push(left.shift());
        }
        while (right.length > 0) {
            result.push(right.shift());
        }
        return result;
    }
    let len = arr.length;
    if (len < 2) {
        return arr;
    }
    let mid = Math.trunc(len / 2);
    let left = arr.slice(0, mid);
    let right = arr.slice(mid);
    return merge(mergeSort(left), mergeSort(right));
}
```




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

