1. `["1", "3", "10"].map(parseInt)`

   输出 `1 NaN 2` 相当于`parseInt("1", 2)` `parseInt("3", 2)` `parseInt("10", 2)`，parseInt 的第二个参数默认为 2

