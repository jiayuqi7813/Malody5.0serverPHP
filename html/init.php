<?php
include('config.php');
global $init;
global $servername;
global $username;
global $password;
global $ip;
global $status;
$text = file_get_contents('config.php');
if($init == 'false'){
    header('location:/');
}
else if(isset($_GET['submited'])){
    $text = str_replace($ip, $_GET['host2'], $text);
    $text = str_replace($username, $_GET['name'], $text);
    $text = str_replace($password, $_GET['pwd'], $text);
    $text = str_replace($servername, $_GET['host1'], $text);
    $text = str_replace($init, 'false', $text);
    if ($_GET['select']=='option2'){
        $text = str_replace($status,'charts',$text);
    }else
        {
        $text = str_replace($status,'waitlist',$text);
        }
    file_put_contents('config.php', $text);

    $adminss = file_get_contents('admin.php');
    $adminss = str_replace('$adminkey = "123";', '$adminkey = "'.$_GET['admin'].'";', $adminss);
    file_put_contents('admin.php', $adminss);

    $connect = mysqli_connect($_GET['host1'], $_GET['name'], $_GET['pwd']);
    mysqli_query($connect, 'CREATE DATABASE malody');
    mysqli_close($connect);
    $connect = mysqli_connect($_GET['host1'], $_GET['name'], $_GET['pwd'], 'malody');
    mysqli_query($connect, 'CREATE TABLE `charts` (
                                `sid` int(255) NOT NULL,
                                `cid` int(255) NOT NULL,
                                `uid` int(255) NOT NULL,
                                `creator` text NOT NULL,
                                `version` text NOT NULL,
                                `level` text NOT NULL,
                                `type` int(1) NOT NULL,
                                `size` int(255) NOT NULL,
                                `mode` int(1) NOT NULL
                            )');
    mysqli_query($connect, 'CREATE TABLE `items` (
                                `cid` int(255) NOT NULL,
                                `name` text NOT NULL,
                                `hash` char(32) NOT NULL,
                                `file` text NOT NULL
                            )');
    mysqli_query($connect, 'CREATE TABLE `songlist` (
                                `sid` int(6) NOT NULL,
                                `cover` text NOT NULL,
                                `length` int(6) NOT NULL,
                                `bpm` float(100,0) NOT NULL,
                                `title` char(100) NOT NULL,
                                `artist` char(100) NOT NULL,
                                `mode` int(2) NOT NULL,
                                `time` bigint(20) NOT NULL,
                                PRIMARY KEY (`sid`)
                                )');
    mysqli_query($connect, 'CREATE TABLE `waitlist` (
                                `sid` int(255) NOT NULL,
                                `cid` int(255) NOT NULL,
                                `uid` int(255) NOT NULL,
                                `creator` text NOT NULL,
                                `version` text NOT NULL,
                                `level` text NOT NULL,
                                `type` int(1) NOT NULL,
                                `size` int(255) NOT NULL,
                                `mode` int(1) NOT NULL
                                )');
    mysqli_close($connect);
    echo '创建完成，将在3秒后跳转到公告页<br>';
    echo '如果没有跳转，请点击<a href="/">这里</a>跳转。';
    echo "<script>setTimeout(\"window.location.href='/'\",3000)</script>";
    die();
}
else{
    print '
<!DOCTYPE html><html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>开始第一次设置</title>
</head>

<body>
    <div style="text-align: center;">
        <h3>Malody私服初始化页面</h3>
        <form action="init.php" method="GET">
            <input type="hidden" name="submited" value="1">
                <h4>数据库配置</h4>
                <input type="text" placeholder="数据库用户名" name="name">
                <br>
                <input type="text" placeholder="数据库密码" name="pwd">
                <br>
                <input type="text" placeholder="数据库地址(例127.0.0.1)" name="host1">
                <br>
            <h4>ip配置（请输入您服务器的域名或者ip）</h4>
            <input type="text" placeholder="127.0.0.1" name="host2">
            <h4>谱面是否需要审核</h4>
                <input type="radio" name="select" id="optionsRadios1" value="option1" checked>审核
                <input type="radio" name="select" id="optionsRadios2" value="option2">直接上传
                <br><br>
            <h4>后台管理员密码</h4>
                <input type="password" name="admin">
                <br><br>
            <button type="submit">提交</button>
        </form>
    </div>
</body>
    ';
}
?>