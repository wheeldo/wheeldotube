<?php
session_start();
$BASE=dirname (__FILE__);
$DS= DIRECTORY_SEPARATOR;
include $BASE . $DS . 'classes' . $DS . 'dbop.class.php';

$server0=explode(".",$_SERVER['SERVER_ADDR']);
$serverStart=$server0[0];
if($serverStart=="10" || $serverStart=="127" || $serverStart=="::1") $local=1;
else $local=0;


if($local) {
    define("DB_HOST", "localhost");
    define("USER","root");
    define("PASSWORD","");
    define("DATABASE","wheeldotube_gamedata");
}
else {
    define( "DB_HOST", ":/cloudsql/turnkey-rookery-535:main" );
    define("USER","root");
    define("PASSWORD","");
    define("DATABASE","wheeldotube_gamedata");
}

define("BASEURL","http://".$_SERVER['HTTP_HOST']."/Quiz");


$dbop=new dbop();
include $BASE . $DS . 'classes' . $DS . 'wall.class.php';


function getToken($appID,$userID) {
   $url="http://api.wheeldo.com/APIAD.php";
   $postArray=array();
   $postArray['request']='getCode';
   $postArray['function_data[appID]']=$appID;
   $postArray['function_data[userID]']=$userID;
   $response=doRequest($url,$postArray);
   $response=json_decode($response,true);
   return $response['code'];
}

class WheelDoSession {
    private $userToken;
    private $configID;
    private function __construct($userToken,$configID) {
        $this->userToken  = $userToken;
        $this->configID   = $configID;
    }
    public static function createSession($token,$configID){
        return new self($token,$configID);
    }
    public function getSessionData(){
	return $data = array(
            'token'=> $this->userToken,
            'appConfig'	=> $this->configID,
         );
    }
}


function doRequest($url,$postArray) {
    global $local;

    if($local) {
        $ex=explode("//",$url);
        $url=implode("//localhost.",$ex);
    }
    
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);
    $response = curl_exec($ch); 
    curl_close($ch);
    return $response;
}
