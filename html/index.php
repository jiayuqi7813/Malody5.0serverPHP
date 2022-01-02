<?php
#header("content-Type: text/html; ");
header('content-type:application/json; charset=utf-8');
include('config.php');
error_reporting(E_ALL^E_NOTICE);//关闭警报
global $init;
if($init == 'true'){
    header('location:/init.php');
}
global $servername;
global $username;
global $password;
global $mysql_database;
global $status;
global $ip;
$allow_wj = 'jpg,png,jpeg,mc,mcz,ogg,mp3'; //合法后缀名
$allow = explode(',', $allow_wj);
$modes= 0;                              //模式
$conn = mysqli_connect($servername, $username, $password, $mysql_database);

//文件复制
function copy_file($filename,$dest){
    //检测$dest是否是目录并且这个目录是否存在，不存在则创建
    if(!is_dir($dest)){
      mkdir($dest,0777,true);
    }
    $destName=$dest.DIRECTORY_SEPARATOR.basename($filename);
    //检测目标路径下是否存在同名文件
    if(file_exists($destName)){
      return false;
    }
    //拷贝文件
    if(copy($filename,$destName)){
      return true;
    }
    return false;
  }



//用于格式化array数组，方便调试

function dump($vars, $label = '', $return = false) {
    if (ini_get('html_errors')) {
        $content = "<pre>\n";
        if ($label != '') {
            $content .= "<strong>{$label} :</strong>\n";
        }
        $content .= htmlspecialchars(print_r($vars, true));
        $content .= "\n</pre>\n";
    } else {
        $content = $label . " :\n" . print_r($vars, true);
    }
    if ($return) { return $content; }
    echo $content;
    return null;
  }


//截取文件后缀函数+判断，用法get_file_suffix('文件名',$allow)
function get_file_suffix($file_name, $allow_type = array())
{
    $fnarray=explode('.', $file_name);
    $file_suffix = strtolower(array_pop($fnarray));
    if (empty($allow_type)) {
        return $file_suffix;
    } else {
        if (in_array($file_suffix, $allow_type)) {
            return true;
        } else {
            return false;
        }
    }
}



function route($uri, Closure $_route)                   //路由
{
    $pathInfo = $_SERVER['REQUEST_URI'] ?? '/';
    $pathInfo = preg_replace('/\?.*?$/is', '', $pathInfo);
    if (preg_match('#^' . $uri . '$#', $pathInfo, $matches)) {
        $_route($matches);
        exit(0);
    }
}


//api版本说明
route('/index.php/api/store/info',   
    function () {   
        $arr = array('code'=>0,'api'=>202112,'min'=>202103,'welcome'=>"welcome to Malody PHP server!");    
        echo json_encode($arr);


}
);

//歌曲列表查询api
route('/index.php/api/store/list', function () {        
    global $ip;
    global $conn;
    $word = $_GET['word'];
    $from = $_GET["from"];
    $arrayip = array();
    $page_size = 80;    //单页最大歌曲数量
    $sql = "SELECT COUNT( * ) AS amount FROM songlist";
    $row = foundSql($sql);
    $amount = $row["amount"];
    if( $amount ){                      //翻页
        if( $amount < $page_size ){ $page_count = 1; }               //如果总数据量小于$PageSize，那么只有一页
        if( $amount % $page_size ){                                  //取总数据量除以每页数的余数
        $page_count = (int)($amount / $page_size)+1;           //如果有余数，则页数等于总数据量除以每页数的结果取整再加一
        }else{
        $page_count = $amount / $page_size;                      //如果没有余数，则页数等于总数据量除以每页数的结果
        }
    }else{
        $page_count = 0;
    }
    //判读是否搜索，屎山语句
    if(empty($word)){
        if($from == 0){
            $sql = "SELECT * FROM songlist LIMIT ".($from)*$page_size.", $page_size";  
        }else{
            $sql = "SELECT * FROM songlist LIMIT ".($from-1)*$page_size.", $page_size";  
        }
    }
    else{
        if($from == 0){
            $sql = "SELECT *
        FROM `malody`.`songlist`
        WHERE (
        CONVERT( `title`
        USING utf8 ) LIKE '%$word%'
        )
        LIMIT ".($from)*$page_size.", $page_size"; 
        }else{
            $sql = "SELECT *
        FROM `malody`.`songlist`
        WHERE (
        CONVERT( `title`
        USING utf8 ) LIKE '%$word%'
        )
        LIMIT ".($from-1)*$page_size.", $page_size";  
        }
    }
    $ret = mysqli_query($conn, $sql);
    $result = mysqli_fetch_all($ret, MYSQLI_ASSOC);
    #var_dump($result);

    for($i=0;$i<=sizeof($result)-1;$i++){
        $result[$i]['cover']='http://'.$ip.''.$result[$i]['cover']; 
    }
    #var_dump($result);
    $jsres= json_encode($result, JSON_UNESCAPED_SLASHES);
    if(sizeof($result)<$page_size){
        $prj = '{"code": 0,"hasMore": false,"next": '.($from+1).',"data": '.$jsres.'}';
    }else{
        $prj = '{"code": 0,"hasMore": true,"next": '.($from+1).',"data": '.$jsres.'}';

    }
    print_r($prj);

});

