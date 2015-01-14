<?php
include 'top.php';
header("Content-type: text/html; charset=utf-8");

$user = array(
   "fname"=>$_POST['fname'], 
   "lname"=>$_POST['lname'], 
   "email"=>$_POST['email']
 );

$channel_id=$_POST['channel_id'];


$check=$dbop->selectAssocRow("users","WHERE `email`='{$user['email']}'");
if($check) {
    $user_id=$check['id'];
}
else {
    $fields=array(
        'email'=>$user['email'],
        'fname'=>$user['fname'],
        'lname'=>$user['lname'],
        'image'=>'/media/img/u_default.jpg',
        'activation_link'=>sha1(time()),
        'unique_link'=>substr(sha1(time().$user['email']),0,10),
        'reg_time'=>time(),
        'ghost'=>0

    );
    $user_id=$dbop->insertDB("users",$fields);
    echo mysql_error();
}



// assoc with channel:


$check=$dbop->selectAssocRow("channel_user","WHERE `channel_id`='{$channel_id}' AND `user_id`='{$user_id}'");
if(!$check) {
    $fields=array(
        'channel_id'=>$channel_id,
        'user_id'=>$user_id
    );
    $dbop->insertDB("channel_user",$fields);
}