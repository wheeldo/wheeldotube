<?php
session_start();
error_reporting(E_ALL);

date_default_timezone_set('Asia/Jerusalem');

ini_set('display_errors', '1');
$BASE=dirname (__FILE__);
$DS= DIRECTORY_SEPARATOR;
include $BASE . $DS . 'classes' . $DS . 'dbop.class.php';

$server0=explode(".",$_SERVER['REMOTE_ADDR']);
$serverStart=$server0[0];
if($serverStart=="10" || $serverStart=="127" || $serverStart=="::1") $local=1;
else $local=0;
if($local) {
    define("DB_HOST", "localhost");
    define("USER","root");
    define("PASSWORD","");
    define("DATABASE","wheeldotube_main");
}
else {
    define("DB_HOST", ":/cloudsql/turnkey-rookery-535:main" );
    define("USER","root");
    define("PASSWORD","");
    define("DATABASE","wheeldotube_main");
}

define("BASEURL","http://".$_SERVER['HTTP_HOST']."/admin");


$dbop=new dbop();



function create_pa($str) {
    $s="tube2014";
    $s.=$str;
    $s_hash=sha1($s);
    
    $extra=substr(md5(time()), 0, 8);
    return $extra.$s_hash;
}

function is_equal_pa($str1,$str2) {
    return substr($str1, 8)==substr($str2, 8);
}



function doRequest($url,$postArray) {

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);
    $response = curl_exec($ch); 
    curl_close($ch);
    return $response;
}

//var_dump($_SESSION['login']);