//歌曲下谱面查询api
route('/index.php/api/store/charts',
    function () {        
    if (isset($_GET["sid"])) {//是否存在"sid"的参数
        $sid = $_GET["sid"];
        $sql = 'SELECT songlist.sid,charts.cid,charts.uid,charts.creator, charts.version, charts.level,charts.type, charts.size,charts.mode FROM songlist , charts WHERE songlist.sid = charts.sid AND songlist.sid ='.$sid.';';
        $result1 = searchSql($sql);
        for($i=0;$i<=sizeof($result1)-1;$i++){
            $result1[$i]['version'] = urldecode($result1[$i]['version']);
        }
        $jsres= json_encode($result1, JSON_UNESCAPED_SLASHES);
        $prj = '{"code": 0,"hasMore": true,"next": 0,"data": '.$jsres.'}';
        print_r($prj);
    } else {
        echo '{"code":-2}';
    }
}
);

//推荐谱面列表(有bug，等修复)
route('/index.php/api/store/promote',   
    function () {        
        global $ip;
        $sql = 'SELECT * FROM charts';
        $result = searchSql($sql);
        #for($i=0;$i<=sizeof($result)-1;$i++){
        #    $result[$i]['cover']='http://'.$ip.''.$result[$i]['cover']; 
        #}
        $jsres= json_encode($result, JSON_UNESCAPED_SLASHES);
        $prj = '{"code": 0,"hasMore": true,"next": 0,"data": '.$jsres.'}';
        print_r($prj);

}
);


//活动列表(分区)
route('/index.php/api/store/events',   
    function () {        
        global $ip;
        $from = $_GET['from'];
        $sql = 'select * from events';
        $result = searchSql($sql);
        for($i=0;$i<=sizeof($result)-1;$i++){
            $result[$i]['cover']='http://'.$ip.''.$result[$i]['cover']; 
        }
        $jsres= json_encode($result, JSON_UNESCAPED_SLASHES);
        $prj = '{"code": 0,"hasMore": true,"next":'.($from+1).',"data": '.$jsres.'}';
        print_r($prj);

}
);

//活动谱面列表(谱面)
route('/index.php/api/store/event',   
    function () {        
        global $ip;
        $eid = $_GET['eid'];
        $from = $_GET['from'];
        $sql = 'select * from event where eid ='.$eid.'';
        $result = searchSql($sql);
        for($i=0;$i<=sizeof($result)-1;$i++){
            $result[$i]['cover']='http://'.$ip.''.$result[$i]['cover']; 
            $result[$i]['version'] = urldecode($result[$i]['version']);
        }
        $jsres= json_encode($result, JSON_UNESCAPED_SLASHES);
        $prj = '{"code": 0,"hasMore": true,"next":'.($from+1).',"data": '.$jsres.'}';
        print_r($prj);

}
);

//谱面下载api
route('/index.php/api/store/download',
    function () {        
        global $ip;
    if (isset($_GET["cid"])) {//是否存在"cid"的参数
        $cid = $_GET["cid"];
        //文件对应查询
        $sql = 'SELECT 
        items.`name`,
        items.hash,
        items.file
        FROM
        charts ,
        items
        WHERE
        charts.cid = items.cid AND
        charts.cid = '.$cid.';';
        //sid,cid查询
        $sql1 = 'SELECT
        charts.sid,
        items.cid
        FROM
        charts ,
        items
        WHERE
        charts.cid = items.cid AND
        charts.cid = '.$cid.';';

        $result = searchSql($sql);
        for($i=0;$i<=sizeof($result)-1;$i++){
            $result[$i]['file']='http://'.$ip.''.$result[$i]['file']; 
        }
        if (empty($result)) {
            echo '{"code":-2}';
        } else {
            $scid = foundSql($sql1);
            $jsres= json_encode($result, JSON_UNESCAPED_SLASHES);
            #print_r($jsres);
            $sid = $scid['sid'];
            $cid = $scid['cid'];
            $prj = '{"code": 0,"items": '.$jsres.',"sid":'.$sid.',"cid":'.$cid.'}';
            print_r($prj);
            #print_r($prj);
        }
    } else {
        echo '{"code":-2}';
    }
}
);

