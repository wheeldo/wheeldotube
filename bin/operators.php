<?php
require_once 'google/appengine/api/taskqueue/PushTask.php';
use \google\appengine\api\taskqueue\PushTask;

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;


require_once 'top.php';
$just_class=isset($just_class)?$just_class:false;

if(!$just_class) {
    if(empty($_POST)):
            $data = file_get_contents("php://input");
            $data_array=json_decode($data,true);
            $_POST=$data_array;
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
}



class operators extends dbop{

    public function __consteuct() {
            return;
    }

    public function checkGuide($request) {
        $user_id=$_SESSION['login_user']['ID'];

        $check=$this->selectAssocRow("guidance","WHERE `user_id`='{$user_id}' AND `{$request['guide']}`=1");

        $res['status']=$check?"faild":"ok";
        header('Content-Type:application/json');
        echo json_encode($res);
    }

    public function setGuide($request) {
        $user_id=$_SESSION['login_user']['ID'];

        $check=$this->selectAssocRow("guidance","WHERE `user_id`='{$user_id}' AND `{$request['guide']}`=1");

        if(!$check):
            $this->insertDB("guidance", array(
                "user_id"=>$user_id,
                $request['guide']=>1
            ));
        endif;

        header('Content-Type:application/json');
        echo json_encode($request);
    }

    public function getRecommendedGame($request) {
            $res=array();
            $gid=$request['gid'];
            $user_id=$_SESSION['login_user']['ID'];

            $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");
            $game_id=$game['id'];
            $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game['id']}'");
            $channel_id=$game_channel['channel_id'];


            $return_game=array();

            $sql="SELECT
            games.id,
            games.`owner`,
            games.unique_id,
            games.time,
            games.private,
            games.open_status,
            games.game_type,
            games.game_template,
            games.plays,
            games.`name`,
            games.game_type,
            games.thumbnail,
            games.full_desc,
            games.call_action_text,
            games.call_action_link,
            games.prize,
            games.prize_text,
            games.prize_time_limit,
            games.winner,
            games.voucher,
            games.voucher_name,
            games.voucher_email_subject,
            games.voucher_email_content
            FROM
            games
            INNER JOIN game_channel ON game_channel.game_id = games.id
            WHERE
            game_channel.channel_id = {$channel_id} AND
            game_channel.game_id != {$game_id} AND
            games.approved = 1 AND
            games.private = 0 AND
            games.thumbnail !='' AND
            NOT EXISTS
                            (
                            SELECT  null
                            FROM    wheeldotube_gamedata.game_quiz_user
                            WHERE   wheeldotube_gamedata.game_quiz_user.game_id = wheeldotube_main.games.id AND wheeldotube_gamedata.game_quiz_user.user_id = {$user_id}
                            )"
            . "ORDER BY
            games.time DESC";


            $game_from_channel=$this->selectQDB($sql);

            if($game_from_channel['n']>0) {
                    $return_game= mysql_fetch_assoc($game_from_channel['p']);
            }
            else {
                    // give some random game back:
                    $sql="SELECT
                    games.id,
                    games.`owner`,
                    games.unique_id,
                    games.time,
                    games.private,
                    games.open_status,
                    games.game_type,
                    games.game_template,
                    games.plays,
                    games.`name`,
                    games.game_type,
                    games.thumbnail,
                    games.full_desc,
                    games.call_action_text,
                    games.call_action_link,
                    games.prize,
                    games.prize_text,
                    games.prize_time_limit,
                    games.winner,
                    games.voucher,
                    games.voucher_name,
                    games.voucher_email_subject,
                    games.voucher_email_content
                    FROM
                    games
                    WHERE
                    NOT EXISTS
                                    (
                                    SELECT  null
                                    FROM    wheeldotube_gamedata.game_quiz_user
                                    WHERE   wheeldotube_gamedata.game_quiz_user.game_id = wheeldotube_main.games.id AND wheeldotube_gamedata.game_quiz_user.user_id = {$user_id}
                                    ) AND
                    games.thumbnail !='' AND
                    games.approved = 1 AND
                    games.private = 0
                    ORDER BY
                    games.time DESC";


                    $game_random=$this->selectQDB($sql);
                    $return_game= mysql_fetch_assoc($game_random['p']);
            }



