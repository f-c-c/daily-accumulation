# postcss-cssnext features 未来的CSS

> 发现未来的CSS

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

