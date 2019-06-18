# Web Components

`document.querySelector` 对于` jquery`

document.querySelector第一次获得浏览器的广泛支持，终结了jQuery一统天下的局面，同样的情况也可能会发生在前端框架上，比如Angular和React

有了这些框架，我们就能完成一些一直想做但一直没办法实现的事情——**创建可重用的自动化前端组件**。然而，这些框架会增加复杂性，增加专有的语法，还会增大负担

一切终将改变

在现代Web API的发展下，创建可重用的前端组件终于不再需要框架了。有了自定义元素和影子DOM，我们就可以创建能够随意复用的组件

Web组件（Web Component）的概念最初于2011年提出，组件包括一系列功能，可以仅通过HTML、CSS和JavaScript就能创建可重用的组件。也就是说，创建组件不需要再使用React或Angular之类的框架。更妙的是，这些组件还能够无缝地集成到这些框架中

有史以来我们第一次能够仅通过HTML、CSS和JavaScript创建组件并在任何现代浏览器上运行。现在，最新版本的Chrome、Safari、Firefox和Opera桌面版，以及Safari的iOS版、Chrome的Android版都支持Web组件。可以去 [https://caniuse.com](https://caniuse.com/) 查询其支持情况：也就是说，现在几乎能在任何浏览器（包括移动浏览器）上使用Web组件。

它由**四项主要技术**组成，它们可以一起使用来创建封装功能的定制元素，可以在你喜欢的任何地方重用，不必担心代码冲突。

### **Custom elements（自定义元素）：**

> 就是用户自定义的HTML元素，可以使用CustomElementRegistry定义自定义元素。如果你想注册新的元素，只需通过window.customElements获得registry的实例，然后调用其define方法：

```javascript
window.customElements.define('my-element', MyElement);
```

define方法的第一个参数是要创建的新元素的标签名称。接下来，你只需要下面的代码就可以使用该元素：

```javascript
<my-element></my-element> 
```

名称中的横线（-）是必须的，这是为了避免与原生HTML元素的命名冲突。

MyElement构造函数必须是ES6类，然而很不幸的是，由于Javascript类不同于传统的OOP语言的类，这很容易造成混乱。而且，因为这里可以使用Object，所以Proxy也是可行的，这样就能在自定义元素上实现简单的数据绑定。但是，如果想实现对原生HTML元素的扩展，这个限制是必须的，这样才能保证你的元素能够继承整个DOM API。

下面我们来为自定义元素写一个类：

```javascript
class MyElement extends HTMLElement {
 constructor() {
    super();
  }
  connectedCallback() {
    // here the element has been inserted into the DOM
  }
}
```

我们自定义元素的类只是普通的JavaScript类，它扩展了原生的HTMLElement。除了构造函数之外，它还有个方法叫做connectedCallback，当元素被插入到DOM树之后该方法会被调用。你可以认为它相当于React的componentDidMount方法。

一般来说，组件的设置应当尽可能低推迟到connectdedCallback中进行，因为这是唯一一个能够确保所有属性和子元素都存在的地方。一般来说，构造函数应该仅初始化状态，以及设置影子DOM（Shadow DOM）。

元素的constructor和connectedCallback的区别在于，constructor在元素被创建时调用（例如通过调用document.createElement创建），而connectedCallback是在元素真正被插入到DOM中时调用，例如当元素所在的文档被解析时，或者通过document.body.appendChild添加元素时。

你也可以通过customElements.get('my-element')来获取自定义元素的构造函数的引用，通过该方法来创建元素，假设该元素已经通过customElements.define()注册过了的话。然后可以通过new element()而不是document.createElement()来初始化元素：

```javascript
customElements.define('my-element', class extends HTMLElement {...});
...
const el = customElements.get('my-element');
const myElement = new el();  // same as document.createElement('my-element');
document.body.appendChild(myElement);
```

与connectedCallback相对的就是disconnectedCallback，当元素从DOM中移除时会调用该方法。在这个方法中可以进行必要的清理工作，但要记住这个方法不一定会被调用，比如用户关闭浏览器或关闭浏览器标签页的时候。

还有个adoptedCallback方法，当通过document.adoptNode(element)将元素收养至文档中时会调用该方法。到目前为止，我从来没遇到过需要使用该回调函数的情况。

另一个常用的生命周期方法是attributeChangedCallback。当属性被添加到observedAttributes数组时该方法会被调用。该方法调用时的参数为属性的名称、属性的旧值和新值：

```javascript
class MyElement extends HTMLElement {
  static get observedAttributes() {
    return ['foo', 'bar'];
  }
 
  attributeChangedCallback(attr, oldVal, newVal) {
    switch(attr) {
      case 'foo':
        // do something with 'foo' attribute
 
      case 'bar':
        // do something with 'bar' attribute
 
    }
  }
}
```

该回调函数仅在属性存在于observedAttributes数组中时才会被调用，在上例中为foo和bar。任何其他属性的变化不会调用该回调函数。

属性主要用于定义元素的初始配置和初始状态。理论上通过序列化的方式给属性传递复杂的值，但这会对性能造成很大影响，而且由于你能够访问组件的方法，所以这样做是没有必要的。但如果确实希望像React、Angular等框架提供的功能那样，在属性上实现数据绑定，可以看看Ploymer（https://polymer-library.polymer-project.org/）。

**生命周期方法的顺序**

 生命周期方法的执行顺序为：

```
constructor -> attributeChangedCallback -> connectedCallback
```

为什么attributeChangedCallback会在connectedCallback之前被调用？

回忆一下，Web组件的属性的主要目的是初始化配置。也就是说，当组件被插入到DOM中时，配置应当已经被初始化过了，所以attributeChangedCallback应当在connectedCallback之前被调用。

也就是说，如果想根据特定属性的值，在影子DOM中配置任何结点，那就需要在constructor中引用属性，而不能在connectedCallback中进行。

例如，如果组件中有个id="container"，而你需要在属性disabled发生改变时，将这个元素设置为灰色背景，那么需要在constructor中引用该属性，这样它才能出现在attributeChangedCallback中：

```javascript

constructor() {
  this.container = this.shadowRoot.querySelector('#container');
}
 
attributeChangedCallback(attr, oldVal, newVal) {
  if(attr === 'disabled') {
    if(this.hasAttribute('disabled') {
      this.container.style.background = '#808080';
    }
    else {
      this.container.style.background = '#ffffff';
    }
  }
}
```

如果不得不等到connectedCallback中才能创建this.container，那么可能在第一次attributeChangedCallback被调用时，this.container不存在。所以，尽管你应当尽量将组件的设置推迟到connectedCallback中进行，但这是个例外情况。

另一点很重要的是，要意识到你可以在通过customElements.define()注册Web组件之前就使用它。当元素存在于DOM中，或者被插入到DOM中时，如果它还没有被注册，那么它将成为HTMLUnknownElement的实例。浏览器会对于任何它不认识的HTML元素的处理方法是，你依然可以像使用其他元素那样使用它，只是它没有任何方法，也没有默认的样式。

在通过customElements.define()注册之后，该元素就会通过类定义得到增强。该过程称为“升级”（upgrading）。可以在元素被升级时通过customElements.whenDefined调用一个回调函数，该方法返回一个Promise，在元素被升级时该Promise得到解决：

````javascript
customElements.whenDefined('my-element')
.then(() => {
  // my-element is now defined
})
````

**Web组件的公共API**

除了生命周期方法之外，你还可以在元素上定义方法，这些方法可以从外部调用。这个功能是React和Angular等框架无法实现的。例如，你可以定义一个名为doSomething的方法：

```javascript
class MyElement extends HTMLElement {
  ...
 
  doSomething() {
    // do something in this method
  }
}
```

然后在组件外部像这样调用它：

```javascript
const element = document.querySelector('my-element');
element.doSomething();
```

任何在元素上定义的属性都会成为它的公开JavaScript API的一部分。这样，只需给元素的属性提供setter，就可以实现数据绑定，从而实现类似于在元素的HTML里渲染属性值等功能。因为原生的HTML属性（attribute）值仅支持字符串，因此对象等复杂的值应该作为自定义元素的属性（properties）。

除了定义Web组件的初始状态之外，HTML属性（attribute）还用来反映相应的组件属性（property）的值，因此元素的JavaScript状态可以反映到其DOM表示中。下面的例子演示了input元素的disabled属性：

```javascript
<input name="name">
 
const input = document.querySelector('input');
input.disabled = true;
```

在将input的disabled属性（property）设置为true后，这个改动会反映到相应的disabled HTML属性（attribute）中：

```html
<input name="name" disabled>
```

用setter可以很容易实现从属性（property）到HTML属性（attribute）的映射：

```javascript
class MyElement extends HTMLElement {
  ...
 
  set disabled(isDisabled) {
    if(isDisabled) {
      this.setAttribute('disabled', '');
    }
    else {
      this.removeAttribute('disabled');
    }
  }
 
  get disabled() {
    return this.hasAttribute('disabled');
  }
}
```