//文件上传验证
route('/index.php/api/store/upload/sign', function () {
    global $ip;
    global $allow;
    global $conn;
    $errorIndex = 0;
    $errorMsg = '';
    $jc = '';
    $host = 'http://'.$ip.'/upload.php';
    if (isset($_POST['sid']) && isset($_POST['cid'])) {
        $sid = $_POST['sid'];
        $cid = $_POST['cid'];
        $name = $_POST['name'];
        $hash = $_POST['hash'];
        //将传参内容存入数组
        $namey = explode(",", $name);
        $hashy = explode(",", $hash);
        $num = count($namey);       //判断数据数量
        
        for ($i=0;$i<=$num-1;$i++) {        //判断文件后缀名合法性
            if(get_file_suffix($namey[$i],$allow)){
                $flag = 1;
                $errorIndex = -1;
                $errorMsg = "access";
            }
            else{
                $errorIndex = $i;
                $errorMsg = "type illegal!";
                $flag = 0;
            }
        }
        for ($i=0;$i<=$num-1;$i++) {
            if($flag){
                $file = '/file/_song_'.$sid.'_/'.$cid.'/'.$namey[$i];
        
                $sql = "INSERT INTO `malody`.`items` (
                   `cid` ,
                   `name` ,
                   `hash` ,
                   `file`
                   )
                    VALUES (
                   ".$cid.",'".$namey[$i]."','".$hashy[$i]."','".$file."'
                   );";
                $result = mysqli_query($conn,$sql);
                if($result){
                    $jsc ='{"sid": "'.$sid.'","cid":"'.$cid.'","hash":"'.$hashy[$i].'","name":"'.$namey[$i].'"}';
                    if($i ==$num-1){
                        $jc = $jc.$jsc;
                    }
                    else{
                        $jc = $jc.$jsc.',';  
                    }
                    
                }
                else{
                    echo "shit";
                }
            }
            
        }
        $res = '{"code":0,"errorIndex":'.$errorIndex.',"errorMsg":"'.$errorMsg.'","host":"'.$host.'","meta":
            ['.$jc.']}';
        print_r($res);
    } else {
        echo 'need';
    }
});

//三阶段验证
route('/index.php/api/store/upload/finish', function () {        
    require 'mp3time.php';
    global $ip;
    global $status;
    global $conn;
    $cover = '';
    $level = 1;
    
    if (isset($_POST['sid']) && isset($_POST['cid'])) {
        $uid = $_GET['uid'];
        $sid = $_POST['sid'];
        $cid = $_POST['cid'];
        $name = $_POST['name'];
        $hash = $_POST['hash'];
        $size = $_POST['size'];
        $main = $_POST['main'];
        $namey = explode(",", $name);
        $hashy = explode(",", $hash);
        $num = count($namey);
        $dir2 = './file/_song_'.$sid.'_/'.$cid.'/';
        $dir2 = str_replace(PHP_EOL, '', $dir2);
        
        for($i=0;$i<=$num-1;$i++){
            //判断后缀，执行对应操作
            if(substr($namey[$i], strrpos($namey[$i], '.')+1) == 'jpg'|substr($namey[$i], strrpos($namey[$i], '.')+1) == 'png'|substr($namey[$i], strrpos($namey[$i], '.')+1) == 'jpeg'){
                $cover = '/pic/'.$namey[$i];
                $finalfile = $dir2.$namey[$i];
                copy_file($finalfile,'pic');
                continue;
            }
            if(substr($namey[$i], strrpos($namey[$i], '.')+1) == 'mc'){
                $file_mine = 'zip://file/_song_'.$sid.'_/'.$cid.'/'.$namey[$i].'#'.$namey[$i];
                $json_string = file_get_contents($file_mine);
                $data = json_decode(trim($json_string,chr(239).chr(187).chr(191)),true);
                $creator = $data['meta']['creator'];
                $version1 = $data['meta']['version'];
                $version = urlencode($version1);
                $mode = $data['meta']['mode'];
                $time = $data['meta']['time'];
                $title = $data['meta']['song']['title'];
                $artist = $data['meta']['song']['artist'];
                $bpm = $data['time']['0']['bpm'];
                continue;
            }
            if(substr($namey[$i], strrpos($namey[$i], '.')+1) == 'ogg'){
                $filepath = 'file/_song_'.$sid.'_/'.$cid.'/'.$namey[$i];
                $length = mp3time($filepath);
            }
        }
        $sql1 = "INSERT INTO `malody`.`songlist` (`sid`, `cover`, `length`, `bpm`, `title`, `artist`, `mode`, `time`) VALUES 
        ('$sid', '$cover', '$length', '$bpm', '$title', '$artist', '$mode', '$time')";
        $result = searchSql($sql1);
        $sql2 = "INSERT INTO `malody`.`$status` (`sid`, `cid`, `uid`, `creator`, `version`, `level`, `type`, `size`,`mode`) VALUES 
        ('$sid', '$cid', '$uid', '$creator', '$version', '$level', '0', '$size','$mode');";
        $result = searchSql($sql2);
        echo '{"code": 0}';
    }

    
});




route('/', function () {
    #可以自定义任何你想要的内容
    header("Content-type: text/html; charset=utf-8");
    echo"公告栏"."<br>";
    echo"服务器正常运行";
});
