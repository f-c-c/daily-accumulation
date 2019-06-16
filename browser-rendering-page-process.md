# 浏览器的工作原理

在地址栏输入 `google.com` 直到您在浏览器屏幕上看到 Google 首页的整个过程中都发生了些什么?

目前使用的主流浏览器有五个：Internet Explorer、Firefox、Safari、Chrome 浏览器和 Opera。不同的浏览器内核，渲染过程不太一样

### 浏览器的主要功能

就是**向服务器发出请求**，在浏览器窗口中**展示您选择的网络资源**。资源一般是指 HTML 文档，也可以是 PDF、图片或其他的类型。资源的位置由用户使用 URI（统一资源标示符）指定

浏览器解释并显示 HTML 文件的方式是在 HTML 和 CSS 规范中指定的。这些规范由网络标准化组织 W3C（万维网联盟）进行维护。 

浏览器的用户界面并没有任何正式的规范，这是多年来的最佳实践自然发展以及彼此之间相互模仿的结果:

- 用来输入 URI 的地址栏
- 前进和后退按钮
- 书签设置选项
- 用于刷新和停止加载当前文档的刷新和停止按钮
- 用于返回主页的主页按钮

### 浏览器的主要组件

1. **用户界面** - 包括地址栏、前进/后退按钮、书签菜单等。除了浏览器主窗口显示的您请求的页面外，其他显示的各个部分都属于用户界面。
2. **浏览器引擎** - 在用户界面和呈现引擎之间传送指令。
3. **呈现引擎** - 呈现引擎的作用嘛...当然就是“呈现”了，也就是在浏览器的屏幕上显示请求的内容。如果请求的内容是 HTML，它就负责解析 HTML 和 CSS 内容，并将解析后的内容显示在屏幕上。Firefox 使用的是 **Gecko**，这是 Mozilla 公司“自制”的呈现引擎。而 Safari 和 Chrome 浏览器使用的都是 **WebKit**。
4. **网络** - 用于网络调用，比如 HTTP 请求。其接口与平台无关，并为所有平台提供底层实现。
5. **用户界面后端** - 用于绘制基本的窗口小部件，比如组合框和窗口。其公开了与平台无关的通用接口，而在底层使用操作系统的用户界面方法。
6. **JavaScript 解释器**。用于解析和执行 JavaScript 代码。
7. **数据存储**。这是持久层。浏览器需要在硬盘上保存各种数据，例如 Cookie。新的 HTML 规范 (HTML5) 定义了“网络数据库”，这是一个完整（但是轻便）的浏览器内数据库。

![browser-component](./assert/browser-component.png)

值得注意的是，和大多数浏览器不同，**Chrome 浏览器的每个标签页都分别对应一个呈现引擎实例**。每个标签页都是一个独立的进程。

webkit 内核渲染流程：

![webkit-process](./assert/webkit-process.png)



Mozilla 的 Gecko 呈现引擎主流程:

![gecko-process](./assert/gecko-process.png)

虽然主流浏览器渲染过程叫法有区别，但是主要流程还是相同的

Gecko 将视觉格式化元素组成的树称为“框架树”。每个元素都是一个框架。WebKit 使用的术语是“呈现树”，它由“呈现对象”组成。对于元素的放置，WebKit 使用的术语是**“布局”**，而 Gecko 称之为**“重排”**。

呈现引擎将开始解析 HTML 文档，并将各标记逐个转化成“DOM Tree”上的 [DOM](https://www.html5rocks.com/zh/tutorials/internals/howbrowserswork/#DOM) 节点。同时也会解析外部 CSS 文件以及样式元素中的样式数据。HTML 中这些带有视觉指令的样式信息将用于创建另一个树结构：[呈现树](https://www.html5rocks.com/zh/tutorials/internals/howbrowserswork/#Render_tree_construction)。

呈现树包含多个带有视觉属性（如颜色和尺寸）的矩形。这些矩形的排列顺序就是它们将在屏幕上显示的顺序。

呈现树构建完毕之后，进入“[布局](https://www.html5rocks.com/zh/tutorials/internals/howbrowserswork/#layout)”处理阶段，也就是为每个节点分配一个应出现在屏幕上的确切坐标。下一个阶段是[绘制](https://www.html5rocks.com/zh/tutorials/internals/howbrowserswork/#Painting) - 呈现引擎会遍历呈现树，由用户界面后端层将每个节点绘制出来。

需要着重指出的是，这是一个**渐进的过程**。为达到更好的用户体验，呈现引擎会力求尽快将内容显示在屏幕上。它不必等到整个 HTML 文档解析完毕之后，就会开始构建呈现树和设置布局。在不断接收和处理来自网络的其余内容的同时，呈现引擎会将部分内容解析并显示出来。

故基本流程如下：

* 1. HTML解析出**DOM Tree**
* 2. CSS解析出**Style Rules**
* 3. 将二者关联生成**Render Tree**
* 4. **Layout** 根据Render Tree计算每个节点的信息（重排）
* 5. **Painting** 根据计算好的信息绘制整个页面（重绘）

### HTML解析 DOM Tree

HTML Parser的任务是将HTML标记解析成DOM Tree, 可以参考React解析DOM的过程