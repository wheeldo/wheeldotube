<?php
require_once 'top.php';
setcookie("user_login", "", time()-3600);
unset($_SESSION['login_user']);

$user_data=$dbop->selectAssocRow("users","WHERE `unique_link`='{$u}'");

$user['ID']=$user_data['id'];
$user['uid']=$user_data['unique_link'];
$user['name']=$user_data['fname']." ".$user_data['lname'];
$user['email']=$user_data['email'];
$user['image']=$user_data['image'];
$user['ghost']=$user_data['ghost'];
$_SESSION['login_user']=$user;
setcookie("user_login", $user['ID'], time()+3600*24*182);

//echo "<pre>";


if(isset($_GET['inv'])) {
    $_SESSION['invited_by']=$_GET['inv']; 
}


$uri_exp=explode("?",$_SERVER['REQUEST_URI']);
//print_r($uri_exp);
//echo "http://www.wheeldo.co{$uri_exp[0]}";
header("location:http://www.wheeldo.co{$uri_exp[0]}");



