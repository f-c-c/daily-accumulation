# Web Components

`document.querySelector` 对于` jquery`

document.querySelector第一次获得浏览器的广泛支持，终结了jQuery一统天下的局面，同样的情况也可能会发生在前端框架上，比如Angular和React

有了这些框架，我们就能完成一些一直想做但一直没办法实现的事情——**创建可重用的自动化前端组件**。然而，这些框架会增加复杂性，增加专有的语法，还会增大负担

一切终将改变 x-tag **自定义组件**

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

如果需要在HTML属性（attribute）发生变化时执行一些动作，那么可以将其加入到observedAttributes数组中。为了保证性能，只有加入到这个数组中的属性（attribute）才会被监视。当HTML属性（attribute）的值发生变化时，attributeChangedCallback就会被调用，同时传入HTML属性的名称、当前值和新值：

```javascript
class MyElement extends HTMLElement {  
  static get observedAttributes() {    
    return ['disabled'];  
  }
 
  constructor() {    
    const shadowRoot = this.attachShadow({mode: 'open'});
    shadowRoot.innerHTML = `      
      <style>        
        .disabled {          
          opacity: 0.4;        
        }      
      </style>      
 
      <div id="container"></div>    
    `;
 
    this.container = this.shadowRoot('#container');  
  }
 
  attributeChangedCallback(attr, oldVal, newVal) {    
    if(attr === 'disabled') {      
      if(this.disabled) {        
        this.container.classList.add('disabled');      
      }      
      else {        
        this.container.classList.remove('disabled')      
      }    
    }
  }
}
```

这样，每当disabled属性（attribute）改变，this.container（即元素的影子DOM中的div元素）上的“disabled”就会随之改变。

### Shadow DOM（影子DOM）：

一组JavaScript API，用于将封装的“影子”DOM树附加到元素（与主文档DOM分开呈现）并控制其关联的功能。通过这种方式，您可以保持元素的功能私有，这样它们就可以被脚本化和样式化，而不用担心与文档的其他部分发生冲突。  **dom沙箱环境**  dom 被隔离了

使用影子DOM，自定义元素的HTML和CSS可以完全封装在组件内部。这意味着在文档的DOM树中，元素会显示为单一的HTML标签，其实际内部HTML结构会出现在#shadow-root中。

实际上，好几个原生HTML元素也在使用影子DOM。例如，如果在网页上放置一个<video>元素，它会显示为单一的标签，但同时显示的播放、暂停按钮等在开发者工具中查看<video>元素时是看不到的。

这些元素实际上是<video>元素的影子DOM的一部分，因此默认是隐藏的。要在Chrome中显示影子DOM，可以在“偏好设置”中的开发者工具中找到设置，勾选“显示用户代理的影子DOM”。在开发者工具中重新检查<video>元素，就能看到元素的影子DOM。

影子DOM还支持真正的CSS范围（scope）。所有定义在组件内部的CSS只对组件本身有效。元素仅从组件外部定义的CSS中继承最小量的属性，甚至，连这些属性都可以配置为不继承。但是，你可以暴露一些CSS属性，允许组件的使用者给组件添加样式。这种机制解决了现有的CSS的许多问题，同时依然支持自定义组件的样式。

 定义影子root的方式如下：

```javascript
const shadowRoot = this.attachShadow({mode: 'open'});
shadowRoot.innerHTML = `<p>Hello world</p>`;
```

这段代码在定义影子root时使用了mode: 'open'，其含义是它可以通过开发者工具进行查看和操作，可以查询，也可以配置任何暴露的CSS属性，也可以监听它抛出的事件。影子root的另一个模式是mode: 'closed'，但这个选项不推荐使用，因为使用者将无法与组件进行人和交互，甚至都不能监听其抛出的事件。

要给影子root添加HTML，可以将HTML字符串赋值给影子root的innerHTML属性，也可以使用<template>元素。HTML模板基本上是一段HTML片段，供以后使用。在插入到DOM树中之前，它不可见，也不会被解析，也就是说其内部定义的任何外部资源都不会被下载，任何CSS和JavaScript在插入到DOM之前也不会被解析。例如，你可以定义多个<template>元素，当组件的HTML根据组件状态而发生变化时，将相应的模板插入到DOM中。这样就可以很容易地改变组件的大量HTML，而不需要逐个处理DOM结点。

 

