<?php

if (isset($_POST['sid']) && isset($_POST['cid'])&& isset($_POST['name']) && isset($_POST['hash']) ) {
    $sid = $_POST['sid'];
    $cid = $_POST['cid'];
    $name = $_POST['name'];
    $hash = $_POST['hash'];

}else{
    echo "{code:-2}";
    die();
}
$dir1 = './file/_song_'.$sid.'_/';
$dir2 = './file/_song_'.$sid.'_/'.$cid.'/';
$dir2 = str_replace(PHP_EOL, '', $dir2);
echo $dir2;
if(!is_dir($dir1)){
    mkdir($dir1);
}
if(!is_dir($dir2)){
    mkdir($dir2);
}

    if($_FILES["file"]["error"]>0){
        $id = $_FILES["file"]["error"];
        echo '{code:'.$id.'}';
    }
    else{
        $Fname = $_FILES["file"]["name"];
        $size = $_FILES["file"]["size"];
        $tmpfile = $_FILES['file']['tmp_name'];
        $finalfile = $dir2.$Fname;
        $str = str_replace(PHP_EOL, '', $finalfile);
        move_uploaded_file($tmpfile,$str);
        echo $finalfile;
    }
?>