            header('Content-Type:application/json');
            echo json_encode($return_game);
    }

    public function contactUsSend($request) {
            $res=array();

            $contact=$request['contact'];
            $subject="Contact us Wheeldo.com";
            $parameters=array(
                    "ip" => $_SERVER['REMOTE_ADDR'],
                    "time" =>date("d/m/Y H:i",time()),
                    "name" => $contact['name'],
                    "email" => $contact['email'],
                    "message"=>$contact['message']
            );


            $templateName="contact_us";
            $body=file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
            foreach($parameters as $key=>$value):
                            $body=str_replace("[".$key."]", $value,$body);
            endforeach;


            $task = new PushTask('/send_email_processing', ['from'=>"Wheeldo system",'email'=>"info@wheeldo.com",'subject'=>$subject,'body'=>$body]);
            $task_name = $task->add();

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function sendMeVoucher($request) {
            $res=array();
            $gid=$request['gid'];
            $user_id=$_SESSION['login_user']['ID'];

            $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
            $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");

            @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
            $quiz_design=$this->selectAssocRow("quiz_design","WHERE `game_id`='{$game['id']}'");
            @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );

            $voucher_no=$gid."-".$user['unique_link'];

            if(isset($_COOKIE[$voucher_no])) {
                    $res['status']="faild";
                    $res['error']="Voucher already sent";
                    header('Content-Type:application/json');
                    echo json_encode($res);
                    return;
            }

            setcookie($voucher_no, 1, time()+3600*24*365*10);

            $subject="Check out this game";
            $parameters=array(
                    "banner" => $quiz_design['banner'],
                    "name" => $user['fname']." ".$user['lname'],
                    "voucher_no" => $voucher_no,
                    "content" => $game['voucher_email_content']
            );

            $subject="Coupon ::: ".$game['voucher_email_subject'];


            $templateName="voucher";
            if(isset($request['lang'])&&$request['lang']=="he") {
                    $templateName="voucher_he";
            }
            $body=file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
            foreach($parameters as $key=>$value):
                            $body=str_replace("[".$key."]", $value,$body);
            endforeach;



            $task = new PushTask('/send_email_processing', ['from'=>$parameters['name'],'email'=>$user['email'],'subject'=>$subject,'body'=>$body]);
            $task_name = $task->add();
            $res['status']="ok";

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function sendToFriend($request) {
            $res=array();
            $gid=$request['gid'];
            $user_id=$_SESSION['login_user']['ID'];

            $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");

            $reg_data=$request['reg_data'];

            $check=$this->selectAssocRow("users","WHERE `email`='{$reg_data['email']}'");
            if(!$check) {
                    $fields=array();
                    $fields['fname']=$reg_data['fname'];
                    $fields['lname']=$reg_data['lname'];
                    $fields['email']=$reg_data['email'];
                    $fields['password']="";
                    $fields['image']="/media/img/u_default.jpg";
                    $fields['activation_link']=sha1(time());
                    $fields['unique_link']=substr(sha1(time()),0,10);
                    $fields['reg_time']=time();
                    $fields['ghost']=0;
                    $new_user_id=$this->insertDB("users",$fields);
                    $invited_unique_link=$fields['unique_link'];
            }
            else{
                    $new_user_id=$check['id'];
                    $invited_unique_link=$check['unique_link'];
            }

            $new_user=$this->selectAssocRow("users","WHERE `id`='{$new_user_id}'");

            //create row:
            @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
            $key=$gid."_".$user['unique_link']."_".$invited_unique_link;
            $check_if_inv_sent=$this->selectAssocRow("game_invitations","WHERE `key`='{$key}'");
            if(!$check_if_inv_sent) {
                    $fields=array(
                            "key"=>$key,
                            "time"=>time()
                    );
                    $this->insertDB("game_invitations",$fields);
            }
            @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );

            $fixed_link="http://www.wheeldo.co/play/{$gid}?u={$invited_unique_link}&inv={$user['unique_link']}";

            if(!$check_if_inv_sent):
                    $subject="Check out this game";
                    $parameters=array(
                            "sender_name" => $user['fname']." ".$user['lname'],
                            //"game_name" =>
                            "name" => $new_user['fname']." ".$new_user['lname'],
                            "game_link" => $fixed_link
                    );

                    $templateName="invite_friend";
                    if(isset($request['lang'])&&$request['lang']=="he") {
                            $templateName="invite_friend_he";
                    }
                    $body=file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
                    foreach($parameters as $key=>$value):
                                    $body=str_replace("[".$key."]", $value,$body);
                    endforeach;

                    $task = new PushTask('/send_email_processing', ['from'=>$user['fname']." ".$user['lname'],'email'=>$reg_data['email'],'subject'=>$subject,'body'=>$body]);
                    $task_name = $task->add();
            endif;

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function subscribeToggle($request) {
            $res=array();
            $cid=$request['cid'];
            $user_id=$_SESSION['login_user']['ID'];

            if($_SESSION['login_user']['ghost']=="1"){
                    $res['subscribe']=0;
            }
            else {
                    if(isset($request['forceState']))
                        // forceState will either be sent as 0 or 1
                        // if forceState is 1, then we want $check to evaluate to false,
                        // so cast it as a bool and take the opposite value
                        $check = !(bool)intval($request['forceState']);
                    else {
                $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");
                $check=$this->selectAssocRow("channel_user","WHERE `user_id`='{$user_id}' AND `channel_id`='{$channel['id']}'");
                    }

                    if($check) {
                            $this->deleteDB("channel_user", $check['id']);
                            $res['subscribe']=0;
                    }
                    else {
                            $fields=array();
                            $fields['user_id']=$user_id;
                            $fields['channel_id']=$channel['id'];
                            $this->insertDB("channel_user", $fields);
                            $res['subscribe']=1;
                    }
            }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function checkSubscribe($request) {
            $cid=$request['cid'];

            $res=array();
            if($_SESSION['login_user']['ghost']=="1"){
                    $res['subscribe']=0;
            }
            else{
                    $user_id=$_SESSION['login_user']['ID'];
                    $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");
                    $res['cid'] = $cid;
                    $res['user_id'] = $user_id;
                    $res['channel_id'] = $channel['id'];
                    $check=$this->selectAssocRow("channel_user","WHERE `user_id`='{$user_id}' AND `channel_id`='{$channel['id']}'");
                    $res['subscribe']=$check?1:0;
            }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function getMyChannels() {
            $res=array();
            $user_id=$_SESSION['login_user']['ID'];

            $ans=$this->selectDB("channels","WHERE `user_id`='{$user_id}'");
            $res['channels']=array();
            for($i=0;$i<$ans['n'];$i++) {
              $row=mysql_fetch_assoc($ans['p']);
              $res['channels'][]=$row;
            }

            $user_email=$_SESSION['login_user']['email'];
            $ans=$this->selectDB("channle_admin","WHERE `email`='{$user_email}'");
            $res['shared_channels']=array();
            for($i=0;$i<$ans['n'];$i++) {
              $row=mysql_fetch_assoc($ans['p']);


              $res['shared_channels'][]=$this->selectAssocRow("channels","WHERE `id`='{$row['channle_id']}'");
            }

            //        $ans=$this->selectDB("channels","WHERE `user_id`='{$user_id}'");
            //        for($i=0;$i<$ans['n'];$i++) {
            //          $row=mysql_fetch_assoc($ans['p']);
            //          $res[]=$row;
            //        }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function setSMOpen($request) {
            $res=array();
            $val=$request['val'];
            setcookie("sm_open", $val, time()+3600*24*365*10);
            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function getArticle($request) {
            $res=array();
            $article_name=$request['article_name'];
            $check=$this->selectAssocRow("articles","WHERE `article_name`='{$article_name}'");
            $res=$check;
            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function forgotPassword($request) {
            $res=array();
            $email=$request['email'];
            $new_password=substr(sha1(time()),0,10);

            $fields=array();
            $fields['password']=create_pa($new_password);

            $check=$this->selectAssocRow("users","WHERE `email`='{$request['email']}'");

            $name=$check['fname']." ".$check['lname'];

            if($check) {
                    $this->updateDB("users",$fields,$check['id']);
                    $parameters=array(
                            "name" => $name,
                            "new_password" => $new_password
                    );

                    $templateName="password_recovery";
                    $body=file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
                    foreach($parameters as $key=>$value):
                                    $body=str_replace("[".$key."]", $value,$body);
                    endforeach;

                    $subject="Password recovery ::: Wheeldo";
                    //email::$localMachine=$local;
                    //email::sendMyEmailPlease("Wheeldo system",$email,$subject,$body);
                    $task = new PushTask('/send_email_processing', ['from'=>"Wheeldo system",'email'=>$email,'subject'=>$subject,'body'=>$body]);
                    $task_name = $task->add();

                    $res['status']="ok";
            }
            else {
                    $res['status']="faild";
                    $res['error']="Email does not exist!";
            }


            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function getChannel($request) {
            $res=array();
            $cid=$request['cid'];
            $user_id=$_SESSION['login_user']['ID'];
            $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");
            $user=$this->selectAssocRow("users","WHERE `id`='{$channel['user_id']}'");
            $res['channel']=$channel;
            $res['channel']['user_image']=$user['image'];

            $res['your_channel']=0;
            if($channel['user_id']==$user_id){
                    $res['your_channel']=1;
            }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function getGame($request) {
            global $dbop;
            $res=array();
            $gid=$request['gid'];

            $sql="SELECT
            games.id,
            games.`owner`,
            games.unique_id,
            games.time,
            games.private,
            games.open_status,
            games.game_type,
            games.game_template,
            games.plays,
            games.`name`,
            games.game_type,
            games.thumbnail,
            games.full_desc,
            games.call_action_text,
            games.call_action_link,
            users.fname AS user_fname,
            users.lname AS user_lname,
            users.image AS user_image,
            users.unique_link,
            channels.unique_id AS cid,
            channels.`name` AS cname
            FROM
            games
            INNER JOIN users ON games.`owner` = users.id
            INNER JOIN game_channel ON games.id = game_channel.game_id
            INNER JOIN channels ON game_channel.channel_id = channels.id
            WHERE
            games.unique_id = '$gid'";
            //die($sql);
            $ans=$dbop->selectQDB($sql);
            $game=mysql_fetch_assoc($ans['p']);

            $game['u_time']=$this->timeToWords($game['time']);
            $game['channel_name'] = $game['cname'];
            $game['channel_url'] = "/channel/".$game['cid'];

            if($game)
                    $res=$game;

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    private function timeToWords($time_ago) {
            $diff_time=time()-$time_ago;

            // years:
            $c_year=floor($diff_time/3600/24/365);
            if($c_year>0) {
                    $str="";
                    $str.=$c_year." ";
                    $str.=($c_year>1)?"years":"year";
                    $str.=" ago";
                    return $str;
            }
            /////////

            // months:
            $c_month=floor($diff_time/3600/24/30);
            if($c_month>0) {
                    $str="";
                    $str.=$c_month." ";
                    $str.=($c_month>1)?"months":"month";
                    $str.=" ago";
                    return $str;
            }
            /////////


            // days:
            $c_day=floor($diff_time/3600/24);
            if($c_day>0) {
                    $str="";
                    $str.=$c_day." ";
                    $str.=($c_day>1)?"days":"day";
                    $str.=" ago";
                    return $str;
            }
            /////////

            // hours:
            $c_hour=floor($diff_time/3600);
            if($c_hour>0) {
                    $str="";
                    $str.=$c_hour." ";
                    $str.=($c_hour>1)?"hours":"hour";
                    $str.=" ago";
                    return $str;
            }
            /////////

            //return date("d/m/Y",$time_ago);

            return "Just now";
    }
    
    public function getGameTypeName($game_type) {
        switch($game_type):
            case 1:
                return "Pro Quiz";

            case 2:
                return "Competition Trivia";
            
            case 3:
                return "Personality Test";
                
            case 4:
                return "Learning Quiz";
        endswitch;
        
    }
    
    private function duplicateGame($source_id,$target_user_id,$target_channel_id) {
        $game=$this->selectAssocRow("games","WHERE `id`='{$source_id}'");
        $game['plays']=0;
        $game['time']=time();
        $game['owner']=$target_user_id;
        $game['unique_id']=substr(sha1(time()),0,10);
        unset($game['id']);
        $new_game_id=$this->insertDB("games",$game);
        
        // insert to channel: 
        $this->insertDB("game_channel", array(
            "game_id"=>$new_game_id,
            "channel_id"=>$target_channel_id
        ));
        /////////////////////

        
        // duplicate game in gamedata:
        
        //games:
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $game=$this->selectAssocRow("games","WHERE `game_id`='{$source_id}'");
        if($game):
            unset($game['id']);
            $game['game_id']=$new_game_id;
            $this->insertDB("games",$game);
        endif;
        ////////

        //quiz_design:
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $quiz_design=$this->selectAssocRow("quiz_design","WHERE `game_id`='{$source_id}'");
        if($quiz_design):
            unset($quiz_design['id']);
            $quiz_design['game_id']=$new_game_id;
            $this->insertDB("quiz_design",$quiz_design);
        endif;
        //////////////
        
        //quiz_game:
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectDB("quiz_game","WHERE `game_id`='{$source_id}'");
        for($i=0;$i<$ans['n'];$i++) {
           $row=mysql_fetch_assoc($ans['p']);
           unset($row['id']);
           $row['game_id']=$new_game_id;
           $this->insertDB("quiz_game",$row);
        }
        ////////////
        
        //quiz_design:
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $quiz_settings=$this->selectAssocRow("quiz_settings","WHERE `game_id`='{$source_id}'");
        if($quiz_settings):
            unset($quiz_settings['id']);
            $quiz_settings['game_id']=$new_game_id;
            $this->insertDB("quiz_settings",$quiz_settings);
        endif;
        //////////////
        
        //test_yourself_data:
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $test_yourself_data=$this->selectAssocRow("test_yourself_data","WHERE `game_id`='{$source_id}'");
        if($test_yourself_data):
            unset($test_yourself_data['id']);
            $test_yourself_data['game_id']=$new_game_id;
            $this->insertDB("test_yourself_data",$test_yourself_data);
        endif;
        //////////////
        
        
        //test_yourself_data_q_game:
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectDB("test_yourself_data_q_game","WHERE `game_id`='{$source_id}'");
        for($i=0;$i<$ans['n'];$i++) {
           $row=mysql_fetch_assoc($ans['p']);
           unset($row['id']);
           $row['game_id']=$new_game_id;
           $this->insertDB("test_yourself_data_q_game",$row);
        }
        ////////////////////////////
        
        
        //test_yourself_data_results:
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectDB("test_yourself_data_results","WHERE `game_id`='{$source_id}'");
        for($i=0;$i<$ans['n'];$i++) {
           $row=mysql_fetch_assoc($ans['p']);
           unset($row['id']);
           $row['game_id']=$new_game_id;
           $this->insertDB("test_yourself_data_results",$row);
        }
        ////////////////////////////
 
    }

    private function loadJsonGameBox($sql) {
            $ans=$this->selectQDB($sql);


            if($ans['n']==0) {

                    $sql="SELECT
                    games.id,
                    games.unique_id,
                    games.time,
                    games.plays,
                    games.`name`,
                    games.game_type,
                    games.thumbnail,
                    games.prize,
                    games.prize_text,
                    games.prize_time_limit,
                    games.winner,
                    games.full_desc,
                    users.fname,
                    users.lname,
                    users.image,
                    users.unique_link,
                    channels.unique_id AS cid,
                    channels.`name` AS cname,
                    channels.small_icon,
                    users.fname AS user_fname,
                    users.lname AS user_lname
                    FROM
                    channels
                    INNER JOIN game_channel ON game_channel.channel_id = channels.id
                    INNER JOIN games ON game_channel.game_id = games.id
                    INNER JOIN users ON games.`owner` = users.id
                    WHERE games.id IN (307)";
                    $ans=$this->selectQDB($sql);
            }

            for($i=0;$i<$ans['n'];$i++) {
              @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );
              $row=mysql_fetch_assoc($ans['p']);
              $game_id=$row['id'];
              $res[$i]['id']=$row['id'];
              $res[$i]['name']=$row['name'];                  
              $res[$i]['game_type']=$row['game_type']; 
              $res[$i]['game_type_name']=$this->getGameTypeName($row['game_type']); 
              $res[$i]['full_desc']=$row['full_desc'];
              $res[$i]['full_desc']=(strlen($row['full_desc']) > 353) ? substr($row['full_desc'],0,350).'...' : $row['full_desc'];
              $res[$i]['user_fname']=$row['user_fname'];
              $res[$i]['user_lname']=$row['user_lname'];
              $res[$i]['image']=$row['image'];
              $res[$i]['small_icon']=$row['small_icon'];
              $res[$i]['plays']=$row['plays'];
              $res[$i]['u_time']=$this->timeToWords($row['time']);
              $res[$i]['link']="/play/".$row['unique_id'];
              $res[$i]['unique_id']=$row['unique_id'];
              $res[$i]['channel_name']=$row['cname'];
//		  if(strlen($row['cname']) > 27)
//			   $res[$i]['channel_name'] = substr($row['cname'],0,27) . "...";



              $res[$i]['channel']="/channel/".$row['cid'];

              $res[$i]['thumbnail']=$row['thumbnail']!=""?$row['thumbnail']:"";

              //@mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );

//		  // prize:
//		  $res[$i]['winner']=$row['winner'];
//		  $res[$i]['prize']=0;
//		  if($row['prize']=="1") {
//			  $res[$i]['prize']=1;
//			  $res[$i]['prize_time_limit']=$row['time']+(int)$row['prize_time_limit']*3600-time();
//		  }
//
//		  // $game_id
//		  $leader=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `ghost`='0' ORDER BY `score` DESC");
//		  $res[$i]['leader']=$leader?$leader['user_name']:"No leader";
            }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function getSuggestedChannel($request) {
            global $dbop;
            $res=array();
            $cid=$request['cid'];
            $sql="SELECT
                    games.id,
                    games.unique_id,
                    games.time,
                    games.plays,
                    games.`name`,
                    games.game_type,
                    games.thumbnail,
                    games.prize,
                    games.prize_text,
                    games.prize_time_limit,
                    games.winner,
                    games.full_desc,
                    users.fname,
                    users.lname,
                    users.image,
                    users.unique_link,
                    channels.unique_id AS cid,
                    channels.`name` AS cname,
                    channels.small_icon,
                    users.fname AS user_fname,
                    users.lname AS user_lname
                    FROM
                    channels
                    INNER JOIN game_channel ON game_channel.channel_id = channels.id
                    INNER JOIN games ON game_channel.game_id = games.id
                    INNER JOIN users ON games.`owner` = users.id
                    WHERE games.private=0 AND
                    channels.unique_id = '$cid'
                    ORDER BY games.time DESC";
            $this->loadJsonGameBox($sql);
    }

    public function getSuggested() {
            global $dbop;
            $res=array();

            $r=$this->selectAssocRow("games_rows","WHERE `row`='Recommended'");

            $ids=json_decode($r['game_ids'],true);

            $ids_limit=array();
            $c=0;
            foreach($ids as $id):
                    if($c>=(int)$r['no'])
                            break;
                    $ids_limit[]=$id;
                    $c++;
            endforeach;

            $ids_in=implode(",",$ids_limit);

            $sql="SELECT
            games.id,
            games.unique_id,
            games.time,
            games.plays,
            games.`name`,
            games.game_type,
            games.thumbnail,
            games.prize,
            games.prize_text,
            games.prize_time_limit,
            games.winner,
            games.full_desc,
            users.fname,
            users.lname,
            users.image,
            users.unique_link,
            channels.unique_id AS cid,
            channels.`name` AS cname,
            channels.small_icon
            FROM
            games
            INNER JOIN users ON games.`owner` = users.id
            INNER JOIN game_channel ON games.id = game_channel.game_id
            INNER JOIN channels ON game_channel.channel_id = channels.id
            WHERE games.id IN ($ids_in)
            ORDER BY games.time DESC";
            $this->loadJsonGameBox($sql);
    }

    public function getPopular() {
            global $dbop;
            $res=array();

            $r=$this->selectAssocRow("games_rows","WHERE `row`='Popular'");

            $ids=json_decode($r['game_ids'],true);

            $ids_limit=array();
            $c=0;
            foreach($ids as $id):
                    if($c>=(int)$r['no'])
                            break;
                    $ids_limit[]=$id;
                    $c++;
            endforeach;

            $ids_in=implode(",",$ids_limit);

            $sql="SELECT
            games.id,
            games.unique_id,
            games.time,
            games.plays,
            games.`name`,
            games.game_type,
            games.thumbnail,
            games.prize,
            games.prize_text,
            games.prize_time_limit,
            games.winner,
            games.full_desc,
            users.fname,
            users.lname,
            users.image,
            users.unique_link,
            channels.unique_id AS cid,
            channels.`name` AS cname,
            channels.small_icon
            FROM
            games
            INNER JOIN users ON games.`owner` = users.id
            INNER JOIN game_channel ON games.id = game_channel.game_id
            INNER JOIN channels ON game_channel.channel_id = channels.id
            WHERE games.id IN ($ids_in)
            ORDER BY games.time ASC
            LIMIT 0,8";
            $this->loadJsonGameBox($sql);
    }

    public function getFeatured() {
            global $dbop;
            $res=array();

            $r=$this->selectAssocRow("games_rows","WHERE `row`='Featured'");

            $ids=json_decode($r['game_ids'],true);


            $ids_limit=array();
            $c=0;
            foreach($ids as $id):
                    if($c>=(int)$r['no'])
                            break;
                    $ids_limit[]=$id;
                    $c++;
            endforeach;

            $ids_in=implode(",",$ids_limit);

            $sql="SELECT
            games.id,
            games.unique_id,
            games.time,
            games.plays,
            games.`name`,
            games.game_type,
            games.thumbnail,
            games.prize,
            games.prize_text,
            games.prize_time_limit,
            games.winner,
            games.full_desc,
            users.fname,
            users.lname,
            users.image,
            users.unique_link,
            channels.unique_id AS cid,
            channels.`name` AS cname,
            channels.small_icon
            FROM
            games
            INNER JOIN users ON games.`owner` = users.id
            INNER JOIN game_channel ON games.id = game_channel.game_id
            INNER JOIN channels ON game_channel.channel_id = channels.id
            WHERE games.id IN ($ids_in)
            ORDER BY games.time ASC
            LIMIT 0,8";
            $this->loadJsonGameBox($sql);
    }

    public function getMy() {
            global $dbop;
            $res=array();
            if(!isset($_SESSION['login_user']))
                    return;
            $user_id=$_SESSION['login_user']['ID'];


            $sql="SELECT
            games.id,
            games.unique_id,
            games.time,
            games.plays,
            games.`name`,
            games.game_type,
            games.thumbnail,
            games.prize,
            games.prize_text,
            games.prize_time_limit,
            games.winner,
            games.full_desc,
            users.fname,
            users.lname,
            users.image,
            users.unique_link,
            channels.unique_id AS cid,
            channels.`name` AS cname,
            channels.small_icon
            FROM
            games
            INNER JOIN users ON games.`owner` = users.id
            INNER JOIN game_channel ON games.id = game_channel.game_id
            INNER JOIN channels ON game_channel.channel_id = channels.id
            WHERE games.owner = {$user_id}
            ORDER BY games.time DESC";
            $ans=$this->selectQDB($sql);
            
            if($ans['n']==0) {
            //if(false){
                // duplicate game in case the game are is empty:
                if(!isset($_SESSION['login_user']))
                    return;
                $user_id=$_SESSION['login_user']['ID'];
                $ans=$this->selectDB("channels","WHERE `user_id`='{$user_id}'");
                $res['channels']=array();
                for($i=0;$i<$ans['n'];$i++) {
                  $row=mysql_fetch_assoc($ans['p']);
                  $channel_id=$row['id'];
                  break;
                }
                $this->duplicateGame(771,$user_id,$channel_id);
                //$this->getMy();
                return;
                ////////////////////////////////////////////////
            }
            
            
            
            $this->loadJsonGameBox($sql);
            
    }

    public function signOut() {
            $res=array();
            global $dbop;
            $user_data=$dbop->selectAssocRow("users","WHERE `id`='{$_SESSION['login_user']['ID']}'");
            if($user_data['ghost']!="1") {
                    setcookie("user_login", "", time()-3600);
                    unset($_SESSION['login_user']);
            }
            $res['status']="ok";

            //var_dump($_SESSION['login_user']);
            header('Content-Type:application/json');
            echo json_encode($res);
    }

    private function checkIfChannelExists() {

            if(isset($_SESSION['login_user'])) {

                    $user_id=$_SESSION['login_user']['ID'];
                    $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");

                    if($user['ghost']!="1"):
                            $check=$this->selectAssocRow("channels","WHERE `user_id`='{$user_id}'");
                            if(!$check):
                                    // create channel:
                                    $fields=array();
                                    $fields['unique_id']=substr(sha1(time()),0,10);
                                    $fields['user_id']=$user['id'];
                                    $fields['user_private_channel']=1;
                                    $fields['name']=ucfirst($user['fname'])." ".ucfirst($user['lname']);
                                    $fields['description']="";
                                    $fields['small_icon']="media/css/dice/img/u_default.jpg";
                                    $fields['cover']="/media/img/deafult_banner.jpg";
                                    $fields['time']=time();
                                    $this->insertDB("channels",$fields);
                                    //////////////////
                            endif;
                    endif;
            }
    }

    public function getUser($request) {
            global $dbop;


            $cookie_data=isset($_COOKIE["user_login"])?json_decode($_COOKIE["user_login"],true):false;

            if(isset($request['u'])&&$request['u']!="0"&&$user_data=$dbop->selectAssocRow("users","WHERE `unique_link`='{$request['u']}'")) {
                    $user['ID']=$user_data['id'];
                    $user['uid']=$user_data['unique_link'];
                    $user['name']=$user_data['fname']." ".$user_data['lname'];
                    $user['email']=$user_data['email'];
                    $user['image']=$user_data['image'];
                    $user['ghost']=$user_data['ghost'];
                    $_SESSION['login_user']=$user;
                    setcookie("user_login", json_encode($user), time()+3600*24*182);
            }
            elseif(isset($_SESSION['login_user'])&&$user_data=$dbop->selectAssocRow("users","WHERE `id`='{$_SESSION['login_user']['ID']}'")) {
                    
                    $user['ID']=$user_data['id'];
                    $user['uid']=$user_data['unique_link'];
                    $user['name']=$user_data['fname']." ".$user_data['lname'];
                    $user['email']=$user_data['email'];
                    $user['image']=$user_data['image'];
                    $user['ghost']=$user_data['ghost'];
                    setcookie("user_login", json_encode($user), time()+3600*24*182);
            }
            elseif($cookie_data && $cookie_data['ID']>0 && $user_data=$dbop->selectAssocRow("users","WHERE `id`='{$cookie_data["ID"]}'")) {
                    $user['ID']=$user_data['id'];
                    $user['uid']=$user_data['unique_link'];
                    $user['name']=$user_data['fname']." ".$user_data['lname'];
                    $user['email']=$user_data['email'];
                    $user['image']=$user_data['image'];
                    $user['ghost']=$user_data['ghost'];
                    $_SESSION['login_user']=$user;
                    setcookie("user_login", json_encode($user), time()+3600*24*182);
            }
            else {

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
                    ////////////////////

                    $user_data=$dbop->selectAssocRow("users","WHERE `id`='{$user_id}'");
                    $user['ID']=$user_data['id'];
                    $user['uid']=$user_data['unique_link'];
                    $user['name']=$user_data['fname']." ".$user_data['lname'];
                    $user['email']=$user_data['email'];
                    $user['image']=$user_data['image'];
                    $user['ghost']=$user_data['ghost'];
                    $_SESSION['login_user']=$user;
                    setcookie("user_login", json_encode($user), time()+3600*24*182);
            }

            $this->checkIfChannelExists();

            // get channels:

            $user['my_channels']=array();

            $ans=$dbop->selectDB("channels","WHERE `user_id`='{$user['ID']}'");
            for($i=0;$i<$ans['n'];$i++) {
              $row=mysql_fetch_assoc($ans['p']);
              $channel=$row;
              $channel['owner']=1;
              $user['my_channels'][]=$channel;
            }


            $user_email=$user_data['email'];
            $ans=$this->selectDB("channle_admin","WHERE `email`='{$user_email}'");
            $res['shared_channels']=array();
            for($i=0;$i<$ans['n'];$i++) {
              $row=mysql_fetch_assoc($ans['p']);
              $channel=$this->selectAssocRow("channels","WHERE `id`='{$row['channle_id']}'");
              $channel['owner']=0;
              $user['my_channels'][]=$channel;
            }


            if($user_data && $user_data['ip']=="") {
                $r=json_decode(file_get_contents("http://ipinfo.io/".$_SERVER['REMOTE_ADDR']."/json"),true);
                $this->updateDB("users",array("ip"=>$_SERVER['REMOTE_ADDR'],"country"=>$r['country']),$user_data['id']);
            }


            // check for premium
            $user['premium']=0;
            if($user_data['admin']=="1") {
                    $user['premium']=1;
            }

            echo json_encode($user);
    }

    public function signInUser($request) {
            global $dbop;
            $res=array();

            unset($_SESSION['login_user']);
            setcookie("user_login", "", time()-3600);

            $res['status']="faild";

            $user=$dbop->selectAssocRow("users","WHERE `email`='{$request['email']}' AND `active`='1'");
            if($user) {
                    $inputPasswort=create_pa(trim($request['password']));
                    $userPassword=$user['password'];

                    if(is_equal_pa($userPassword,$inputPasswort)) {
                            // reg proccess:



                            $user_data['ID']=$user['id'];
                            $user_data['uid']=$user['unique_link'];
                            $user_data['name']=$user['fname']." ".$user_data['lname'];
                            $user_data['email']=$user['email'];
                            $user_data['image']=$user['image'];
                            $user_data['ghost']=$user['ghost'];
                            $_SESSION['login_user']=$user_data;
                            setcookie("user_login", json_encode($user), time()+3600*24*182);
                            setcookie("user_unique", $user['unique_link'], time()+3600*24*365*2);




                            ////////////////
                            $res['status']="ok";
                    }
            }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function activateAccount($request, $output = true) {
            global $dbop;
            $res=array();
            $link=$request['link'];

            $check=$dbop->selectAssocRow("users","WHERE `activation_link`='{$link}'");
            if($check) {
                    $dbop->updateDB("users",array("active"=>1,"activation_link"=>""),$check['id']);

                    // create channel:
                    $fields=array();
                    $fields['unique_id']=substr(sha1(time()),0,10);
                    $fields['user_id']=$check['id'];
                    $fields['user_private_channel']=1;
                    $fields['name']=ucfirst($check['fname'])." ".ucfirst($check['lname']);
                    $fields['description']="";
                    $fields['small_icon']="media/css/dice/img/u_default.jpg";
                    $fields['cover']="/media/img/deafult_banner.jpg";
                    $fields['time']=time();
                    $dbop->insertDB("channels",$fields);
                    //////////////////

                    $res['status']="ok";

                    // login user:
                    setcookie("user_login", "", time()-3600);
                    unset($_SESSION['login_user']);

                    $user_data=$check;
                    $user['ID']=$user_data['id'];
                    $user['uid']=$user_data['unique_link'];
                    $user['name']=$user_data['fname']." ".$user_data['lname'];
                    $user['email']=$user_data['email'];
                    $user['image']=$user_data['image'];
                    $user['ghost']=$user_data['ghost'];
                    $_SESSION['login_user']=$user;
                    setcookie("user_login", json_encode($user), time()+3600*24*182);

                    //////////////
            }
            else {
                    $res['status']="faild";
                    $res['error']=mysql_error();
            }

            if($output) {
            header('Content-Type:application/json');
            echo json_encode($res);
            }
            else
                    return $res;
    }

    private function sendActivationEmail($user_id,$auto_generated_password=false) {
            global $dbop;
            $user=$dbop->selectAssocRow("users","WHERE `id`='{$user_id}'");
            $email=$user['email'];
            $activation_link="http://".$_SERVER['HTTP_HOST']."/activation/".$user['activation_link'];
            $name=$user['fname']." ".$user['lname'];

            $parameters=array(
                    "name" => $name,
                    "activation_link" => $activation_link
            );
            
            
            if($auto_generated_password) {
                $parameters['password']="Your temporary password is: <strong>".$auto_generated_password."</strong><br><br>";
            }

            $templateName="registration";
            $body=file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
            foreach($parameters as $key=>$value):
                            $body=str_replace("[".$key."]", $value,$body);
            endforeach;

            $subject="Thank you for registration with Wheeldo";
            //email::$localMachine=$local;
            //var_dump($user);
            $task = new PushTask('/send_email_processing', ['from'=>"Wheeldo system",'email'=>$email,'subject'=>$subject,'body'=>$body]);
            $task_name = $task->add();
            //email::sendMyEmailPlease("Wheeldo system",$email,$subject,$body);
    }

    private function notify_new_registration($user_id,$reg_source=false) {
            global $dbop;
            $user=$dbop->selectAssocRow("users","WHERE `id`='{$user_id}'");
            $email=$user['email'];
            $name=$user['fname']." ".$user['lname'];
            $reg_time = date("d/m/Y",$user['reg_time']);//;
            
            
            $source="";
            if($reg_source) {
                $source='<p>Registration source: '.$reg_source.'</p><br />';                                                     
            }

            $parameters=array(
                    "name" => $name,
                    "email" => $email,
                    "source" =>$source,
                    "reg_time" => $reg_time
            );
            $templateName="new_registration";
            $body=file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
            foreach($parameters as $key=>$value):
                            $body=str_replace("[".$key."]", $value,$body);
            endforeach;
            $subject="New user registration!";
            //email::$localMachine=$local;
            $notify_emails = array();
            $ans=$dbop->selectDB("users","WHERE `notified_new_registration`=1");
            for($i=0;$i<$ans['n'];$i++) {
              $row=mysql_fetch_assoc($ans['p']);
              $notify_emails[]=$row['email'];
            }
            if(count($notify_emails)>0){
                    foreach($notify_emails as $email){
                       $task = new PushTask('/send_email_processing', ['from'=>"Wheeldo system",'email'=>$email,'subject'=>$subject,'body'=>$body]);
                       $task_name = $task->add();
                       ///email::sendMyEmailPlease("Wheeldo system",$email,$subject,$body);
                      //file_put_contents('test.txt',$email,FILE_APPEND);
                    }
            }
    }

    public function googleLogin($request) {
            $res = array();
            $res['status'] = 'error';

            $user_data = $request['data'];
            $google_uid = $user_data['id'];

            $user=$this->selectAssocRow("users","WHERE `google_uid`='{$google_uid}'");
            if(!$user) {
                    $data['google_uid'] = $google_uid;
                    $data['fname'] = $user_data['name']['givenName'];
                    $data['lname'] = $user_data['name']['familyName'];
                    $data['email'] = $user_data['emails'][0]['value'];
                    $data['password'] = '';
                    $data['image'] = $user_data['image']['url'];
                    if(isset($request['gid']))
                            $data['gid'] = $request['gid'];

                    if(isset($request['gid'])) {
                            // compensate for the different data format expected by regNewUserGame()
                            $data['reg_data'] = $data;
                            $result = $this->regNewUserGame($data);
                    }
                    else
                            $result = $this->regNewUser($data);

                    return;
            }
            else {
                    $this->doLogin($user['id']);
                    $res['status'] = 'ok';
                    $res['uid'] = $user['unique_link'];
            }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function isFacebookUser($request) {
            $check=$this->selectAssocRow("users","WHERE `facebook_uid`='{$request['facebook_uid']}'");
            $res = array('is_user'=>$check ? true : false);
            $res['query'] = $request['facebook_uid'];

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function loginFacebookUser($request) {
            global $request, $session;

            $res = array();

            require_once('fb_init.php');

            if ($session) {
                    // graph api request for user data
                    $request = new FacebookRequest( $session, 'GET', '/me?fields=id' );
                    $response = $request->execute();
                    $graphObject = $response->getGraphObject()->asArray();

                    $user=$this->selectAssocRow("users","WHERE `facebook_uid`='{$graphObject['id']}'");

                    if($user) {
                            $this->doLogin($user['id']);

                            $res['status'] = 'ok';
                            $res['uid'] = $user['unique_link'];
                    }
                    else {
                            $res['status'] = 'error';
                    }
            }
            else {
                    $res['status'] = 'error';
                    $res['error'] = 'Your profile could not be found';
            }

            header('Content-Type:application/json');
            echo json_encode($res);
    }

    private function doLogin($user_id) {
            setcookie("user_login", "", time()-3600);
            unset($_SESSION['login_user']);

            $user_data=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");

            $user['ID']=$user_data['id'];
            $user['uid']=$user_data['unique_link'];
            $user['name']=$user_data['fname']." ".$user_data['lname'];
            $user['email']=$user_data['email'];
            $user['image']=$user_data['image'];
            $user['ghost']=$user_data['ghost'];

            //print_r($user);

            $_SESSION['login_user']=$user;
            setcookie("user_login", json_encode($user), time()+3600*24*182);
    }

    public function registerFacebookUser($request) {
            global $request, $session;

            require_once('fb_init.php');

            $data = array();
            $res = array();

            if ($session) {
                    // graph api request for user data
                    $request = new FacebookRequest( $session, 'GET', '/me?fields=id,first_name,last_name,email' );
                    $response = $request->execute();
                    $graphObject = $response->getGraphObject()->asArray();

                    //print_r($graphObject);

                    $data['facebook_uid'] = $graphObject['id'];
                    $data['fname'] = $graphObject['first_name'];
                    $data['lname'] = $graphObject['last_name'];
                    $data['email'] = $graphObject['email'];
                    $data['password'] = '';
                    $data['image'] = "https://graph.facebook.com/{$graphObject['id']}/picture?type=large";
                    if(isset($request['gid']))
                            $data['gid'] = $request['gid'];

                    if(isset($request['gid'])) {
                            $data['reg_data'] = $data;
                            $result = $this->regNewUserGame($data);
                    }
                    else
                            $this->regNewUser($data);
                    return;

                    //echo '<pre>' . print_r( $graphObject, 1 ) . '</pre>';
                    //echo '<a href="' . $helper->getLogoutUrl( $session, 'http://yourwebsite.com/app/logout.php' ) . '">Logout</a>';
            }
            else {
                    // show login url
                    echo '<a href="' . $helper->getLoginUrl( array( 'email', 'user_friends' ) ) . '">Login</a>';
            }

            header('Content-Type:application/json');
                            echo json_encode($res);
    }
        
    public function signInGame($request) {
        $res=array();

        $si_data=$request['si_data'];
        $user=$this->selectAssocRow("users","WHERE `email`='{$si_data['email']}'");

        if($user){
            $inputPassword=create_pa(trim($si_data['pass']));
            $userPassword=$user['password'];

            if(is_equal_pa($userPassword,$inputPassword)) {
                $this->doLogin($user['id']);
                $res['status']="ok";
                $res['uid']=$user['unique_link'];
            }
            else {
                $res['status']="faild";
            }
        }
        else {
            $res['status']="faild";
        }

        header('Content-Type:application/json');
        echo json_encode($res);

    }

    public function regNewUserGame($request) {
            $res=array();
            $special_case = false;
            $gid=$request['gid'];
            $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");

            $check=$this->selectAssocRow("users","WHERE `email`='{$request['reg_data']['email']}'");
            if($check) {
                    $user = $check;
                    $res['user_id']=0;

                    // is this a request to link an existing account with a Facebook/Google+ profile?
                    if(isset($request['reg_data']['facebook_uid']) && !$user['facebook_uid']) {
                            $special_case = true;

                            $fields['facebook_uid']=$request['reg_data']['facebook_uid'];

                            $check=$dbop->updateDB("users",$fields,$user['id']);
                            if($check) {
                                    $res['status']="ok";
                                    $res['user_id']=$user['id'];
                                    $res['uid']=$user['unique_link'];

                                    if(!$user['active'])
                                            $this->activateAccount(array('link'=>$user['activation_link']), false);

                                    // automatically log the user in
                                    $this->doLogin($user['id']);
                            }
                            else {
                            $res['status']="faild";
                                    $res['error']="Unable to link your account with your Facebook profile";
                            }
                    }
                    elseif(isset($request['reg_data']['google_uid']) && !$user['google_uid']) {
                            $special_case = true;

                            $fields['google_uid']=$request['reg_data']['google_uid'];

                            $check=$dbop->updateDB("users",$fields,$user['id']);
                            if($check) {
                                    $res['status']="ok";
                                    $res['user_id']=$user['id'];
                                    $res['uid']=$user['unique_link'];

                                    if(!$user['active'])
                                            $this->activateAccount(array('link'=>$user['activation_link']), false);

                                    // automatically log the user in
                                    $this->doLogin($user['id']);
                            }
                            else {
                            $res['status']="faild";
                                    $res['error']="Unable to link your account with your Google Plus profile";
                            }
                    }
            }

            if(!$special_case) {
                    $fields=array();
                    $fields['fname']=$request['reg_data']['fname'];
                    $fields['lname']=$request['reg_data']['lname'];
                    $fields['email']=$request['reg_data']['email'];
                    $fields['password']="";
                    $fields['image']=$request['reg_data']['img']?$request['reg_data']['img']:"/media/img/u_default.jpg";
                    $fields['activation_link']=sha1(time());
                    $fields['unique_link']=substr(sha1(time()),0,10);
                    $fields['facebook_uid']=$request['reg_data']['facebook_uid'];
                    $fields['google_uid']=$request['reg_data']['google_uid'];
                    $fields['reg_time']=time();
                    $fields['ghost']=0;




                    if(isset($_SESSION['login_user']['ID']) && $_SESSION['login_user']['ghost']=="1") {
                            $user_id=$_SESSION['login_user']['ID'];
                            $check=$this->updateDB("users",$fields,$_SESSION['login_user']['ID']);
                    }
                    else {
                            $user_id=$this->insertDB("users",$fields);
                            // exp change 28/7/2014
                            $check=true;
                    }


                    // leads fields:
                    $lead_data=array();
                    $lead_data['user_id']=$user_id;
                    $lead_data['fname']=$fields['fname'];
                    $lead_data['lname']=$fields['lname'];
                    $lead_data['email']=$fields['email'];
                    $lead_data['reg_time']=$fields['reg_time'];
                    ////////////////



                    if($check) {

                            //var_dump($user_id);
                            // setcookie("user_login", "", time()-3600);
                            // unset($_SESSION['login_user']);
                            if(!($fields['facebook_uid'] || $fields['google_uid'])){
                                    $this->sendActivationEmail($user_id);
                                    $this->notify_new_registration($user_id,'"'.$game['name'].'" game');
                                    $this->doLogin($user_id);
                            }
                            else {
                                    //$this->notify_new_registration($user_id);
                                    // Facebook users should be auto-activated and auto-logged in
                                    $this->activateAccount(array('link'=>$fields['activation_link']), false);
                                    $this->doLogin($user_id);
                            }

                            $res['status']="ok";
                            $res['uid']=$fields['unique_link'];
                    }
                    else {
                            $res['status']="faild";
                            $res['error']=mysql_error();
                    }

                    //unset($_SESSION['login_user']);
                    //setcookie("user_login", "", time()-3600);

                    //setcookie("user_login", $user_id, time()+3600*24*182);
                    //setcookie("user_unique", $fields['activation_link'], time()+3600*24*365*2);

                    $res['user_id']=$user_id;
            }

            // subscribe channel
            //
            //$res['check']=$request['reg_data']['subscribe']?"ok":"wierd";

            if($request['reg_data']['subscribe']):

                    $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game['id']}'");
                    $channel_id=$game_channel['channel_id'];

                    $check=$this->selectAssocRow("channel_user","WHERE `user_id`='{$user_id}' AND `channel_id`='{$channel_id}'");
                    if(!$check){
                            $fields=array();
                            $fields['user_id']=$user_id;
                            $fields['channel_id']=$channel_id;
                            $id=$this->insertDB("channel_user", $fields);

                    }

            endif;


            // insert new game lead: 
            if(!isset($game['id'])) {
                $gid=$request['gid'];
                $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'"); 
            }
            $game_id=$game['id'];
            $lead_data['game_id']=$game_id;
            $this->insertDB("game_leads", $lead_data);  
            ////////////////////////


            $res['dl']=$_SESSION['login_user'];
            header('Content-Type:application/json');
            echo json_encode($res);
    }

    public function regNewUser($request) {
            global $dbop;

            $fields=array();
            $res=array();
            $special_case = false;

            //echo "<pre>[".print_r($request, true)."]</pre>";

            // check if the user exists, including a few special scenarios
            $user=$dbop->selectAssocRow("users","WHERE `email`='{$request['email']}'");
            if($user) {
                    // is this a request to link an existing account with a Facebook/Google+ profile?
                    if(isset($request['facebook_uid']) && !$user['facebook_uid']) {
                            $special_case = true;

                            $fields['facebook_uid']=$request['facebook_uid'];

                            $check=$dbop->updateDB("users",$fields,$user['id']);
                            if($check) {
                                    $res['status']="ok";

                                    if(!$user['active'])
                                            $this->activateAccount(array('link'=>$user['activation_link']), false);

                                    // automatically log the user in
                                    $this->doLogin($user['id']);
                            }
                            else {
                            $res['status']="faild";
                                    $res['error']="Unable to link your account with your Facebook profile";
                            }
                    }
                    elseif(isset($request['google_uid']) && !$user['google_uid']) {
                            $special_case = true;

                            $fields['google_uid']=$request['google_uid'];

                            $check=$dbop->updateDB("users",$fields,$user['id']);
                            if($check) {
                                    $res['status']="ok";

                                    if(!$user['active'])
                                            $this->activateAccount(array('link'=>$user['activation_link']), false);

                                    // automatically log the user in
                                    $this->doLogin($user['id']);
                            }
                            else {
                            $res['status']="faild";
                                    $res['error']="Unable to link your account with your Google Plus profile";
                            }
                    }
            }

            if(!$special_case) {
                    $fields['fname']=$request['fname'];
                    $fields['lname']=$request['lname'];
                    $fields['email']=$request['email'];
                    $fields['password']=create_pa($request['password']);
                    $fields['image']=$request['image']?$request['image']:"/media/img/u_default.jpg";
                    $fields['facebook_uid']=$request['facebook_uid'];
                    $fields['google_uid']=$request['google_uid'];
                    $fields['activation_link']=sha1(time());
                    $fields['unique_link']=substr(sha1(time()),0,10);
                    $fields['reg_time']=time();
                    $fields['ghost']=0;

                    if(isset($_SESSION['login_user']['ID']) && $_SESSION['login_user']['ghost']=="1") {
                            $check=$dbop->updateDB("users",$fields,$_SESSION['login_user']['ID']);
                            $user_id=$_SESSION['login_user']['ID'];
                    }
                    else {
                            $user_id=$check=$dbop->insertDB("users",$fields);
                    }

                    if($check) {
                            //var_dump($user_id);
                            // setcookie("user_login", "", time()-3600);
                            // unset($_SESSION['login_user']);
                            if(!($fields['facebook_uid'] || $fields['google_uid'])){
                                    $this->sendActivationEmail($user_id);
                                    $this->notify_new_registration($user_id,"ReNewUser function");
                            }
                            else {
                                    //$this->notify_new_registration($user_id);
                                    // Facebook users should be auto-activated and auto-logged in
                                    $this->activateAccount(array('link'=>$fields['activation_link']), false);
                                    $this->doLogin($user_id);
                            }

                            $res['status']="ok";
                    }
                    else {
                            $res['status']="faild";
                            $res['error']=mysql_error();
                    }
            }

            if(!$request['suppress_output']) {
                    header('Content-Type:application/json');
                    echo json_encode($res);
            }
            else {
                    return $res;
            }
    }

    public function checkEmail($request) {
            global $dbop;
            $res=array();
            $check=$dbop->selectAssocRow("users","WHERE `email`='{$request['email']}'");
            if($check)
                    $res['status']="faild";
            else
                    $res['status']="ok";

            header('Content-Type:application/json');
            echo json_encode($res);
    }
        
        
        
    public function gameFormRR($REQ) {
        
        $data=$REQ['data'];
        
        // register user:
        // check for email:
        $new_password=false;
        if(!isset($data['password'])) {
            $new_password=substr(sha1(time()),0,10);
            $pass=create_pa($new_password);
        }
        else {
            $pass=create_pa($data['password']);
        }
        
        
        $check=$this->selectAssocRow("users","WHERE `email`='{$data['email']}'");
        if(!$check) {
            $fields=array();
            $fields['fname']=$data['fname'];
            $fields['lname']=$data['lname'];
            $fields['email']=$data['email'];
            $fields['password']=$pass;
            $fields['image']='/media/img/u_default.jpg';
            $fields['active']=1;
            $fields['reg_time']=time();
            $fields['activation_link']=sha1(time());
            $fields['unique_link']=substr(sha1(time()),0,10);
            $fields['ghost']=0;
            
            $user_id=$this->insertDB("users",$fields);
        }
        else {
            //die("user duplicate!!!!");
            $user_id=$check['id'];
            
            $channels=$this->selectAssocRow("channels","WHERE `user_id`='{$user_id}'");
            $c_id=$channels['id'];
        }
        /////////////////
        
        
        if(!$check) {
            // create channel:
            $fields=array();
            $fields['unique_id']=substr(sha1(time()),0,10);
            $fields['user_id']=$user_id;
            $fields['user_private_channel']=0;
            $fields['name']=$data['fname']." ".$data['lname']." channel";
            $fields['small_icon']="media/css/dice/img/u_default.jpg";
            $fields['cover']="/media/img/deafult_banner.jpg";
            $fields['time']=time();
            $c_id=$this->insertDB("channels",$fields);
            //////////////////

            // welcome email
            $this->sendActivationEmail($user_id,$new_password);
            $this->notify_new_registration($user_id,"Website");
        }
        
        
        // login user:
        $user_data=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        $user['ID']=$user_data['id'];
        $user['uid']=$user_data['unique_link'];
        $user['name']=$user_data['fname']." ".$user_data['lname'];
        $user['email']=$user_data['email'];
        $user['image']=$user_data['image'];
        $user['ghost']=$user_data['ghost'];
        $_SESSION['login_user']=$user;
        setcookie("user_login", json_encode($user), time()+3600*24*182);
        /////////////
        
        
        
        $res=array();
        $res['status']="ok";
        $res['user_id']=$user_id;
        $res['c_id']=$c_id;
        echo json_encode($res);
        
        return;
    }
    
    
    public function loadDomainTopics($request) {
        $res=array();
        $domains=$request['domains'];
        
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        foreach($domains as $domain) {
            $domainlower=strtolower($domain);
            $ans=$this->selectDB("questionnaires_tags","WHERE `tag`='{$domainlower}'");	
            for($i=0;$i<$ans['n'];$i++) {
                $row=mysql_fetch_assoc($ans['p']);
                $questionnaire=$this->selectAssocRow("questionnaires","WHERE `id`='{$row['questionnaire_id']}'");
                if($questionnaire)
                    $res[$domain][]=array("name"=>$questionnaire['name'],"id"=>$row['questionnaire_id']);
            } 
            
        }

        header('Content-Type:application/json');
	echo json_encode($res);
        
    }
}

//echo "<pre>[".print_r($op, true)."]</pre>";
if(!$just_class):
    $gt=new operators();
    $gt->$op($REQ);
endif;