创建影子root之后，就可以在上面使用所有DOM的方法，就像平常处理document对象那样，如使用this.shadowRoot.querySelector来查找元素。组件的所有CSS都可以定义在<style>标签中，但也可以通过通常的<link rel="stylesheet">来读取外部样式表。除了一般的CSS之外，还可以使用:host选择器给组件自己定义样式。例如，自定义元素默认使用display: inline，使用下面的CSS可以将其定义为块元素：

```
:host {
  display: block;
}
```

这还可以实现上下文样式。例如，如果想在组件定义了disabled属性时灰掉，可以这样做：

```
:host([disabled]) {
  opacity: 0.5;
}
```

 默认情况下，自定义元素会从周围的CSS继承一些属性，如color、font等。但是如果你希望从全新的状态开始，使组件的所有CSS属性重置到默认值，可以这样做：

```
:host {
  all: initial;
}
```

 有一点很重要：外部定义在组件上的样式的优先级要高于在影子DOM中使用:host定义的样式。因此，如果定义了：

```
my-element {
  display: inline-block;
}
```

它将会覆盖：

```
:host {
  display: block;
}
```

外部不可能给自定义元素内部的任何元素定义样式。但如果希望使用者能够给组件（中的部分元素）定义样式，那么可以通过暴露CSS变量来实现。例如，如果希望使用者能选择组件的背景颜色，那么可以暴露名为--background-color的CSS变量。

假设组件的影子DOM的根节点的元素为<div id="container">：

```
#container {
  background-color: var(--background-color);
}
```

 那么，组件的使用者可以从外部定义其背景色：

```
my-element {
  --background-color: #ff0000;
}
```

组件内部应该为其定义默认值，以备使用者不定义背景色的情况：

```
:host {
  --background-color: #ffffff;
}
#container {
  background-color: var(--background-color);
}
```

当然，CSS变量的名字可以任意选择，唯一的要求是必须以“--”开始。

通过对CSS和HTML范围（scope）的支持，影子DOM解决了CSS的全局性带来的问题——会导致巨大的、只能添加的样式表，其中的选择器的规则越来越具体，充满了各种覆盖。影子DOM使得开发者可以将标记语言和样式打包到组件内部，而不需要任何工具或命名规则。这样就不用担心新的class或id会与已有的冲突。

除了能够通过CSS变量给Web组件内部设置样式之外，还可以给Web组件注入HTML。

**通过slot进行组合**

组合就是将影子DOM树与使用者提供的标记语言组合在一起。<slot>元素可以实现这一过程，可以认为它是影子DOM中的一个占位符，使用者提供的标记语言将在此处渲染。使用者提供的标记语言称为“轻量DOM”（light DOM）。组合过程将轻量DOM和影子DOM结合在一起，形成新的DOM树。

例如，你可以创建一个<image-gallery>组件，使用该组件时，提供两个标准的<img>标签供组件渲染用：

```
<image-gallery>
  <img src="foo.jpg" slot="image">
  <img src="bar.jpg" slot="image">
</image-gallery>
```

该组件将接受两个图像，并在组件的影子DOM内部渲染。注意图像上的slot="image"属性。该属性告诉组件图像在影子DOM中渲染的位置。影子DOM的样子可能如下：

```
<div id="container">
  <div class="images">
    <slot name="image"></slot>
  </div>
</div>
```

当轻量DOM中的元素被分配到元素的影子DOM中后，得到的DOM树如下所示：

```
<div id="container">
  <div class="images">
    <slot name="image">
      <img src="foo.jpg" slot="image">
      <img src="bar.jpg" slot="image">
    </slot>
  </div>
</div>
```

 可见，用户提供的带有slot属性的元素将被渲染到slot元素内部，slot元素的name属性值必须匹配相应的slot属性的值。

