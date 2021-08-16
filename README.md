# Malody5.0私服文档
代码多少写的血压升高，欢迎友好指出意见以及反馈bug，如果单纯过来嘲讽代码写得8行那我建议你还是绕道自己写代码

### 版本要求：

php>=7.4

mysql >=5.7

推荐apache2，nginx存在admin.php无法使用的问题（原因未知。

### 搭建文档：

#### 一键搭建：

新增docker一键部署，直接在当前目录docker-compose up -d即可

访问url后

![image-20210729220716457](/README.assets/image-20210729220716457.png)

按图配置，后面两个内容自定义，随后点击提交即可。

### 手动搭建

init.php作为一键配置功能，首次访问会自动进入，按需配置即可

后续所需更改功能

需要更改内容：

config.php

![image-20210720215314296](/README.assets/image-20210720215314296.png)

username，password改为你的数据库密码，

ip改为服务器的公网ip，本地的话就不用动

admin.php

![image-20210720215516729](/README.assets/image-20210720215516729.png)

adminkey改为你需要的密码，用于后台管理员登录。

### 游戏内使用：

![image-20210720215620744](/README.assets/image-20210720215620744.png)

如图，http://ip/index.php，一定要加index.php,否则无用

### 特色功能

访问ip/admin.php

![image-20210720215721060](/README.assets/image-20210720215721060.png)

进入后端管理，可以进行对谱面管理，谱面审核等一系列功能，非常好用

![image-20210720215802823](/README.assets/image-20210720215802823.png)

### 谱面上传流程：

谱面上传后进入待审核页面，管理员通过后才进入正式谱面中。

首次进入会通过导航页配置选择，后续需要更改，将config.php中

![image-20210729221041172](README.assets\image-20210729221041172.png)

此处进行更改即可，waitlist代表需要审核，charts代表无需审核。

### 建议：

建议：如果需要部署在公网做一个私有服务器，最好上个waf，推荐云盾，宝塔，安全狗都可以

### DoList

- [x] docker一键部署

- [ ] admin翻页功能
- [ ] 活动页面
- [ ] admin搜索功能
- [ ] 安全性提高
