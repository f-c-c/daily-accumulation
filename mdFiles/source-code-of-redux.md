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
    dispatch, //我们最常用的dispatch方法，派发action
    //只要把 View 的更新函数，对于 React 项目就是 组件的 render，我们在 Redux 应用中并没有使用 store.subscribe，React Redux 中的 connect 方法隐式地帮我们完成了这个工作
    subscribe,//添加一个变化监听器。每当 dispatch（action）的时候就会执行
    getState,//通过该方法，我们可以拿到当前状态树state，就是简单返回当前state
    replaceReducer,//为了拿到所有 reducer 中的初始状态，这个方法主要用于 reducer 的热替换，替换一个新的大reducer，再跑一下dispatch
    [$$observable]: observable//观察者模式，用于处理订阅关系
  }
```

看源码可以看到，在调用 createStore 的时候，执行了关键的一行代码：

`dispatch({ type: ActionTypes.INIT })`

- 这一行代码传入了一个 `redux` 内部定义的一个随机且唯一的 `action` 

- 可以看出：dispatch方法 接受一个  action 作为参数，什么又是 action？？？
- action 本质上是 JavaScript 普通对象。我们约定，action 内必须使用一个字符串类型的 `type` 字段来表示将要执行的动作。多数情况下，`type` 会被定义成字符串常量。当应用规模越来越大时，建议使用单独的模块或文件来存放 action
- `dispatch`方法 完成了两个事情：
  - 第一个事情: 完成了state的更新，具体就是将当前的（旧的state）和 传入的 action 派发给了传入的 `currentReducer`，说的大白话一点就是：在 `dispatch`方法 内部调用了传给 `createStore` 方法的第一个参数：`reducer`，而这个`reducer`方法又接收两个参数，一个是旧的`state`，另一个是`action`，实现的代码就是这一行了：`currentState = currentReducer(currentState, action)`，经过这一步，`state `就已经改变了（根据我们的action来变）
  - 第二个事情：通知所有的 订阅者（大声的宣告：我的state已经改变了，你们知道了吗），这个时候执行更新`view`等操作

```javascript
  function dispatch(action) {
    try {
      isDispatching = true
      // 第一个事情： 完成了state的更新
      currentState = currentReducer(currentState, action)// 执行大的 reducer
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

- 上面提到dispatch里面执行了reducer，这个 reducer 又长啥样呢，下面👇给一个例子：

  reducer 里面是一个一个的方法，每一个方法对应一个state的状态：`info`、`computer`、`film`

  ```javascript
  import types from '../constants/index.js';
  import { combineReducers } from 'redux';
  
  // reducer 拆分（每个 reducer 只负责管理全局 state 中它负责的一部分）
  // 负责 info 部分
  function info(state = {}, action) {
      switch (action.type) {
          case types.CHANGE_NAME:
              return {
                  ...state,
                  name: state.name === '小东' ? '小明' : '小东'
              }
  
          case types.CHANGE_AGE:
              return {
                  ...state,
                  age: state.age + 1
              }
  
          default:
              return state
      }
  }
  
  // 负责 computer 部分
  function computer(state = {}, action) {
      switch (action.type) {
          case types.CHANGE_COMPUTER_SIZE:
              return {
                  ...state,
                  size: state.size - 1
              }
          default:
              return state
      }
  }
  
  // 负责 film 部分
  function film(state = {}, action) {
      switch (action.type) {
          case types.CHANGE_ASYNCDATA:
              return {
                  ...state,
                  subjects: action.resp.subjects
              }
  
          default:
              return state
      }
  }
  
  // 在 redux 源码的 createStore 里面有一句    dispatch({ type: ActionTypes.INIT })
  // 用一个不匹配任何 reducer 的 action 去调了下dispatch，会默认走每一个 reducer 的default 分支
  // 生成一个空的 state 树 （如果在我们初始化createStore 时没有指定初始state的话）
  // 空的 state 长这样： {info: {},computer: {},film: {}}
  
  // 导出一个大的 reducer
  // export default function reducer(state = {}, action) {
  //     return {
  //         info: info(state.info, action),
  //         computer: computer(state.computer, action)
  //     }
  // }
  
  // 导出一个大的 reducer  与上面的写法效果是一致的，利用了 redux 的api去合并 reducer 代码更加简洁
  const reducer = combineReducers({
      info,
      computer,
      film  
  })
  export default reducer 
  ```

看上面的代码很清晰：dispatch里面执行了reducer相当于 ：

```javascript
combineReducers({
    info,
    computer,
    film  
})(currentState, action)
```

### 三：顺藤摸瓜，我们终于找到了redux 的又一个重要api combineReducers

从字面意思就很好理解：组合`reducer`，我们将`reducer`拆分为了单个单个的（我叫它们为 `小reducer`），每一个`小reducer`只负责`state`里面的一个小数据(大`state`的一个属性)，`combineReducers` 的作用就是去遍历所有的`小 reducer`执行我们的每一个`小 reducer`，每一个`小 reducer` 返回一个新的 `小state`,`combineReducers`再将每一个小的 `state`组合为一个大的对象，这就是更新后的大的`state`

- 可以看出`combineReducers`接收一个对象，这个对象就是我们的`小 reducer`们：

  - ```javascript
    {
        info,
        computer,
        film  
    }
    ```

    这里的`info、computer、film`都是`小 reducer` ,都是**纯函数**，接收`小state`和`action`，返回一个新的`小state`

`combineReducers.js` 关键代码如下：

```javascript
export default function combineReducers(reducers) {
  const reducerKeys = Object.keys(reducers)// 得到小reducer 的key
  const finalReducers = {}
  // 错误处理
  for (let i = 0; i < reducerKeys.length; i++) {
    const key = reducerKeys[i]
		
    if (process.env.NODE_ENV !== 'production') {
      // 如果传入的小reducer 是'undefined' 报警告
      if (typeof reducers[key] === 'undefined') {
        warning(`No reducer provided for key "${key}"`)
      }
    }
		// 如果传入的小reducer 是 function类型，就将小reducer（函数）保存起来 放到 finalReducers[key]
    if (typeof reducers[key] === 'function') {
      finalReducers[key] = reducers[key]
    }
  }
  const finalReducerKeys = Object.keys(finalReducers)// 这里得到的就是 最终的小reducer 的key

  let unexpectedKeyCache
  if (process.env.NODE_ENV !== 'production') {
    unexpectedKeyCache = {}
  }

  let shapeAssertionError
  try {
    assertReducerShape(finalReducers)
  } catch (e) {
    shapeAssertionError = e
  }

  return function combination(state = {}, action) {
    if (shapeAssertionError) {
      throw shapeAssertionError
    }

    if (process.env.NODE_ENV !== 'production') {
      const warningMessage = getUnexpectedStateShapeWarningMessage(
        state,
        finalReducers,
        action,
        unexpectedKeyCache
      )
      if (warningMessage) {
        warning(warningMessage)
      }
    }

    let hasChanged = false
    const nextState = {}
    // 这里去循环小reducer
    for (let i = 0; i < finalReducerKeys.length; i++) {
      const key = finalReducerKeys[i]// 拿到每一个 小 reducer的key
      const reducer = finalReducers[key]// 拿到 key对应的 reducer函数
      const previousStateForKey = state[key]// 拿到小 reducer的key 在state中对应的数据（一部分旧数据state）(这里如果在createStore initState 么有的key而reducer却有,这里会拿到一个 undefined,传入小reducer时由于解构赋值的原因，取默认值{},导致最后会生成一个 空对象)
      const nextStateForKey = reducer(previousStateForKey, action)//得到这个 key 对应的新的 state
      if (typeof nextStateForKey === 'undefined') {
        const errorMessage = getUndefinedStateErrorMessage(key, action)
        throw new Error(errorMessage)
      }
      // 执行完一个小reducer就会得到一个 key 对应的新的state，将其放入新的对象 nextState（组合数据）
      nextState[key] = nextStateForKey
      hasChanged = hasChanged || nextStateForKey !== previousStateForKey
    }
    // 上面的👆for循环结束时，nextState 已经组合完毕（遍历了所有的小 reducer）
    return hasChanged ? nextState : state
  }
}
```

到此为止，我们学到了 redux 里面的两个重要的 API： `createStore` `combineReducers`

### 四，applyMiddleware

我们还注意到在 createStore 时的第三个参数 传递了一个: `Redux.applyMiddleware(thunkMiddleware)` ，而在createStore 中有这么一点代码：表示如果我们传递了第三个参数，最终调的 `createStore`，相当于：`enhancer(createStore)(reducer, preloadedState)`也相当于`applyMiddleware(mid1, mid2, mid3, ...)(createStore)(reducer, preloadedState)`

```javascript
  if (typeof enhancer !== 'undefined') {
    if (typeof enhancer !== 'function') {
      throw new Error('Expected the enhancer to be a function.')
    }

    return enhancer(createStore)(reducer, preloadedState)
  }
```

支持异步的中间件： thunkMiddleware 的源码如下：

```javascript
function createThunkMiddleware(extraArgument) {
  return ({ dispatch, getState }) => next => action => {
    if (typeof action === 'function') {
      return action(dispatch, getState, extraArgument);
    }

    return next(action);
  };
}

const thunk = createThunkMiddleware();
thunk.withExtraArgument = createThunkMiddleware;

export default thunk;
```

applyMiddleware 源码如下：

**整个 redux 我觉得最精髓的一行代码就是：`dispatch = compose(...chain)(store.dispatch)`**

```javascript
import compose from './compose'
export default function applyMiddleware(...middlewares) {
  return createStore => (...args) => {
    const store = createStore(...args)
    let dispatch = () => {
      throw new Error(
        'Dispatching while constructing your middleware is not allowed. ' +
          'Other middleware would not be applied to this dispatch.'
      )
    }

    const middlewareAPI = {
      getState: store.getState,
      dispatch: (...args) => dispatch(...args)
    }
    const chain = middlewares.map(middleware => middleware(middlewareAPI))
    dispatch = compose(...chain)(store.dispatch)

    return {
      ...store,
      dispatch
    }
  }
}
```

### compose 组合函数

```javascript
export default function compose(...funcs) {
  if (funcs.length === 0) {
    return arg => arg
  }

  if (funcs.length === 1) {
    return funcs[0]
  }

  return funcs.reduce((a, b) => (...args) => a(b(...args))) // 堪称经典
}
```



我们先暂时告别 redux，转而看看下面的东西：这是一个使用了redux的组件

```javascript
import React, { Component } from 'react';
import * as actions from '../../../store/actions';
import { connect } from 'react-redux'
import autobind from 'autobind-decorator'
import Con from '../../con/index'
import { Button } from 'antd';

@autobind
class About extends Component {
    constructor(props) {
        super(props);
    }
    changeName() {
        this.props.changeName();
    }
    changeAge() {
        this.props.changeAge();
    }
    changeAsyncData() {
        this.props.changeAsyncData();
    }
    changeComputerSize() {
        this.props.changeComputerSize();
    }
    render() {
        let { data: { info: { color, name, age }, computer: { size }, film: { subjects } } } = this.props;
        return <div>
            <Con color={color} name={name} age={age} size={size} subjects={subjects} />
            <Button type="primary" onClick={this.changeName}>用redux同步改变name</Button>
            <Button type="primary" onClick={this.changeAge}>用redux同步改变age++</Button>
            <Button type="primary" onClick={this.changeAsyncData}>异步数据获取豆瓣api</Button>
            <Button type="primary" onClick={this.changeComputerSize}>用redux组合改变电脑尺寸--</Button>
        </div>
    }
}
// 用于建立组件跟store的state的映射关系 将 redux 中的 state 数据 传给 App 放在了 this.props 里面
// 作为一个函数，它可以传入两个参数，结果一定要返回一个object
// 传入mapStateToProps之后，会订阅store的状态改变，在每次store的state发生变化的时候，都会被调用
let mapStateToProps = (state) => {
    // return {
    //     ...state
    // }
    // 像上面👆的写法 store 里面的数据是直接灌如了 this.props
    // 像下面👇的写法 store 里面的数据是直接灌如了 this.props.data, 没什么大区别，只是在 取数据时不同
    return {
        data: state
    }
}
// 建立组件跟store.dispatch的映射关系，在组件里面调用 this.props.changeName 就会调用 store.dispatch 去派发给 reducer 修改数据
// 可以是一个object，也可以传入函数
let mapDispatchToProps = (dispatch) => {
    return {
        changeName: () => {
            dispatch(actions.changeName())
        },
        changeAge: () => {
            dispatch(actions.changeAge())
        },
        changeAsyncData: () => {
            // 为什么 异步actions 时 在actions 里面已经 dispatch 了，这里还要 dispatch
            dispatch(actions.changeAsyncData());
        },
        changeComputerSize: () => {
            dispatch(actions.changeComputerSize());
        }
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(About);
```

如上所示：一个组件里面有两个特别的方法：`mapStateToProps` 和 `mapDispatchToProps`  我们注意到在导出时有一个：`connect(mapStateToProps, mapDispatchToProps)(About);`，这时我们打开 `react-redux`的源码去看看 这个 `connect` 到底干了些什么，`react-redux` 里面全是高阶函数，函数式编程体现的淋漓尽致

同样的方法先看 index.js: 可以看见 `react-redux` 向外导出了很多东西，而我们这里关心的 `connect` 也在里面



去掉注释的createStore版本：

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

