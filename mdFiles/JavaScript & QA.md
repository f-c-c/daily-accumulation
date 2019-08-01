## 2018.11.07

### JavaScript & QA测试工程师

#### 单元测试

- 全局安装 karma `npm install -g karma` 以及 `npm install -g karma-cli`

- 接着配置 karma 的配置文件 `karma init my.conf.js`

  - 在这里可以通过 上下箭头选择断言库比如：
    - Jasmine mocha qunit nodeunit nunit
  - 这里我选择 jasmine ，接着`Do you want to use Require.js ?`选择 `no`
  - 接着是选择浏览器(无头浏览器，就是没有GUI界面的浏览器)
    - Chrome ChromeHeadless ChromeCanary Firefox Safari PhantomJS Opera IE
  - 这里我们选择 PhantomJS 回车，`What is the location of your source and test files ?`再回车，`Should any of the files included by the previous patterns be excluded ?`再回车，`Do you want Karma to watch all the files and run the tests on change ?` 选择 `yes` 回车

- 看见`Config file generated at "/Users/qitmac000352/Documents/admin/yideng/2018.11.06/qatest/my.conf.js".`就表示 karma 的基本配置文件就已经生成了 

- 接着在 `package.json` 里面加上运行 karma 的测试命令：

- ```javascript
    "scripts": {
      "test": "echo \"Error: no test specified\" && exit 1",
      "unit": "karma start my.conf.js"
    },
  ```

- `my.conf.js` 里的 singleRun 必须设置为 true （保证能在无头浏览器里面运行）

- 接着指定开发的项目文件 和 测试文件(`my.conf.js里面的files 字段`)：

  - `*`: 匹配0到多个字符，如果用于路径匹配，只匹配一级目录。 
    `**`: 一般用于路径匹配，**匹配多级目录**。

- ```javascript
     // list of files / patterns to load in the browser
      files: [
        "./unit/**/*.js", //指定 被测试的文件
        "./unit/**/*.spec.js" //指定 测试文件
      ],
  ```

- 接着就可以写测试脚本了，通常，测试脚本与所要测试的源码脚本同名，但是后缀名为`.test.js`（表示测试）或者`.spec.js`（表示规格）。

- ```javascript
  describe('加法函数的测试', function() {
    it("1 加 1应该等于2", function() {
      expect(window.add(1)).toBe(2);
    });
  });
  ```

- 上面这段代码，就是测试脚本，它可以独立执行。测试脚本里面应该包括一个或多个`describe`块，每个`describe`块应该包括一个或多个`it`块。

  `describe`块称为"测试套件"（test suite），表示一组相关的测试。它是一个函数，第一个参数是测试套件的名称（"加法函数的测试"），第二个参数是一个实际执行的函数。

  `it`块称为"测试用例"（test case），表示一个单独的测试，是测试的最小单位。它也是一个函数，第一个参数是测试用例的名称（"1 加 1 应该等于 2"），第二个参数是一个实际执行的函数。

- 接着就可以运行命令 `npm run unit` 去执行这个单元测试了，这里可能会报很多错，需要安装的包有以下：

- ```javascript
    "devDependencies": {
      "jasmine-core": "^3.3.0",
      "karma": "^3.1.1",
      "karma-chrome-launcher": "^2.2.0",
      "karma-coverage": "^1.1.2",
      "karma-jasmine": "^1.1.2",
      "karma-phantomjs-launcher": "^1.0.4"
    },
  ```

- 还有 phantomjs (我是下载下来安装的，应该也可以 通过 npm 安装)

- 在 karma 的配置文件里面：

- ```javascript
  // Karma configuration
  // Generated on Wed Nov 07 2018 20:13:00 GMT-0800 (GMT-08:00)
  
  module.exports = function(config) {
    config.set({
      // base path that will be used to resolve all patterns (eg. files, exclude)
      basePath: '',
      // frameworks to use
      // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
      frameworks: ['jasmine'],
      // list of files / patterns to load in the browser
      files: [
        "./unit/**/*.js",
        "./unit/**/*.spec.js"
      ],
      // list of files / patterns to exclude
      exclude: [
      ],
      // preprocess matching files before serving them to the browser
      // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
      preprocessors: {
        // source files, that you wanna generate coverage for
        // do not include tests or libraries
        // (these files will be instrumented by Istanbul)
        'unit/**/*.js': ['coverage']
      },
      // test results reporter to use
      // possible values: 'dots', 'progress'
      // available reporters: https://npmjs.org/browse/keyword/karma-reporter
      reporters: ['progress','coverage'],
      // optionally, configure the reporter
      coverageReporter: {
        type : 'html',
        dir : 'docs/coverage/'
      },
      // web server port
      port: 9876,
      // enable / disable colors in the output (reporters and logs)
      colors: true,
      // level of logging
      // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
      logLevel: config.LOG_INFO,
      // enable / disable watching file and executing tests whenever any file changes
      autoWatch: true,
      // start these browsers
      // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
      browsers: ['PhantomJS'],
      // Continuous Integration mode
      // if true, Karma captures browsers, runs the tests and exits
      singleRun: true,
      // Concurrency level
      // how many browser should be started simultaneous
      concurrency: Infinity,
      plugins: [
        'karma-jasmine',
        'karma-phantomjs-launcher',
        'karma-coverage'
      ]
    })
  }
  
  ```

