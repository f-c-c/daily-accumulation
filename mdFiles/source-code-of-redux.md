# redux 源码分析

首先打开 redux 源码的 src 目录，里面有五个重要的文件

- `createStore.js`
- `compose.js`
- `combineReducers.js`
- `bindActionCreators.js`
- `applyMiddleware.js`

### 一：首先看redux的入口文件 index.js

可以看到 该文件向外导出了 一些东西：`createStore`、`combineReducers`、`bindActionCreators`、`applyMiddleware`、`compose`、`__DO_NOT_USE__ActionTypes`，只要我们引入了 `redux` 我们就可以调用其上面挂载的5个方法 以及一个对象：`__DO_NOT_USE__ActionTypes`

```javascript
export {
  createStore,
  combineReducers,
  bindActionCreators,
  applyMiddleware,
  compose,
  __DO_NOT_USE__ActionTypes
}
```

### 二：再看 `createStore.js`

这个文件向外导出了一个函数：`createStore`，我们平时像👇这个样子去使用这个函数创建一个 `store`：

```javascript
import reducer from './reducer'
import * as Redux from 'redux';
import thunkMiddleware from 'redux-thunk';

let initState = {
    info: {
        name: '小明',
        age: 28,
        job: 'qunar',
        address: '北京'
    },
    computer: {
        color: 'red',
        size: 15,
        prize: 20000
    }
}
const store = Redux.createStore(reducer, initState, Redux.applyMiddleware(thunkMiddleware));
export default store;
```

 返回的 `store` 是一个对象， 上面挂了一些方法：

```javascript
  return {
    dispatch,
    subscribe,
    getState,
    replaceReducer,
    [$$observable]: observable
  }
```

看源码可以看到，在调用 createStore 的时候，执行了关键的一行代码：

`dispatch({ type: ActionTypes.INIT })`

- 这一行代码传入了一个 `redux` 内部定义的一个随机且唯一的 `action` `dispatch`方法去掉注释和相关错误判断的代码如下：

- 可以看出：dispatch方法 接受一个  action 作为参数，什么又是 action？？？
- action 本质上是 JavaScript 普通对象。我们约定，action 内必须使用一个字符串类型的 `type` 字段来表示将要执行的动作。多数情况下，`type` 会被定义成字符串常量。当应用规模越来越大时，建议使用单独的模块或文件来存放 action
- Dispatch 完成了两个事情：
  - 第一个事情: 完成了state的更新，具体就是将当前的（旧的state）和 传入的 action 派发给了传入的 `reducer`，说的大白话一点就是：在 dispatch方法 内部调用了传给 createStore 方法的第一个参数：`reducer`，而这个reducer方法接收两个参数，一个是旧的state，另一个是action，实现的代码就是这一行了：`currentState = currentReducer(currentState, action)`，经过这一步，state 就已经改变了（根据我们的action来变）
  - 第二个事情：通知所有的 订阅者（大声的宣告：我的state已经改变了，你们知道了吗），这个时候试图接收到了，更新视图

```javascript
  function dispatch(action) {
    try {
      isDispatching = true
      // 第一个事情： 完成了state的更新
      currentState = currentReducer(currentState, action)
    } finally {
      isDispatching = false
    }
		// 第二个事情： 通知所有的 订阅者 state 已经被更新
    const listeners = (currentListeners = nextListeners)
    for (let i = 0; i < listeners.length; i++) {
      const listener = listeners[i]
      listener()
    }

    return action
  }
```



去掉注释的版本：

