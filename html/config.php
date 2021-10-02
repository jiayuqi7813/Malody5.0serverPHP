<?php
$init = 'false';
$servername = "localhost";
$username = "root";
$password = "root";
$mysql_database = "malody";
$ip = '127.0.0.1';
$status = 'waitlist';
#$status = 'charts';
 
// 创建连接



function searchSql($sql)
{
    global $servername;
    global $username;
    global $password;
    global $mysql_database;
    $conn = mysqli_connect($servername, $username, $password, $mysql_database);
    $ret = mysqli_query($conn, $sql);
    mysqli_close($conn);
    return mysqli_fetch_all($ret, MYSQLI_ASSOC);
}

function foundSql($sql)
{
    global $servername;
    global $username;
    global $password;
    global $mysql_database;
    $conn = mysqli_connect($servername, $username, $password, $mysql_database);
    $ret = mysqli_query($conn, $sql);
    mysqli_close($conn);
    return mysqli_fetch_array($ret, MYSQLI_ASSOC);
}

?>