- 配置好之后，再 `npm run unit` 就可以检查 **覆盖率**了，报表生成在  `dir : 'docs/coverage/'`下面，使用浏览器打开就可以看见报表了


#### e2e 测试

- 自动化测试

- 先安装  `npm install selenium-webdriver --save-dev`

- 再安装一个驱动包（用来打开浏览器的） 比如火狐的：`[geckodriver(.exe)]` google的 `[chromedriver(.exe)]`，当然电脑上要有对应的浏览器

- 下面写一个简单的 e2e 脚本

- ```javascript
  const {Builder, By, Key, until} = require('selenium-webdriver');//引入驱动
  
  (async function example() {
    let driver = await new Builder().forBrowser('firefox').build();//使用火狐
    try {
      await driver.get('http://www.baidu.com');//打开百度
      await driver.findElement(By.name('wd')).sendKeys('webdriver', Key.RETURN);//找到name属性等于wd的输入框，然后输入 ‘webdriver’ 再回车
      await driver.wait(until.titleIs('webdriver_百度搜索'), 1000);//等待一秒看网页标题是否是webdriver_百度搜索
    } finally {
      await driver.quit();
    }
  })();
  ```

- 运行代码 ：就可以看见火狐浏览器自己在跑程序了，如果成功没有任何提示，如果报错，直接报在终端，没有生成报告

- ```javascript
    "scripts": {
      "test": "echo \"Error: no test specified\" && exit 1",
      "unit": "karma start my.conf.js",
      "e2e": "node ./e2e/baidu.spec.js"
    },
  ```

#### UI自动化

- 可以用 `phantomCSS` 截图抓图比图，可以在无头浏览器里面游走

- 我们安装一个 backstopjs `sudo npm install -g backstopjs` 这里容易安装不上，尝试各种 比如 cnpm 或者 yarn，我是通过切换源唯taobao安装上的

- 然后执行 `backstop init`命令，就会在工程中生成一个文件夹 `backstop_data/engine_scripts` 以及 一个 backstop.json  `backstop_data/engine_scripts/casper` 表示的就是在无头浏览器里面操作鼠标，里面的`backstop_data/engine_scripts/cookies.json` 是可以注入 cookie 的，因为有些网站需要cookie，因为有些网站需要 登陆的。

- 我们在 backstop_data 目录下建立一个 bitmaps_reference 文件夹，这个文件夹是 拿来放 美工UI的图的，

- ```javascript
    "paths": {
      "bitmaps_reference": "backstop_data/bitmaps_reference",// 美工图
      "bitmaps_test": "backstop_data/bitmaps_test",// 测试图
      "engine_scripts": "backstop_data/engine_scripts",// 引擎
      "html_report": "docs/backstop_data/html_report",// 报表
      "ci_report": "docs/backstop_data/ci_report"// ci 报表
    },
    "report": ["browser"],// 放浏览器里面去跑
    "engine": "puppeteer", // 引擎，因为 phantomjs 已经停止维护了，现在的js无头浏览器 puppeteer是第一
  ```

- 然后执行 `backstop test` 就会生成报告文件，我们将ui的图放在 backstop_data/bitmaps_reference里面，再重新运行 backstop test 就会得到 ui 自动化的测试结果，`backstop test` 命令容易卡住，多尝试一下

#### 集成命令

- `"test": "npm run unit && npm run e2e && npm run ui",`
  - 一下 子就运行了**单元测试**、**e2e测试**、**ui自动化测试**。其中单元测试和UI自动化是有报表的

#### service端测试

- 接口测试 service 端要用到 mocha：是专门测试异步api的，首先安装

-  `npm install --save-dev mocha`  `npm install --save-dev mochawesome`

- 需要配置一个 mochaRunner.js (里面写你的测试代码)

- 到目前为止的 package.json 文件如下所示

- ```javascript
  {
    "name": "test",
    "version": "1.0.0",
    "description": "",
    "main": "index.js",
    "scripts": {
      "test": "npm run unit && npm run e2e",
      "unit": "karma start my.conf.js",
      "e2e": "node ./e2e/baidu.spec.js",
      "ui": "backstop test",
      "service": "node ./mochaRunner.js"
    },
    "author": "",
    "license": "ISC",
    "devDependencies": {
      "express": "^4.16.4",
      "jasmine-core": "^3.3.0",
      "karma": "^3.1.1",
      "karma-coverage": "^1.1.2",
      "karma-jasmine": "^1.1.2",
      "karma-phantomjs-launcher": "^1.0.4",
      "mocha": "^5.2.0",
      "mochawesome": "^3.1.1",
      "selenium-webdriver": "^4.0.0-alpha.1"
    },
    "dependencies": {
      "axios": "^0.18.0"
    }
  }
  ```

- `npm run service` 所有的http请求的接口报表就出来了，接口测试就可以了

#### 工程里面用

nightwatchjs（vue用的）、rize+puppeteer

#### 自动化的录制

配起来相当复杂。阿里的 f2etest. Recorder  （要用很多机器）没有精力就不要去搞了