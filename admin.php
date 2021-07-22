<?php

$adminkey = "123";/*设置密码*/
session_start();
include ("config.php");

$modes = array("Key","","","Catch","Pad","Taiko","Ring","Slide","Live");
$types = array("Alpha","Beta","Stable");

function route($uri, Closure $_route)                   //路由
{
    $pathInfo = $_SERVER['REQUEST_URI'] ?? '/';
    $pathInfo = preg_replace('/\?.*?$/is', '', $pathInfo);
    if (preg_match('#^' . $uri . '$#', $pathInfo, $matches)) {
        $_route($matches);
        exit(0);
    }
}

function config1(){ //主页面加载
    print <<<EOT
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>welcome</title>
    <style>
</style>
    <!--<link href="bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">-->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- 引入jquery -->
    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.js"></script>
    <!-- jQThumb缩略图插件 -->
    <script type="text/javascript" src="/scripts/jqthumb.js"></script> 
    <script type="text/javascript" src="/scripts/jqthumb.min.js"></script>
    <script type="text/javascript">
        //处理缩略图
	    function DrawImage(hotimg){
		    $(hotimg).jqthumb({
		    	width : '100%',//宽度
		    	height : '200px',//高度
		    	//position : { y: '50%', x: '50%'},//从图片的中间开始产生缩略图
		    	zoom : '1',//缩放比例
		    	method : 'auto'//提交方法，用于不同的浏览器环境，默认为‘auto’
		    });
	    }
    </script>
    </head>
EOT;
}
//主页面登录验证
route('/admin.php', function () {
    global $adminkey;
    if(@$_POST['password'] == $adminkey){
        $_SESSION['login'] = md5($adminkey);
        }
        if($_SERVER['QUERY_STRING'] == "logout"){
        $_SESSION['login'] = "";
        header("location: " . $_SERVER['PHP_SELF']);
        exit();
        }
        $html_login = <<<EOF
        <!DOCTYPE html>
        <html>
        <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            /*--预设--*/ 
            body { padding:0px;margin: 0px; } 
            .gg {
                justify-content: center;
                display: flex !important;
            }
            #lyrow, #lyrow input, #lyrow textarea { font-size:12px;font-family: 'Microsoft YaHei', '微软雅黑', MicrosoftJhengHei, '华文细黑', STHeiti, MingLiu; } 
            #lyrow { height:100vh;width: 100vw; } 
            #lyrow div { min-height: 18px;  } 
            #lyrow input, #lyrow textarea { border:rgb(235, 235, 235) 1px solid;border-radius: 3px;padding: 5px 8px;outline: 0; } 
            #lyrow input:hover, #lyrow textarea:hover { border: 1px solid #6bc1f2; } 
            /*--编辑--*/ 
            #lyrow .loginform { text-align:center;margin:0px auto 0px auto;width:230px;height:100px;background-color:rgba(255, 255, 255, 1);box-shadow:2px 2px 10px 1px rgba(64, 63, 63, 1); } 
            #lyrow .un_redsa { text-align:center;margin:260px auto 0px auto; } 
            
            </style>
            <!--下载font-awesome.css图标包-->
            <!--https://www.58html.com/html/template/font-awesome.zip-->
            <link rel="stylesheet" href="https://www.58html.com/gui/css/font-awesome/css/font-awesome.css">
            <div id="lyrow">
            
            <div >
                <h1 class="gg">Malody后端服务器管理系统</h1></div>
                <div id="loginform">
        
                <div style="text-align:center; margin:260px auto 0px;">
                
                <form action="" method="post">密码<input type="password" name="password" style="width:120px; margin-top: 35px;">
                
                <input type="submit" value="登录" style="margin-left: 5px;">
            </form>
            </div>
            </div>
            
            
            </div>
        EOF;
        if(@$_SESSION['login'] != md5($adminkey)){
        exit($html_login);
        
        }
        echo '<script>window.location.href="/admin.php/manage";</script>';
});

