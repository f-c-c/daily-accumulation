# PM2启动node

[pm2官网]([http://pm2.keymetrics.io/docs/usage/quick-start/](http://pm2.keymetrics.io/docs/usage/quick-start/))

`npm install pm2@latest -g`

配置文件`ecosystem.config.js`：

```json
module.exports = {
    apps: {
        name: "koa-swig-ssr",
        script: "./app.js",
        watch: true,
        instances: "max",
        exec_mode: "cluster",
        log_date_format: "YYYY-MM-DD HH:mm Z",
        merge_logs: true,
        type: "PM2",//PM2 err
        process_id: 0,
        out_file: "./pm2Log/koa-swig-ssr.stdout.log",
        error_file: "./pm2Log/koa-swig-ssr.stderror.log",
        app_name: "one-echo",
        message: "echo\n",
        env: {
            // "PORT": 8085,
            "NODE_ENV": "development"
        },
        env_production: {
            // "PORT": 8083,
            "NODE_ENV": "production",
        }
    }
}
```

`pm2`的配置文件和 我们的`app.js`是紧密相连的

### 相关常用命令

`pm2 start ecosystem.config.js` 默认取配置文件里面的 `env` 配置

`pm2 start ecosystem.config.js --env production` 这样可以取其他的配置，记得跟在 `--env` 后门的名称要对应上

`pm2 stop all` `pm2 list`  `pm2 monit`