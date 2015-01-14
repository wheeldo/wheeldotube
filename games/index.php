<?php
$ds=DIRECTORY_SEPARATOR;
require "..".$ds."bin".$ds."top.php";


$gid=$_GET['gid'];
$uid=$_GET['uid'];

$hu=isset($_GET['hu'])?true:false;


$referrer=isset($_GET['r'])?$_GET['r']:false;



$game_tube=$dbop->selectAssocRow("games","WHERE `unique_id`='{$gid}'");

if(!$game_tube) {
    header("location:gameError");
}



if($uid==="0") {
    // create temp user:
    $fields=array();
    $fields['fname']="Guest";
    $fields['lname']="";
    $fields['email']=$_SERVER['REMOTE_ADDR']."_".time();
    $fields['password']="";
    $fields['image']="/media/img/u_default.jpg";
    $fields['activation_link']="";
    $fields['unique_link']=substr(sha1(time()),0,10);
    $fields['reg_time']=time();
    $fields['ghost']=1;
    

    $user_id=$dbop->insertDB("users",$fields);
    
    $user_data=$dbop->selectAssocRow("users","WHERE `id`='{$user_id}'");
    $user['ID']=$user_data['id'];
    $user['uid']=$user_data['unique_link'];
    $user['name']=$user_data['fname']." ".$user_data['lname'];
    $user['email']=$user_data['email'];
    $user['image']=$user_data['image'];
    $user['ghost']=$user_data['ghost'];
    $_SESSION['login_user']=$user;
    setcookie("user_login", $user['ID'], time()+3600*24*182);
    
    $link="/games?".str_replace('uid=0', "uid={$fields['unique_link']}", $_SERVER['QUERY_STRING']);

    header("location:".$link);
    
    
   str_replace('uid=0', "uid={$fields['unique_link']}", $_SERVER['QUERY_STRING']);
}
else {

    $user=$dbop->selectAssocRow("users","WHERE `unique_link`='{$uid}'");

    if(!$user) {
        header("location:gameError");
    }
}
@mysql_select_db( "wheeldotube_gamedata" , $dbop->getConn() ) or die( "error - unable to select database" );


$game=$dbop->selectAssocRow("games","WHERE `game_id`='{$game_tube['id']}'");

//var_dump($game_tube);

$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$scriptVersion = $detect->getScriptVersion();

$user_data=$user;

$template=$game_tube['game_template'];


$userVisible=array(
    "ID"=>$user_data['id'],
    "name"=>ucfirst($user_data['fname'])." ".ucfirst($user_data['lname'])
);

//var_dump($game_tube);
switch((int)$game_tube['game_type']):
    case 1: // normal old quiz
       require "classes/class.quiz.php"; 
       $game_data=new quiz($game_tube['id'],$user['id'],$user);
       //$logic_dir="quiz_new";
       $logic_dir="quiz";
    break;
    case 2: // normal quiz
       require "classes/class.quiz.php"; 
       $game_data=new quiz($game_tube['id'],$user['id'],$user);
       //$logic_dir="quiz_new";
       $logic_dir="quiz_new";
    break;
    case 3: // test yourself
       require "classes/class.test_yourself.php"; 
       $game_data=new test_yourself($game_tube['id'],$user['id'],$user);
       $logic_dir="test_yourself";
    break;
    case 4: // learning quiz
       require "classes/class.quiz.php"; 
       $game_data=new quiz($game_tube['id'],$user['id'],$user,true);
       $logic_dir="quiz_learning";
    break;
endswitch;




$__isMobile=$detect->isMobile();
//$__isMobile=true;



include "logic".$ds.$logic_dir.$ds."index.php";

//var_dump($game);


//header("Location: http://www.example.com/");