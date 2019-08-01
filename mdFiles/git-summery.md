

### Git 别名

```
$ git config --global alias.co checkout
$ git config --global alias.br branch
$ git config --global alias.ci commit
$ git config --global alias.st status
```

这意味着，当要输入 `git commit` 时，只需要输入 `git ci`。

### 常用命令

- `$ touch index.html` 在当前目录下创建一个index.html文件
- `$ mkdir css` 在当前目录下创建一个css文件夹
- `$ cd ..` 返回上一层目录
- `$ cd js` 进入目录js
- `$ pwd` 显示当前所在的目录
- `$ git clone git@github.com:YourUserName/xxx.git` 克隆远程仓库到本地仓库
- `$ git clone -b assas git@github.com:YourUserName/xxx.git` 克隆远程仓库的不同分支到本地仓库
- `$ git add index.html` 向暂存区中添加文件index.html
- `$ git add .` 向暂存区中添加所有文件
- `$ git commit -m "本次commit的描述信息"` 将暂存区中的文件实际保存到仓库的历史记录中
- `$ git push -u origin master` 推送到远程仓库master分支
- `$ git status` 查看仓库的状态
- `$ git log` 查看提交日志（commit的日志）
- `$ git diff` 查看工作树和暂存区的区别（只有add过的文件才能看见区别）
- `$ git diff HEAD` 查看工作树和最新提交（commit）的差别。commit之前运行该命令是一个好习惯
- `$ git fetch` 取回所有分支的更新，比如远程别人又新建了分支，我们需要执行该命令同步信息
- `$ git branch` 查看本地分支
- `$ git branch -a` 查看远程分支
- `$ git branch -b feature-A` 基于master创建一个分支feature-A，并切换到分支feature-A
- `$ git checkout master` 切换回本地分支master（可在本地已有的分支间来回的切换）
- `$ git checkout -b xxx origin/xxx` 基于远程的xxx分支在本地创建分支并命名为xxx，并切换到该分支
- `$ no next tag解决办法` 出现no next tag ，此时按 `q + enter` 即可退出
- `$ ssh-keygen -t rsa -C "your_email@youremail.com"` 在新的电脑上使用git时，需要从新生成秘钥对
- 查看本地文件id_rsa.pub或者使用命令`$ cat ~/.ssh/id_rsa.pub`查看公钥。以便在github上新增一个公钥
- 完成以后，验证下这个key是不是正常工作：`$ ssh -T git@github.com`
- `$ git remote add origin git@github.com:YourUserName/xxx.git` 将本地仓库关联到远程仓库（push时就会push到该远程仓库）
- 出错信息：fatal: remote origin already exists. 解决办法：先输入 `$ git remote rm origin`
- 在别人电脑上使用git时，有时提交push时显示别人的名称以及email，修改方法：
  `$ git config --global user.name "Your Name"`
  `$ git config --global user.email you@example.com`