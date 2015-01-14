<?php
session_start();
$BASE=dirname (__FILE__);
$DS= DIRECTORY_SEPARATOR;
include $BASE . $DS . 'classes' . $DS . 'dbop.class.php';

mb_internal_encoding('UTF-8');



//var_dump($_SERVER);
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

define("BASEURL","http://".$_SERVER['HTTP_HOST']."/Quiz");


$dbop=new dbop();
include $BASE . $DS . 'classes' . $DS . 'wall.class.php';
include $BASE . $DS . 'classes' . $DS . 'class.email.php';
include $BASE . $DS . 'classes' . $DS . 'class.mobiledetect.php';
include $BASE . $DS . 'classes' . $DS . 'class.appengine.email.php';
include $BASE . $DS . 'classes' . $DS . 'html2text.php';

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

$arr_ident=0;
function vd($var) {
    global $arr_ident;
    echo '<pre style="font-family:Courier New;font-size:12px;border:1px dashed gray;padding:10px;margin:10px;background-color:#F0F0DE;direction:ltr;-webkit-border-radius: 6px;border-radius: 6px;">';
    echo '<div style="margin-bottom:20px;text-decoration:underline;">('.date("d/m/Y H:i:s",time()).') Debug output for \'$'.print_var_name($var).'\':</div>';
    if(is_array($var)) {
        $arr_ident=0;
        pr_arr($var) ;
    }
    elseif(is_string($var)) {
        echo $var;
    }
    elseif(is_bool($var)) {
        echo "Boolean: ";
        if($var) {
            echo '<span style="color:green;">True</span>';
        }
        else {
            echo '<span style="color:red;">False</span>';
        }
    }
    else {
        echo "Fix Type: ".gettype ($var)."<br>";
        var_dump($var);
    }
    echo "</pre>";
}


function pr_arr($arr) {
    global $arr_ident;
    $m_left=0;
    echo '<div style="margin-left:'.$m_left.'px;">';
    echo 'Array ('.count($arr).'){';
    echo '<div style="padding-left:50px;">';
    foreach($arr as $key=>$val):
        if(is_array($val)) {
            $arr_ident++;
            echo '<div><label style="color:#760098;font-weight:bold;">['.$key.']</label> <span style="color:#12B50A;">=></span> </div>';
            pr_arr($val);
        }
        else {
            echo '<div><label style="color:#760098;font-weight:bold;">['.$key.']</label> <span style="color:#12B50A;">=></span> '.$val.'</div>';
        }  
    endforeach;
    echo '</div>';
    echo '}</div>';
}

function print_var_name($var) {
    foreach($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }
    return false;
}