//谱面管理中心
route('/admin.php/manage', function () {
    global $adminkey;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
        print <<<EOT
        <body>
     <div class="container">
        <div class="row">
            <div class="span12">
                <h3 class="text-center">
                    malody服务器后端管理
                </h3>
                <ul class="nav nav-tabs">
                    <li class="active" id='manage'>
                        <a href="#">铺面管理</a>
                    </li>
                    <li id='wait' class ="">
                        <a href="/admin.php/wait">待审核</a>
                    </li>
                    <li id='stable' class="">
                        <a href="/admin.php/stable">stable管理</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>


EOT;
echo '<h4  class="container"contenteditable="true">歌曲列表:</h4>';
global $conn;
$sql = "SELECT * FROM songlist";
$row = searchSql($sql);
$num = sizeof($row);
echo '<div class="container">
';
for($i=0;$i<=$num-1;$i++){
    $sql1 = 'SELECT
    *
    FROM
    charts
    WHERE
    charts.sid = '.$row[$i]['sid'].'
    ';
    $row2 = searchSql($sql1);
    $nums = sizeof($row2);
    echo '
    <div class="col-sm-6 col-md-3">
    <div class="thumbnail" >
        <img src="'.$row[$i]["cover"].'" class="img-responsive" onload="DrawImage(this)" >
        <div class="caption">
            <h3>'.$row[$i]["title"].'</h3>
            <p>sid:'.$row[$i]['sid'].'</p>
            <p>铺面数量：'.$nums.'</p>
            <p><a href="/admin.php/cat?sid='.$row[$i]['sid'].'" class="btn btn-primary" role="button">查看</a> <a href="#" class="btn btn-default" role="button">编辑</a></p>
        </div>
    </div>
    </div>
';
}
echo'</div></div>';
#var_dump($row);
});