```javascript
import $$observable from 'symbol-observable'
import ActionTypes from './utils/actionTypes'
import isPlainObject from './utils/isPlainObject'

export default function createStore(reducer, preloadedState, enhancer) {
  if (
    (typeof preloadedState === 'function' && typeof enhancer === 'function') ||
    (typeof enhancer === 'function' && typeof arguments[3] === 'function')
  ) {
    throw new Error(
      'It looks like you are passing several store enhancers to ' +
        'createStore(). This is not supported. Instead, compose them ' +
        'together to a single function'
    )
  }

  if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
    enhancer = preloadedState
    preloadedState = undefined
  }

  if (typeof enhancer !== 'undefined') {
    if (typeof enhancer !== 'function') {
      throw new Error('Expected the enhancer to be a function.')
    }

    return enhancer(createStore)(reducer, preloadedState)
  }

  if (typeof reducer !== 'function') {
    throw new Error('Expected the reducer to be a function.')
  }

  let currentReducer = reducer
  let currentState = preloadedState
  let currentListeners = []
  let nextListeners = currentListeners
  let isDispatching = false //是否在reducer改变数据

  function ensureCanMutateNextListeners() {
    if (nextListeners === currentListeners) {
      nextListeners = currentListeners.slice()
    }
  }

  // 获取 state
  function getState() {
    if (isDispatching) {
      throw new Error(
        'You may not call store.getState() while the reducer is executing. ' +
          'The reducer has already received the state as an argument. ' +
          'Pass it down from the top reducer instead of reading it from the store.'
      )
    }

    return currentState
  }
  // 观察者模式 订阅
  function subscribe(listener) {
    if (typeof listener !== 'function') {
      throw new Error('Expected the listener to be a function.')
    }

    if (isDispatching) {
      throw new Error(
        'You may not call store.subscribe() while the reducer is executing. ' +
          'If you would like to be notified after the store has been updated, subscribe from a ' +
          'component and invoke store.getState() in the callback to access the latest state. ' +
          'See https://redux.js.org/api-reference/store#subscribe(listener) for more details.'
      )
    }

    let isSubscribed = true

    ensureCanMutateNextListeners()
    nextListeners.push(listener)

    return function unsubscribe() {
      if (!isSubscribed) {
        return
      }

      if (isDispatching) {
        throw new Error(
          'You may not unsubscribe from a store listener while the reducer is executing. ' +
            'See https://redux.js.org/api-reference/store#subscribe(listener) for more details.'
        )
      }

      isSubscribed = false

      ensureCanMutateNextListeners()
      const index = nextListeners.indexOf(listener)
      nextListeners.splice(index, 1)
    }
  }

  // 把action派发给reducer 去改变state ，然后 通知所有的 订阅者
  function dispatch(action) {
    if (!isPlainObject(action)) {
      throw new Error(
        'Actions must be plain objects. ' +
          'Use custom middleware for async actions.'
      )
    }

    if (typeof action.type === 'undefined') {
      throw new Error(
        'Actions may not have an undefined "type" property. ' +
          'Have you misspelled a constant?'
      )
    }

    if (isDispatching) {
      throw new Error('Reducers may not dispatch actions.')
    }

    try {
      isDispatching = true
      currentState = currentReducer(currentState, action)
    } finally {
      isDispatching = false
    }

    const listeners = (currentListeners = nextListeners)
    for (let i = 0; i < listeners.length; i++) {
      const listener = listeners[i]
      listener()
    }

    return action
  }

  function replaceReducer(nextReducer) {
    if (typeof nextReducer !== 'function') {
      throw new Error('Expected the nextReducer to be a function.')
    }

    currentReducer = nextReducer
    dispatch({ type: ActionTypes.REPLACE })
  }

  function observable() {
    const outerSubscribe = subscribe
    return {
      subscribe(observer) {
        if (typeof observer !== 'object' || observer === null) {
          throw new TypeError('Expected the observer to be an object.')
        }

        function observeState() {
          if (observer.next) {
            observer.next(getState())
          }
        }

        observeState()
        const unsubscribe = outerSubscribe(observeState)
        return { unsubscribe }
      },

      [$$observable]() {
        return this
      }
    }
  }

  dispatch({ type: ActionTypes.INIT })

  return {
    dispatch,
    subscribe,
    getState,
    replaceReducer,
    [$$observable]: observable
  }
}

```

