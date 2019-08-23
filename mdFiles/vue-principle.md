* 首先代码里面可以 new Vue(), 这说明了有一个 Vue 构造函数
```
    var vm = new Vue({
      el: 'app',
      data: {
        text: 'hello world'
      }
    });
```
* 构造函数如下：可以看到，我们将data挂在了 vue 的实例上了，并调用了 observe 函数和 Compile函数
```
function Vue(options) {
  this.data = options.data;
  var data = this.data;
  observe(data, this); //
  var id = options.el;
  var dom = new Compile(document.getElementById(id), this);
  // 编译完成后，将dom返回到app中
  document.getElementById(id).appendChild(dom);
}
```
* observe 函数：在这里我们将data里面的每一个key挂在了 vue 实例上（通过defineProperty进行数据拦截get set）
* data里面的每一个key对应一个  new Dep() 观察者对象
```
    function defineReactive(vm, key, val) {
      var dep = new Dep();
      Object.defineProperty(vm, key, {
        get: function () {
          if (Dep.target) {
            // JS的浏览器单线程特性，保证这个全局变量在同一时间内，只会有同一个监听器使用
            dep.addSub(Dep.target);
          }
          return val;
        },
        set: function (newVal) {
          if (newVal === val) return;
          val = newVal;
          // 作为发布者发出通知
          dep.notify();
        }
      })
    }

    function observe(obj, vm) {
      Object.keys(obj).forEach(function (key) {
        defineReactive(vm, key, obj[key]);
      })
    }
```
* Dep就是一个简单的观察者模式：
```
function Dep() {
  this.subs = [];
}
Dep.prototype = {
  addSub: function (sub) {
    this.subs.push(sub);
  },
  notify: function () {
    this.subs.forEach(function (sub) {
      sub.update();
    })
  }
}
```
* Compile 函数：在这里，我们递归遍历壳子dom的子元素，针对不同元素类型进行处理（nodeType）
* 比如找到所有的文本节点： {{text}} ，每一个匹配上 key 的文本节点对应一个 watcher
```
function Compile(node, vm) {
  if (node) {
    this.$frag = this.nodeToFragment(node, vm);
    return this.$frag;
  }
}
Compile.prototype = {
  nodeToFragment: function (node, vm) {
    var self = this;
    var frag = document.createDocumentFragment();
    var child;

    while (child = node.firstChild) {
      self.compileElement(child, vm);
      frag.append(child); // 将所有子节点添加到fragment中
    }
    return frag;
  },
  compileElement: function (node, vm) {
    var reg = /\{\{(.*)\}\}/;
    // console.log('11111', node.nodeValue);
    //节点类型为元素
    if (node.nodeType === 1) {
      var attr = node.attributes;
      // 解析属性
      for (var i = 0; i < attr.length; i++) {
        if (attr[i].nodeName == 'v-model') {
          var name = attr[i].nodeValue; // 获取v-model绑定的属性名
          node.addEventListener('input', function (e) {
            // 给相应的data属性赋值，进而触发该属性的set方法
            //再批处理 渲染元素
            vm[name] = e.target.value;
          });
          // node.value = vm[name]; // 将data的值赋给该node
          new Watcher(vm, node, name, 'value');
        }
      };
    }
    //节点类型为text
    if (node.nodeType === 3) {
      if (reg.test(node.nodeValue)) {
        console.log('22222', node.nodeValue);
        var name = RegExp.$1; // 获取匹配到的字符串
        name = name.trim();
        // node.nodeValue = vm[name]; // 将data的值赋给该node
        new Watcher(vm, node, name, 'nodeValue');
      }
    }
  },
}
```
* Watcher 每一个watcher有一个唯一的 id，vue源码就是这样做的（全局uid ++ ）6
* 把每一个匹配上 key 的文本节点对应的 watcher 加入批处理的队列queue
```
let uid = 0;
function Watcher(vm, node, name, type) {
    //new Watcher(vm, node, name, 'nodeValue');
    Dep.target = this;
    this.name = name; //text
    this.id = ++uid;
    this.node = node; //当前的节点
    this.vm = vm; //vm 
    this.type = type; //nodeValue
    this.update();
    Dep.target = null;
}

// function queueWatcher(watcher){
//     var id = watcher.id;
//     if(has[id]==null){

//     }
// }
Watcher.prototype = {
    update: function () {
        this.get();
        if(!batcher){
            batcher = new Batcher();
            // console.log(this.node);
            // this.node[this.type] = this.value;
        }
        batcher.push(this);
        //this.node[this.type] = this.value; // 订阅者执行相应操作
    },
    cb: function () {
        //最终实际虚拟dom处理的结果 只处理一次
        console.log("dom update");
        this.node[this.type] = this.value; // 订阅者执行相应操作
    },
    // 获取data的属性值
    get: function () {
        this.value = this.vm[this.name]; //触发相应属性的get
    }
}
```
* 批处理 Batcher 这里来活了，这里将注册一个 micoTask, 
* 在编译的过程中，将会不断的有 watcher 被加入到批处理队列中
* 当dom编译结束，继续后续的同步代码-修改vue实例的属性-触发set-触发观察者的notify-触发对应watcher的update-触发get（这里watcher将达到新值）-加入批处理-》等待⌛️同步代码（主线程修改数据的代码执行完毕）-走批处理注册的微任务-遍历队列执行每一个watcher的cb（修改dom）
* 批处理的存在保证了同步修改同一变量-只会渲染一次（以最后一次结果为准）
* 对于input 元素（我们在编译的时候会绑定 input 事件）-》当用户输入时相当于触发vue实例 属性 的set操作-》拿到新值-》dep 发布通知notify-》所有被观察者watcher执行update，后续过程类似
* 关键是data里面每一个属性对于一个 观察者类型，当这个属性改变时，发布通知改变所有同类型的值 
```
/**
 * 批处理构造函数
 * @constructor
 */
function Batcher() {
    this.reset();
}

/**
 * 批处理重置
 */
Batcher.prototype.reset = function () {
    this.has = {};
    this.queue = [];
    this.waiting = false;
};

/**
 * 将事件添加到队列中
 * @param job {Watcher} watcher事件
 */
Batcher.prototype.push = function (job) {
    let id = job.id;
    if (!this.has[id]) {
        console.log(batcher);
        this.queue.push(job);
        //设置元素的ID
        this.has[id] = true;
        if (!this.waiting) {
            this.waiting = true;
            if ("Promise" in window) {
                Promise.resolve().then( ()=> {
                    this.flush();
                })
            } else {
                setTimeout(() => {
                    this.flush();
                }, 0);
            }
        }
    }
};

/**
 * 执行并清空事件队列
 */
Batcher.prototype.flush = function () {
    this.queue.forEach((job) => {
        job.cb();
    });
    this.reset();
};
```