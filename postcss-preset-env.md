# 未来的CSS

> 发现未来的CSS
>
> [`postcss-cssnext` has been deprecated in favor of `postcss-preset-env`. ]
>
> [Use tomorrow’s CSS today.](https://preset-env.cssdb.org/)

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