`<select>`元素就使用了这种方式，你可以在Chrome的开发者工具中查看（如果你勾选了“显示用户代理的影子DOM”选项，如上文所示）：

它接受用户提供的<option>元素，将其渲染成下拉菜单。

带有name属性的slot元素称为命名slot，但该属性并不是必须的。name属性只是用来将内容渲染到特定的位置。如果一个或多个slot没有name属性，内容将会按照使用者提供的顺序进行渲染。如果使用者提供的内容少于slot的个数，slot还可以提供默认内容。

假设<image-gallery>的影子DOM如下所示：

```
<div id="container">
  <div class="images">
    <slot></slot>
    <slot></slot>
    <slot>
      <strong>No image here!</strong> <-- fallback content -->
    </slot>
  </div>
</div>
```

提供上文中的两个图像时，产生的DOM树如下：

```
<div id="container">
  <div class="images">
    <slot>
      <img src="foo.jpg">
    </slot>
    <slot>
      <img src="bar.jpg">
    </slot>
    <slot>
     <strong>No image here!</strong>
    </slot>
  </div>
</div>
```

影子DOM内部通过slot渲染的元素称为分配结点。这些结点的样式会在渲染到组件内部的影子DOM（即“分配”）后依然有效。在影子DOM内部，分配结点还可以通过::slotted()选择器获得额外的样式：

```
::slotted(img) {
  float: left;
}
```

::slotted()可以接受任何有效的CSS选择器，但只能选择顶层结点。例如，::slot(section img)在这种情况下无法使用：

```
<image-gallery>
  <section slot="image">
    <img src="foo.jpg">
  </section>
</image-gallery>
```

**用JavaScript处理slot**

JavaScript也可以处理slot，可以查看某个slot被分配了什么结点，查看某个元素被分配到了哪个slot，还可以使用slotchange事件。

调用slot.assignedNodes()可以访问slot分配到的结点。如果想获取任何默认内容，可以调用slot.assignedNodes({flatten: true})。

查看element被分配到的slot，可以访问element.assignedSlot。

每当slot内部的结点发生变化（结点被添加或删除）时会产生slotChange事件。注意该事件仅在slot结点本身上触发，而不会在slot结点的子元素上触发

```
slot.addEventListener('slotchange', e => {
  const changedSlot = e.target;
  console.log(changedSlot.assignedNodes());
});
```

 Chrome会在元素首次初始化时触发slotchange事件，而Safari和Firefox在此情况下不会。

**影子DOM中的事件**

自定义元素产生的标准事件（如鼠标和键盘事件等）默认情况下会从影子DOM中冒泡出来。如果事件从影子DOM内部的结点产生，那么它的目标会被重新设置，使之看起来像是从自定义元素本身产生的。如果想知道事件到底产生于影子DOM中的哪个元素，可以调用event.composedPath()来获取该事件经过的一系列结点。但是，事件的target属性永远指向自定义元素本身。

从自定义元素中可以通过CustomEvent抛出任何事件。

```
class MyElement extends HTMLElement {
  ...
  connectedCallback() {
    this.dispatchEvent(new CustomEvent('custom', {
      detail: {message: 'a custom event'}
    }));
  }
}
// on the outside
document.querySelector('my-element').addEventListener('custom', e => console.log('message from event:', e.detail.message));
```

 但是，任何影子DOM内部的结点抛出的事件则不会冒泡到影子DOM外面，除非它是使用composed: true创建的：

```
class MyElement extends HTMLElement {
  ...
  connectedCallback() {
    this.container = this.shadowRoot.querySelector('#container');
    // dispatchEvent is now called on this.container instead of this
    this.container.dispatchEvent(new CustomEvent('custom', {
      detail: {message: 'a custom event'},
      composed: true  // without composed: true this event will not bubble out of Shadow DOM
    }));
  }
}
```

### **HTML templates（HTML模板）：**

除了使用this.shadowRoot.innerHTML给影子root中的元素添加HTML之外，还可以使用<template>来实现这一点。模板用来提供一小段代码供以后使用。模板中的代码不会被渲染，初始化时它的内容会被解析，但仅仅用来保证其内容是正确的。模板内部的JavaScript不会被执行，任何外部资源也不会被获取。默认情况下它是隐藏的。

