# 未来的CSS

> 发现未来的CSS
>
> [`postcss-cssnext` has been deprecated in favor of `postcss-preset-env`. ]
>
> [Use tomorrow’s CSS today.](https://preset-env.cssdb.org/)

看官网能看见 Stage 0 到 4， 分别表示什么意思：

* ### [Stage 0: Aspirational](https://cssdb.org/#stage-0)（激进的）

  * “This is a crazy idea.”  非官方的草案，它应该被认为是高度不稳定,可能发生变化的

* ### [Stage 1: Experimental](https://cssdb.org/#stage-1)（实验的）

  * “This idea might not be crazy.” 实验的草案，比 Stage 0 好一点点

* ### [Stage 2: Allowable](https://cssdb.org/#stage-2)（容许的）

  * “This idea is not crazy.”

* ### [Stage 3: Embraced](https://cssdb.org/#stage-3)（拥抱的）

  * “This idea is becoming part of the web.” 逐渐定型了，可能成为标准了

* ### [Stage 4: Standardized](https://cssdb.org/#stage-4)（标准化的；定型的）

  * Stage 4 features are web standards.

* ### [Rejected](https://cssdb.org/#rejected)（被拒绝的）

  * “I had no idea what I was doing.”

常见用法如下：其他的可随时看官网

### 声明变量

```css
:root {
  --mainColor: red;
}

a {
  color: var(--mainColor);
}
```

### 声明一组属性，便于复用

```css
:root {
  --danger-theme: {
    color: white;
    background-color: red;
  }
}

.danger {
  @apply --danger-theme;
}
```

## 带变量的calc

```css
:root {
  --fontSize: 1rem;
}

h1 {
  font-size: calc(var(--fontSize) * 2);
}
```

## 简化版本的媒体查询

```css
@custom-media --small-viewport (max-width: 30em);
@custom-media --viewport-medium (width <= 50rem);
/* check out media queries ranges for a better syntax !*/

@media (--viewport-medium) {
  body {
    color: var(--mainColor);
    font-family: system-ui;
    font-size: var(--fontSize);
    line-height: calc(var(--fontSize) * 1.5);
    overflow-wrap: break-word;
    padding-inline: calc(var(--fontSize) / 2 + 1px);
  }
}

@media (--small-viewport) {
  /* styles for small viewport */
}


@media (width >= 500px) and (width <= 1200px) {
  /* your styles */
}

/* or coupled with custom media queries */
@custom-media --only-medium-screen (width >= 500px) and (width <= 1200px);

@media (--only-medium-screen) {
  /* your styles */
}
```

## 创建自己的选择器

```css
@custom-selector :--heading h1, h2, h3, h4, h5, h6;

:--heading {
  margin-block: 0;
}
```

## 嵌套选择器

```css
article {
  & p {
    color: #333;
  }
}

```

