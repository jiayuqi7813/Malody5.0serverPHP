# Malody5.0私服文档
当前版本:0.23  


代码多少写的血压升高，欢迎友好指出意见以及反馈bug，如果单纯过来嘲讽代码写得8行那我建议你还是绕道自己写代码  

欢迎加群：767557707  

提出您的宝贵意见以及bug反馈  


### 如何更新：



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

![image-20210729221041172](/README.assets/image-20210729221041172.png)

此处进行更改即可，waitlist代表需要审核，charts代表无需审核。

### 建议：

建议：如果需要部署在公网做一个私有服务器，最好上个waf，推荐云盾，宝塔，安全狗都可以

### DoList

- [x] docker一键部署

- [ ] admin翻页功能
- [x] 活动页面
- [ ] admin搜索功能
- [ ] 安全性提高

### 版本更新日志：

**date:2021-10-2**

version:0.25

--新增event和events数据库，通过init.php进行初始化

--admin.php更新活动管理，暂时活动内增加歌曲暂未支持，但是可以进行创建活动与删除活动

--index.php大部分的api相应回包改为json模式

--修复数据库未知的bug






date:2021-8-18  (未推送，先把readme推出来，等admin.php关于events写好的)

version:0.24  

--更新/index.php/api/store/events路由，可以写入活动  

--更新/index.php/api/store/event路由，可以写入活动谱面  

--修复谱面数据库无法获取上传者uid的bug

  

date:2021-8-17  

version:0.23  

--更新/index.php/api/store/info路由，展示当前版本  
--修复谱面删除时cover也随之删除  
--修复谱面无限刷新的问题  
--修复部分不可见字符导致谱面上传后无法正确写入数据库的问题  
--修复出现同数据的谱面时全部显示的问题

date:2021-8-16  

version:0.21  

--更新了promote的路由，即铺面推荐，具体还无法正常工作，等待客户端修复bug
