<?php
header("content-Type: text/html; charset=utf-8");
include('config.php');
error_reporting(E_ALL^E_NOTICE);//关闭警报

global $ip;
$allow_wj = 'jpg,png,jpeg,mc,mcz,ogg,mp3'; //合法后缀名
$allow = explode(',', $allow_wj);
$modes= 0;                              //模式

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

route('/index.php/api/store/list', function () {        //歌曲列表查询api
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
    $prj = '{"code": 0,"hasMore": true,"next": 0,"data": '.$jsres.'}';
    print_r($prj);

});


route(
    '/index.php/api/store/charts',
    function () {        //歌曲下谱面查询api
    if (isset($_GET["sid"])) {//是否存在"sid"的参数
        $sid = $_GET["sid"];
        $sql = 'SELECT songlist.sid,charts.cid,charts.uid,charts.creator, charts.version, charts.level,charts.type, charts.size,charts.mode FROM songlist , charts WHERE songlist.sid = charts.sid AND songlist.sid ='.$sid.';';
        $result = searchSql($sql);
        $jsres= json_encode($result, JSON_UNESCAPED_SLASHES);
        $prj = '{"code": 0,"hasMore": true,"next": 0,"data": '.$jsres.'}';
        print_r($prj);
    } else {
        echo '{"code":-2}';
    }
}
);

route(
    '/index.php/api/store/download',
    function () {        //谱面下载api
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


route('/index.php/api/store/upload/finish', function () {        //三阶段验证
    require 'mp3time.php';
    global $ip;
    $cover = '';
    $level = 1;
    if (isset($_POST['sid']) && isset($_POST['cid'])) {
        $sid = $_POST['sid'];
        $cid = $_POST['cid'];
        $name = $_POST['name'];
        $hash = $_POST['hash'];
        $size = $_POST['size'];
        $main = $_POST['main'];
        $namey = explode(",", $name);
        $hashy = explode(",", $hash);
        $num = count($namey);
        for($i=0;$i<=$num-1;$i++){
            //判断后缀，执行对应操作
            if(substr($namey[$i], strrpos($namey[$i], '.')+1) == 'jpg'|substr($namey[$i], strrpos($namey[$i], '.')+1) == 'png'|substr($namey[$i], strrpos($namey[$i], '.')+1) == 'jpeg'){
                $cover = '/file/_song_'.$sid.'_/'.$cid.'/'.$namey[$i];
                continue;
            }
            if(substr($namey[$i], strrpos($namey[$i], '.')+1) == 'mc'){
                $file_mine = 'zip://file/_song_'.$sid.'_/'.$cid.'/'.$namey[$i].'#'.$namey[$i];
                $json_string = file_get_contents($file_mine);
                $data = json_decode(trim($json_string,chr(239).chr(187).chr(191)),true);
                $creator = $data['meta']['creator'];
                $version = $data['meta']['version'];
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
        $sql2 = "INSERT INTO `malody`.`waitlist` (`sid`, `cid`, `uid`, `creator`, `version`, `level`, `type`, `size`,`mode`) VALUES 
        ('$sid', '$cid', '0', '$creator', '$version', '$level', '0', '$size','$mode');";
        $result = searchSql($sql2);
        echo '{"code": 0}';
    }

    
});




route('/', function () {
    #echo '<script>alert("跳转中");window.location.href="/index.php/api/store/list";</script>';
    echo"公告栏"."<br>";
    echo"服务器正常运行";
});
