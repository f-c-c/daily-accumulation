# Search algorithm

> **查找定义：**根据给定的某个值，在查找表中确定一个其关键字等于给定值的数据元素

> 1）静态查找和动态查找；
>
> 　　　　注：静态或者动态都是针对查找表而言的。动态表指查找表中有删除和插入操作的表。
>
> 2）无序查找和有序查找。
>
> 　　　　无序查找：被查找数列有序无序均可；
>
> 　　　　有序查找：被查找数列必须为有序数列。

#### 顺序查找

> 就是按照字面意思：按照顺序一个一个的比较，顺序查找一般针对被查找序列是无序的，是一种暴力查找

```javascript
function orderFind (arr, data) {
  let len = arr.length;
  for (let i = 0; i <= len - 1; i++) {
    if (data === arr[i]) return i;
  }
  return -1;
}
```

找最小值

```javascript
function findMin (arr) {
  let len = arr.length;
  let minIndex = 0;
  for (let i = 1; i <= len - 1; i++) {
    if (arr[i] < arr[minIndex]) {
      minIndex = i;
    }
  }
  return minIndex;
}
```

#### 二分查找

> 针对已经去重、排序的列表，二分查找比顺序查找更加高效
>
> **折半查找的前提条件是需要有序表顺序存储，对于静态查找表，一次排序后不再变化，折半查找能得到不错的效率**

版本一：

```javascript
function binSearch1(arr, data) {
    let len = arr.length;
    let left = 0;
    let right = len - 1;
    while (left <= right) {
        let mid = Math.trunc((left + right) / 2);
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

版本二（递归）：

```javascript
function binSearch2(arr, data, left, right) {
    let len = arr.length;
    left = left || 0;
    right = right || len - 1;
    let mid = Math.trunc((left + right) / 2);
    if ((mid === 0 || mid === len - 1) && arr[mid] !== data) {
        return -1;
    }
    if (arr[mid] === data) {
        return mid;
    } else if (arr[mid] > data) {
        return binSearch2(arr, data, left, mid - 1);
    } else {
        return binSearch2(arr, data, mid + 1, right);
    }
}
```

