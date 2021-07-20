<?php

  $one=$_GET['id1'];
   $one=iconv("gb2312", "utf-8",$one);
    header("Content-type: application/octet-stream");
 header("Content-Disposition: attachment; filename=".$one);
 readfile($one);
?>