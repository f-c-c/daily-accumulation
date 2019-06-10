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

#### 二分查找-有序查找

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

#### 插值查找-有序查找

> 首先考虑一个新问题，为什么上述算法一定要是折半，而不是折四分之一或者折更多呢？
>
> 打个比方，在英文字典里面查“apple”，你下意识翻开字典是翻前面的书页还是后面的书页呢？如果再让你查“zoo”，你又怎么查？很显然，这里你绝对不会是从中间开始查起，而是有一定目的的往前或往后翻。
>
> 同样的，比如要在取值范围1 ~ 10000 之间 100 个元素从小到大均匀分布的数组中查找5， 我们自然会考虑从数组下标较小的开始查找。
>
> mid=(low+high)/2, 即mid=low+1/2*(high-low);
>
> 通过类比，我们可以将查找的点改进为如下：
>
> mid=low+(key-a[low])/(a[high]-a[low])*(high-low)
>
> **基本思想：**基于二分查找算法，将查找点的选择改进为自适应选择，可以提高查找效率。当然，差值查找也属于有序查找。
>
> 　　注：**对于表长较大，而关键字分布又比较均匀的查找表来说，插值查找算法的平均性能比折半查找要好的多。反之，数组中如果分布非常不均匀，那么插值查找未必是很合适的选择。**
>
> 　　**复杂度分析：查找成功或者失败的时间复杂度均为O(log2(log2n))。**

```javascript
function InsertionSearch(arr, data) {
    let len = arr.length;
    let left = 0;
    let right = len - 1;
    if (arr[left] > data || arr[right] < data) {
        return -1;
    }
    while (left <= right) {
        // let mid = Math.trunc((left + right) / 2);
        // let mid = Math.trunc(left + (1 / 2) * (right - left));
        let mid = Math.trunc(left + (data - arr[left]) / (arr[right] - arr[left]) * (right - left));
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