如果Web组件需要根据不同的情况渲染完全不同的标记，那么可以使用不同的模板来实现这一点：

```
class MyElement extends HTMLElement {
  ...
 
  constructor() {
    const shadowRoot = this.attachShadow({mode: 'open'});
 
    this.shadowRoot.innerHTML = `
      <template id="view1">
        <p>This is view 1</p>
      </template>
 
      <template id="view1">
        <p>This is view 1</p>
      </template>
 
      <div id="container">
        <p>This is the container</p>
      </div>
    `;
  }
 
  connectedCallback() {
    const content = this.shadowRoot.querySelector('#view1').content.clondeNode(true);
    this.container = this.shadowRoot.querySelector('#container');
 
    this.container.appendChild(content);
  }
}
```

这里两个模板都通过innerHTML放到了影子root内。一开始时两个模板都是隐藏的，只有容器被渲染。在connectedCallback内我们调用this.shadowRoot.querySelector('#view1').content.cloneNode(true)获取了#view1的内容。模板的content属性返回的模板内容为DocumentFragment实例，该实例可以通过appendChild添加到另一个元素中。由于appendChild在元素已存在于DOM中的情况下会移动元素，所以我们首先需要使用cloneNode(true)来复制它。否则，模板的内容将会被移动而不会被添加，意味着我们只能使用其内容一次。

 

模板在需要快速改变一大片HTML或重用HTML的情况下非常有用。模板也不限于Web组件，可以用在DOM中的任何地方。

**扩展原生元素**

到目前为止，我们一直在扩展HTMLElement来创建全新的HTML元素。自定义元素还可以用来扩展内置的原生元素，从而实现对图像、按钮等已有HTML元素的增强。在撰写本文时，该功能仅Chrome和Firefox支持。

扩展已有HTML元素的好处是，它能继承所有的属性和方法。这样就可以渐进式增强已有元素，因此即使浏览器不支持自定义元素，该元素也是可用的，它只不过是采用默认的内置行为。而如果撰写全新的HTML标记，在不支持自定义元素的浏览器中就完全无法使用了。

举个例子，假设我们要增强HTML的<button>元素：

```
class MyButton extends HTMLButtonElement {
  ...
 
  constructor() {
    super();  // always call super() to run the parent's constructor as well
  }
 
  connectedCallback() {
    ...
  }
 
  someMethod() {
    ...
  }
}
 
customElements.define('my-button', MyButton, {extends: 'button'});
```
下面的代码已经和 vue 什么的很像了，浏览器原生就支持这些东西，只是当时支持程度问题让 3 大框架钻了空子
```javascript
  <script>
        window.addEventListener('fcc', function(e) {
            alert(e.detail.info);
        })
        class ButtonHelloElement extends HTMLButtonElement {
            constructor() {
                super();
                this.addEventListener('click', () => {
                    window.dispatchEvent(new CustomEvent('fcc', {
                        detail: {
                            info: 'hahaha'
                        }
                    }))
                })
            }
        }
        customElements.define('button-hello', ButtonHelloElement ,{
            extends: 'button'
        })
    </script>
    <button is="button-hello">hello world</button>
```



这里的Web组件没有扩展更通用的HTMLElement，而是扩展了HTMLButtonElement。现在调用customElements.define时还带了另一个参数{extends: 'button'}，来指明我们的类扩展了<button>元素。这看起来有点多余，因为我们已经指明过要扩展HTMLButtonElement了，但这是必要的，因为有可能有其他元素使用了同一个DOM接口。例如，<q>和<blockquote>都使用同一个HTMLQuoteElement接口。

增强后的按钮可以使用is属性了：

```
<button is="my-button">
```

### **HTML Imports（HTML导入）：**

**在 Google Chrome 73 后已过时**
此功能已过时。虽然它可能仍然适用于某些浏览器，但不鼓励使用它，因为它随时可能被删除。尽量避免使用它。

原译文地址：`https://blog.csdn.net/github_38885296/article/details/89432919`

