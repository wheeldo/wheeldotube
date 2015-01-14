<?php

require_once 'top.php';





if(empty($_POST)):
	$data = file_get_contents("php://input");
	$data_array=json_decode($data,true);
        if(!is_array($data_array)){
            //echo $data;
            
            $pairs = explode('&', $data);
            foreach($pairs as $pair):
                list($key, $value) = explode('=', $pair, 2);
                $_POST[$key]=$value;
            endforeach;
        
            //print_r(parse_str($data));
        }
        else {
            $_POST=$data_array;
        }
endif;

if(!isset($_POST['op']))
	die();
$op=$_POST['op'];

$REQ=array();
foreach($_POST as $key=>$value):
	if(!is_array($value))
		$REQ[mysql_real_escape_string($key)]=mysql_real_escape_string($value);
	else
		$REQ[mysql_real_escape_string($key)]=$value;
endforeach;
unset($_POST);

class usertrack extends dbop{
    private $user_id;
    public function __construct() {
        if(isset($_SESSION['login_user']['ID'])) {
            $this->user_id=$_SESSION['login_user']['ID'];
            // merge sessions:
            $hash=$this->createHash();
            mysql_query("UPDATE `usertrack` SET `user_id`='{$_SESSION['login_user']['ID']}' WHERE `user_id`='{$hash}'");
            //////////////////                        
        }
        else {
            $this->user_id=$this->createHash();
        }
            

        return;
    }
    
    private function createHash() {
        return md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
    }

    public function event($request) {
        
        $uid=$this->user_id;
        if(isset($request['user_id'])) {
            $uid=$request['user_id'];
        }
        
        $this->insertDB("usertrack",array(
            "event"=>$request['event'],
            "event_id"=>$request['event_id'],
            "user_id"=>$uid,
            "time"=>time(),
        ));
        
        header('Content-Type:application/json');
        echo json_encode(array("status"=>"ok"));
    }
}

$ut=new usertrack();
$ut->$op($REQ);



