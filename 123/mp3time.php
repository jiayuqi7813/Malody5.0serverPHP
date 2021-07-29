<?php
error_reporting(0);
@header("content-Type: text/html; charset=utf-8"); //语言强制

include_once('getid3/getid3/getid3.php');

function mp3time($file){
    
    $path = $file;
    $getID3 = new getID3();  //实例化类
    $ThisFileInfo = $getID3->analyze($path); //分析文件，$path为音频文件的地址
    $fileduration=$ThisFileInfo['playtime_seconds']; //这个获得的便是音频文件的时长
    $time = (int)ceil($fileduration);
    return $time;

}



?>