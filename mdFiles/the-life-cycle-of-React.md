# React 的生命周期

> 组件的生命周期包含三个阶段：创建阶段（Mounting）、运行和交互阶段（Updating）、卸载阶段（Unmounting）

#### 创建阶段

特点：该阶段的函数只执行一次

> constructor() 
> componentWillMount() 
> render() 
> componentDidMount()

1. **constructor** 

   constructor参数接受两个参数props,context

   可以获取到父组件传下来的的props,context,如果你想在constructor**构造函数内部(注意是内部哦，在组件其他地方是可以直接接收的)**使用props或context,则需要super函数

   ```
   constructor(props,context) {
     super(props,context)//只要组件存在constructor,就必要要写super,否则this指向会错误
     console.log(this.props,this.context) // 在内部可以使用props和context
   }
   ```

2. **componentWillMount 组件将要挂载**

   组件刚经历constructor,初始完数据，

   组件还未进入render，组件还未渲染完成，dom还未渲染

   - 说明：组件被挂载到页面之前调用，其在render()之前被调用，因此在这方法里`同步`地设置状态将不会触发重渲染
   - 注意：无法获取页面中的DOM对象
   - 注意：可以调用 `setState()` 方法来改变状态值
   - 用途：发送ajax请求获取数据

3. **render 函数**

   - 作用：渲染组件到页面中，无法获取页面中的DOM对象
   - 注意：**不要在render方法中调用 setState() 方法，否则会递归渲染**
     - 原因说明：状态改变会重新调用`render()`，`render()`又重新改变状态

4. **componentDidMount 组件渲染完成**

   - 1 组件已经挂载到页面中
   - 2 可以进行DOM操作，比如：获取到组件内部的DOM对象
   - 3 可以**发送请求**获取数据
   - 4 可以通过 `setState()` 修改状态的值
   - 注意：在这里修改状态会重新渲染

#### 运行阶段

特点：该阶段的函数执行多次，每当组件的props或者state改变的时候，都会触发运行阶段的函数

> componentWillReceiveProps() 
> shouldComponentUpdate() 
> componentWillUpdate() 
> render() 
> componentDidUpdate()

1. **componentWillReceiveProps**

- 说明：组件接受到新的`props`前触发这个方法

- 参数：当前组件`props`值

- 可以通过 `this.props` 获取到上一次的值

- 使用：若你需要响应属性的改变，可以通过对比`this.props`和`nextProps`并在该方法中使用`this.setState()`处理状态改变

- 注意：修改`state`不会触发该方法

  ```javascript
  componentWillReceiveProps(nextProps) {
    console.warn('componentWillReceiveProps', nextProps)
  }
  ```

2. **shouldComponentUpdate**

- 作用：根据这个方法的返回值决定是否重新渲染组件，返回`true`重新渲染，否则不渲染
- 优势：通过某个条件渲染组件，降低组件渲染频率，提升组件性能
- 说明：如果返回值为`false`，那么，后续`render()`方法不会被调用
- 注意：**这个方法必须返回布尔值！！！**
- 场景：根据随机数决定是否渲染组件

```javascript
// - 参数：
//   - 第一个参数：最新属性对象
//   - 第二个参数：最新状态对象
shouldComponentUpdate(nextProps, nextState) {
  console.warn('shouldComponentUpdate', nextProps, nextState)

  return nextState.count % 2 === 0
}
```



3. **componentWillUpdate**

- 作用：组件将要更新
- 参数：最新的属性和状态对象
- 注意：不能修改状态 否则会循环渲染

```javascript
componentWillUpdate(nextProps, nextState) {
  console.warn('componentWillUpdate', nextProps, nextState)
}
```

4. **render** 

- 作用：重新渲染组件，与`Mounting`阶段的`render`是同一个函数
- 注意：这个函数能够执行多次，只要组件的属性或状态改变了，这个方法就会重新执行

5. **componentDidUpdate**

- 作用：组件已经被更新
- 参数：旧的属性和状态对象

```javascript
componentDidUpdate(prevProps, prevState) {
  console.warn('componentDidUpdate', prevProps, prevState)
}
```

#### 卸载阶段

- 组件销毁阶段：组件卸载期间，函数比较单一，只有一个函数，这个函数也有一个显著的特点：组件一辈子只能执行一次！
- 使用说明：只要组件不再被渲染到页面中，那么这个方法就会被调用（ 渲染到页面中 -> 不再渲染到页面中 ）

1. **componentWillUnmount**

作用：在卸载组件的时候，执行清理工作，比如

- 1 清除定时器
- 2 清除`componentDidMount`创建的DOM对象

#### state 和 setState

- 注意：使用 `setState()` 方法修改状态，状态改变后，React会重新渲染组件
- 注意：不要直接修改state属性的值，这样不会重新渲染组件！！！
- 使用：1 初始化state 2 setState修改state

```javascript
// 修改state（不推荐使用）
this.state.test = '这样方式，不会重新渲染组件';
```

```javascript
constructor(props) {
  super(props)

  // 正确姿势！！！
  // -------------- 初始化 state --------------
  this.state = {
    count: props.initCount
  }
}

componentWillMount() {
  // -------------- 修改 state 的值 --------------
  // 方式一：
  this.setState({
    count: this.state.count + 1
  })

  this.setState({
    count: this.state.count + 1
  }, function(){
    // 由于 setState() 是异步操作，所以，如果想立即获取修改后的state
    // 需要在回调函数中获取
    // https://doc.react-china.org/docs/react-component.html#setstate
  });

  // 方式二：
  this.setState(function(prevState, props) {
    return {
      counter: prevState.counter + props.increment
    }
  })

  // 或者 - 注意： => 后面需要带有小括号，因为返回的是一个对象
  this.setState((prevState, props) => ({
    counter: prevState.counter + props.increment
  }))
}
```

