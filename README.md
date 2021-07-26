# Malody5.0私服文档

### 版本要求：

php>=7.4

mysql >=5.7

apache2或者nginx均可

### 搭建文档：

附带了malody.sql,新建malody数据库，将其导入即可，内部有一测试用的数据。

测试用数据替换指令，

update `items` set `file` = replace(file, '127.0.0.1', 'ip');

如图所示

![image-20210727002508200](/README.assets/image-20210727002508200-1627316837687-1627316842213.png)

需要更改内容：

config.php

![image-20210720215314296](/README.assets/image-20210720215314296.png)

username，password改为你的数据库密码，

ip改为服务器的公网ip，本地的话就不用动

index.php

![image-20210720215438841](/README.assets/image-20210720215438841.png)

同理，ip改为服务器公网，本地无需改动

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

如需测试更改，将index.php中的

![image-20210720215939272](README.assets/image-20210720215939272.png)

waitlist改为charts即可



### 已知安全性问题：

1.sql注入漏洞

2.任意文件上传漏洞

3.任意文件读取漏洞

建议：如果需要部署在公网做一个私有服务器，最好上个waf，推荐云盾，宝塔，安全狗都可以

### DoList

- [ ] admin翻页功能
- [ ] 活动页面
- [ ] admin搜索功能
- [ ] 安全性提高