//谱面审核
route('/admin.php/wait', function () {
    global $modes;
    global $adminkey;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
        print <<<EOT
        <body>
     <div class="container">
        <div class="row">
            <div class="span12">
                <h3 class="text-center">
                    malody服务器后端管理
                </h3>
                <ul class="nav nav-tabs">
                    <li class="" id='manage'>
                        <a href="/admin.php/manage">铺面管理</a>
                    </li>
                    <li id='wait' class ="active">
                        <a href="#">待审核</a>
                    </li>
                    <li id='stable' class="">
                        <a href="/admin.php/stable">stable管理</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
EOT;

echo '<h4  class="container"contenteditable="true">待审核列表</h4>';

echo '
    <div class="container">
    <style type="text/css">
    .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
    .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
    .tftable tr {background-color:#d4e3e5;}
    .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
    .tftable tr:hover {background-color:#ffffff;}
    </style>

    <table class="tftable" border="1">
    <th>sid</th><th>cid</th><th>creator</th><th>version</th><th>title</th><th>mode</th><th>edit</th></tr>
   ';
    $sql = 'SELECT
    waitlist.sid,
    waitlist.cid,
    waitlist.creator,
    waitlist.version,
    songlist.title,
    waitlist.`mode`
    FROM
    waitlist ,
    songlist
    WHERE
    songlist.sid = waitlist.sid
    ';
    $row = searchSql($sql);
    #var_dump($row);
    for($i=0;$i<=sizeof($row)-1;$i++){
        echo '
        <tr><td>'.$row[$i]['sid'].'</td><td>'.$row[$i]['cid'].'</td><td>'.$row[$i]['creator'].'</td><td>'.$row[$i]['version'].'</td><td>'.$row[$i]['title'].'</td><td>'.$modes[$row[$i]['mode']].'</td><td>
        <form method="post" action="/admin.php/ok">
        <input type="text" name="cid" hidden value="'.$row[$i]['cid'].'"/>
        <input class="btn btn-link" type="submit" value="通过" />
        </form>
        <form method="post" action="/admin.php/del">
        <input type="text" name="cid" hidden value="'.$row[$i]['cid'].'"/>
        <input class="btn btn-link" type="submit" value="删除" />
        </form>
        </td></tr>';
    }
echo '</table>';
});

//stable管理
route('/admin.php/stable', function () {
    global $modes;
    global $adminkey;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
        print <<<EOT
        <body>
     <div class="container">
        <div class="row">
            <div class="span12">
                <h3 class="text-center">
                    malody服务器后端管理
                </h3>
                <ul class="nav nav-tabs">
                    <li class="" id='manage'>
                        <a href="/admin.php/manage">铺面管理</a>
                    </li>
                    <li id='wait' class ="">
                        <a href="/admin.php/wait">待审核</a>
                    </li>
                    <li id='stable' class="active">
                        <a href="#">stable管理</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
EOT;
echo'<div class="container">';
echo '<h4  class="container"contenteditable="true">stable歌曲列表</h4>';
echo '<style type="text/css">
.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
.tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
.tftable tr {background-color:#d4e3e5;}
.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
.tftable tr:hover {background-color:#ffffff;}
</style>

<table class="tftable" border="1">
<th>sid</th><th>cid</th><th>title</th><th>creator</th><th>version</th><th>mode</th></tr>';

$sql = 'SELECT
charts.sid,
charts.cid,
songlist.title,
charts.creator,
charts.version,
charts.type,
charts.`mode`
FROM
charts ,
songlist
WHERE
charts.sid = songlist.sid AND
charts.type = 2';
$row = searchSql($sql);
#var_dump($row);

for($i=0;$i<=sizeof($row)-1;$i++){
    echo '
    <tr><td>'.$row[$i]['sid'].'</td><td>'.$row[$i]['cid'].'</td><td>'.$row[$i]['title'].'</td><td>'.$row[$i]['creator'].'</td><td>'.$row[$i]['version'].'</td><td>'.$modes[$row[$i]['mode']].'</td>';
}

});

route('/admin.php/cat', function () {//查看语句
    global $adminkey;
    global $modes;
    global $types;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
    
    echo'<div class="container">';
    if(isset($_GET['sid'])){
        $sid = $_GET['sid'];
        echo "sid:$sid,谱面如下";
        $sql = 'SELECT
        *
        FROM
        charts
        WHERE
        charts.sid = '.$sid.'
        ';
        echo '
    
    <style type="text/css">
    .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
    .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
    .tftable tr {background-color:#d4e3e5;}
    .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
    .tftable tr:hover {background-color:#ffffff;}
    </style>

    <table class="tftable" border="1">
    <th>cid</th><th>creator</th><th>version</th><th>mode</th><th>type</th><th>edit</th></tr>
   ';
        $row = searchSql($sql);
        #var_dump($row);
        //输出
    for($i=0;$i<=sizeof($row)-1;$i++){
            echo '
            <tr><td>'.$row[$i]['cid'].'</td><td>'.$row[$i]['creator'].'</td><td>'.$row[$i]['version'].'</td><td>'.$modes[$row[$i]['mode']].'</td><td>'.$types[$row[$i]['type']].'</td><td><form method="post" action="/admin.php/edit">
            <input type="text" name="cid" hidden value="'.$row[$i]['cid'].'"/>
            <input type="text" name="sid" hidden value="'.$row[$i]['sid'].'"/>
            <input class="btn btn-link" type="submit" value="编辑" />
            </form></td></tr>';
        }
    echo '</table>';
    echo '<p><a href="/admin.php/manage" class="btn btn-primary btn pull-right" role="button">back</a>';
    }
});

//审核谱面通过
route('/admin.php/ok', function () {
    error_reporting(0);
    global $adminkey;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
    if(isset($_POST['cid'])){
        $cid = $_POST['cid'];
        $sql1 = 'insert into charts select * from waitlist where cid='.$cid.'';
        $sql2 = 'DELETE FROM waitlist WHERE cid = '.$cid.'';
        $row1 = searchSql($sql1);
        $row2 = searchSql($sql2);
        echo '<script>alert("success");window.location.href="/admin.php/wait";</script>';
    }else{
        echo 'error';
    }
    

});

route('/admin.php/edit', function () {
    global $adminkey;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
    error_reporting(0);
        $cid = $_POST['cid'];
        $sid = $_POST['sid'];
       echo '<div class="container">
       <script language = "javascript">
 
         function wndback()
         { 
            window.history.back();location.reload();
            return false;
        }  
        var ti = "changed!";
     </script>
       <h3>type状态</h3>
       <form action="/admin.php/edit" method="POST">
        <input type="radio" name="type" value="0" >Alpha
        <input type="radio" name="type" value="1" checked="checked">Beta
        <input type="radio" name="type" value="2" checked="checked">Stable
        <input type="text" name="cid" hidden value="'.$cid.'"/>
        <input type="text" name="sid" hidden value="'.$sid.'"/>
        <input class="btn btn-primary" type="submit" value="确定" onclick="alert(ti);"/>
        </form>';
        $type = $_POST['type'];
        $sid = $_POST['sid'];
        $sql = 'UPDATE `malody`.`charts` SET `type` = '.$type.' WHERE  `charts`.`cid` = '.$cid.' LIMIT 1';
        $row = searchSql($sql);
        echo '<form action="/admin.php/delete" method="POST">
        <input type="text" name="cid" hidden value="'.$cid.'"/>
        <input class="btn btn-danger" type="submit" value="删除"/>
        </form>';
        echo '<a href="/admin.php/cat?sid='.$sid.'"class="btn btn-primary" role="button" >返回</a>';
        
       
});

//常规谱面删除
route('/admin.php/delete', function () {
    
    global $ip;
    global $adminkey;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
    
    $cid = $_POST['cid'];
    $sql0 = 'SELECT * FROM `items` WHERE cid ='.$cid.'';
    $row =searchSql($sql0);
    #var_dump($row);
    $str1 = "http://$ip/";
    $sql1 = 'DELETE FROM charts WHERE cid = '.$cid.'';
    $sql2 = 'DELETE FROM itmes WHERE cid = '.$cid.'';
    $row1 = searchSql($sql1);
    $row2 = searchSql($sql2);
    for($i=0;$i<=sizeof($row)-1;$i++){
        $str2 = str_replace($str1,'',$row[$i]['file']);
        if(file_exists($str2)){
            $res = unlink($str2);
            if($res){
                echo '<script>alert("delete success");window.location.href="/admin.php";</script>';
            }else{
                echo '<script>alert("error");window.location.href="/admin.php";</script>';
            }
        }
    }
    
       
});

//审核谱面删除
route('/admin.php/del', function () {
    error_reporting(0);
    global $ip;
    global $adminkey;
    if(@$_SESSION['login'] != md5($adminkey)){
        echo '<script>window.location.href="/admin.php";</script>';
    }
    config1();
    
    $cid = $_POST['cid'];
    $sql0 = 'SELECT * FROM `items` WHERE cid ='.$cid.'';
    $row =searchSql($sql0);
    #var_dump($row);
    $str1 = "http://$ip/";
    $sql1 = 'DELETE FROM waitlist WHERE cid = '.$cid.'';
    $sql2 = 'DELETE FROM itmes WHERE cid = '.$cid.'';
    $row1 = searchSql($sql1);
    $row2 = searchSql($sql2);
    for($i=0;$i<=sizeof($row)-1;$i++){
        $str2 = str_replace($str1,'',$row[$i]['file']);
        if(file_exists($str2)){
            $res = unlink($str2);
            if($res){
                echo '<script>alert("delete success");window.location.href="/admin.php/wait";</script>';
            }else{
                echo '<script>alert("error");window.location.href="/admin.php/wait";</script>';
            }
        }
    }
    
       
});


?>