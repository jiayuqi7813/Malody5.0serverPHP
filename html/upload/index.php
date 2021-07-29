<?php

$down='file_dowm.php';
$path='./upload/';
$url=$_SERVER['REQUEST_URI'];//访问此页面所需的 URI
if (isset($_GET['dir'])) {//判断是否存在目录
$path=$path.$_GET['dir'].'/';
}else{
$url=$url.'?dir=';
}
$fh=opendir($path);
$data=array();
while (($row=readdir($fh))!==false) {
if ($row=='.' || $row=='..') {
continue;
}
$row=iconv("gb2312", "utf-8",$row);
$data[]=$row;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Document</title>
</head>
<body>
<form action="upload_file.php" method="POST" enctype="multipart/form-data">
    <label for="file">文件名：</label>
    <input type="file" name="file" id="file"><br>
    <input type="submit"name="submit" value="提交">
</form>

<table border="1">
<tr>
<td>文件名</td>
<td>操作</td>
</tr>
<?php foreach ($data as $v) { ?>
<tr>
<td><?php echo $v; ?></td>
<td>
<?php if(is_dir($path.$v)){ ?>
<a href="<?php echo $url.'/'.$v; ?>">打开</a>
<a href="<?php echo $down.'?'.'id1'.'='.$v ?>">下载</a>
<?php }else{ ?>
<a href="<?php echo $path.$v; ?>">查看</a>
<a href="<?php echo $down.'?'.'id1'.'='.$v ?>">下载</a>
<?php } ?>
</td>
</tr>
<?php } ?>
</table>
</body>
</html>