vue 分析

没有配置sourcemap  和 生产环境 之前的马桶官网 和 多闪官网，直接源代码都上线了，写这个的人一点都不懂

百度有一个 san.js 也是兼容 ie 6 78 的双向数据绑定的

vue 的核心代码有  Object.defineProperty()

IE 低版本不支持 Object.defineProperty() 这就是 在低版本的 ie 中不支持 vue 的原因

MVVM 可不是 vue 最先搞的，整个 mvvm 最开始是 微软的 `silverlight` 当年的概念版本qq，前端圈里面第一个出现双向数据绑定的库是 `knockoutjs ` 也不是 vue， 很多人说MVVM是 vue 创建的，这瞎胡闹，这是微软的东西

react 是用到状态机





js 的 **同步队列** 和 **异步执行栈**（又分为 宏任务 和 微任务）





