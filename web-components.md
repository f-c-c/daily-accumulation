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