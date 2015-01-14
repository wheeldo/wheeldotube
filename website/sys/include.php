<?php
function isLocalMachine() {

    if(!isset($_SERVER['SERVER_ADDR'])) {
        return false;
    }

    $server0=explode(".",$_SERVER['SERVER_ADDR']);
    $serverStart=$server0[0];
    if($serverStart=="10" || $serverStart=="127") return true;
    else return false;

} 

if(isLocalMachine()) {
    $baseDir="/website/";
}
else {
    $baseDir="/website/";
}



// grabbing referer:
$my_sites = array(
    "http://www.wheeldo.com",
    "http://www.wheeldo.co",
    "http://wheeldo.co",
    "http://www.wheeldo.info",
    "http://localhost",
);
 
$my_site=false;

foreach($my_sites as $site):
    $ref_url=substr($_SERVER['HTTP_REFERER'], 0, strlen($site));
    if($ref_url === $site):
        $my_site=true;
    endif;
endforeach;


if(isset($_SESSION['login_user']['ID']))
    $uid=$_SESSION['login_user']['ID'];
else
    $uid=md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);

if(!$my_site) {
    $postData=array(
        "op"=>"event",
        "event"=>"referer",
        "event_id"=>$_SERVER['HTTP_REFERER'],
        "user_id"=>$uid
    );

    $data = http_build_query($postData);
    $context = [
      'http' => [
        'method' => 'post',
        'X-Appengine-Inbound-Appid'=>'turnkey-rookery-535',
        'content' => $data
      ]
    ];
    $context = stream_context_create($context);
    $result = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/ut', false, $context);  
}
