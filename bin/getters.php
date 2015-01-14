<?php
require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;

require_once 'google/appengine/api/taskqueue/PushTask.php';
use \google\appengine\api\taskqueue\PushTask;

require_once 'top.php';


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

$cookie_data=isset($_COOKIE["user_login"])?json_decode($_COOKIE["user_login"],true):false;
if(isset($_SESSION['login_user'])) {

}
elseif($cookie_data && $cookie_data['ID']>0){
    $_SESSION['login_user']['ID']=$cookie_data["ID"];
}
else {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    die("Blocked!");
}

//$op($REQ);



class getters extends dbop{

    public function __consteuct() {
        return;
    }

    private function stripData($data) {
        $strippedData=str_replace ("___amp___","&",$data);
        return $strippedData;
    }
    
    public function searchUser($request) {
        $searchKey=$request['searchKey'];
        $users=array();
        $sql="SELECT
        users.id,
        users.email,
        users.admin,
        users.fname,
        users.lname,
        users.`password`,
        users.image,
        users.activation_link,
        users.active,
        users.reg_time,
        users.ip,
        users.country,
        users.unique_link,
        users.ghost,
        users.facebook_uid,
        users.google_uid,
        users.notified_new_registration
        FROM
        users
        WHERE
        users.id = '$searchKey' OR
        users.unique_link = '$searchKey' OR
        users.fname LIKE '%$searchKey%' OR
        users.lname = '%$searchKey%' OR
        users.email = '$searchKey'";
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
            $users[$row['id']]=$row['id'].". ".$row['fname']." ".$row['lname']." (".$row['email'].")";
        }
        
        
              
        header('Content-Type:application/json');
        echo json_encode($users);
    }
    
    public function setView($request) {
        $res=array();
        if(isset($_SESSION['user_admin'])) {
            $user_id=$request['user_id'];
            $user_data=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
            $user['ID']=$user_data['id'];
            $user['uid']=$user_data['unique_link'];
            $user['name']=$user_data['fname']." ".$user_data['lname'];
            $user['email']=$user_data['email'];
            $user['image']=$user_data['image'];
            $user['ghost']=$user_data['ghost'];
            $_SESSION['login_user']=$user;
            setcookie("user_login", json_encode($user), time()+3600*24*182);
            $res['status']="ok";
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function resetView($request) {
        if(isset($_SESSION['user_admin'])) {
            $user_data=$this->selectAssocRow("users","WHERE `id`='{$_SESSION['user_admin']}'");
            $user['ID']=$user_data['id'];
            $user['uid']=$user_data['unique_link'];
            $user['name']=$user_data['fname']." ".$user_data['lname'];
            $user['email']=$user_data['email'];
            $user['image']=$user_data['image'];
            $user['ghost']=$user_data['ghost'];
            $_SESSION['login_user']=$user;
            setcookie("user_login", json_encode($user), time()+3600*24*182);
            $res['status']="ok";
        }
    }
    
    public function addChannelAdmin($request) {
       $res=array();
       $channle_id=$request['channle_id'];
       $email=$request['email'];
       
       
       $check=$this->selectAssocRow("channle_admin","WHERE `channle_id`='{$channle_id}' AND`email`='{$email}'");
       if(!$check) {
           $this->insertDB("channle_admin", array(
               "channle_id"=>$channle_id,
               "email"=>$email
           ));
       }

       
       header('Content-Type:application/json');
       echo json_encode($request);
    }
    
    public function removeChannleAdmin($request) {
       $res=array();
       $r_id=$request['r_id'];
       $this->deleteDB("channle_admin",$r_id);
       
       header('Content-Type:application/json');
       echo json_encode($request);
    }
    
    public function loadChannleAdmins($request) {
        $res=array();
        $channle_id=$request['channle_id'];
        
        
        $ans=$this->selectDB("channle_admin","WHERE `channle_id`='{$channle_id}'");

        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res[]=$row;
        }
        
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function load_report_data($request) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        
        $cid=$request['cid'];
        $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");
        
        $game_id=$request['game_id'];
        
        $game=$this->selectAssocRow("games","WHERE `id`='{$game_id}'");
        
        $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game_id}'");
        $channel_id=$game_channel['channel_id'];
        $chceck_shared=$this->selectAssocRow("channle_admin","WHERE `channle_id`='{$channel_id}' AND `email`='{$_SESSION['login_user']['email']}'");
        
        if($game['owner']!=$user_id&&!$chceck_shared)
            die("GFY!");
        
        $sql="SELECT
        users.id,
        users.email,
        users.admin,
        users.fname,
        users.lname,
        users.`password`,
        users.image,
        users.activation_link,
        users.active,
        users.reg_time,
        users.unique_link,
        users.ghost,
        users.facebook_uid
        FROM
        channel_user
        INNER JOIN users ON channel_user.user_id = users.id
        WHERE
        channel_user.channel_id = {$channel['id']}";
        $ans=$this->selectQDB($sql);
        $total_subscribers=$ans['n'];
        
        
        
        
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        
        
        
        $sql="SELECT
        wheeldotube_main.users.id,
        wheeldotube_main.users.email,
        wheeldotube_main.users.admin,
        wheeldotube_main.users.fname,
        wheeldotube_main.users.lname,
        wheeldotube_main.users.`password`,
        wheeldotube_main.users.image,
        wheeldotube_main.users.activation_link,
        wheeldotube_main.users.active,
        wheeldotube_main.users.reg_time,
        wheeldotube_main.users.unique_link,
        wheeldotube_main.users.ghost,
        wheeldotube_main.users.facebook_uid
        FROM
        wheeldotube_main.channel_user
        INNER JOIN wheeldotube_main.users ON wheeldotube_main.channel_user.user_id = wheeldotube_main.users.id
        INNER JOIN wheeldotube_gamedata.game_quiz_user ON wheeldotube_gamedata.game_quiz_user.user_id = wheeldotube_main.users.id
        WHERE
        wheeldotube_main.channel_user.channel_id = {$channel['id']} AND
        wheeldotube_gamedata.game_quiz_user.game_id = {$game_id}";
        
        //echo $sql;
        
        $ans=$this->selectQDB($sql);
        $no_players_from_channel=$ans['n'];
        
        
        
        $ans=$this->selectDB("game_quiz_user","WHERE `game_id`='{$game_id}'");
        $res['players']=$no_players;
        
        
        
        
        $no_players=$ans['n'];
        $res['players']=$no_players;
        
        
        if($total_subscribers>0)
            $res['channel_subscribers']=ceil($no_players_from_channel/$total_subscribers*100);
        else
            $res['channel_subscribers']="N/A";
        
        
        
        $ans=$this->selectDB("game_quiz_user","WHERE `game_id`='{$game_id}'");
        $bounce_total=$ans['n'];
        $bounce_c=0;
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          if($row['finish']=="0")
              $bounce_c++;
        }
        
        if($bounce_total>0)
            $res['bounce']=ceil($bounce_c/$bounce_total*100);
        else
            $res['bounce']="N/A";
        
        
        
        $leaders=array();
        $ans=$this->selectDB("game_quiz_user","WHERE `game_id`='{$game_id}' ORDER BY `score` DESC LIMIT 0,5");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $leaders[]=$row['user_name']." ({$row['score']})";
        }
        
        $res['leaders']=$leaders;
        
        
        header('Content-Type:application/json');
        echo json_encode($res);
        
    }
    
    public function deleteGame($request) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        
        $game_id=$request['game_id'];
        
        $game=$this->selectAssocRow("games","WHERE `id`='{$game_id}'");
        
        
        
        $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game_id}'");
        $channel_id=$game_channel['channel_id'];
        $chceck_shared=$this->selectAssocRow("channle_admin","WHERE `channle_id`='{$channel_id}' AND `email`='{$_SESSION['login_user']['email']}'");
        if($game['owner']!=$user_id&&!$chceck_shared)
            die("GFY!");
        
        $this->deleteDB("games",$game_id);
        
        header('Content-Type:application/json');
        echo json_encode($game);
    }
    
    public function getQuestionnaire($request) {
        $res=array();
        $id=$request['id'];
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectDB("questionnaires_quiz","WHERE `questionnaire_id`='{$id}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $quiz_id=$row['quiz_id'];
          
          $quiz=$this->selectAssocRow("quiz","WHERE `id`='{$quiz_id}'");
          
          $res[$i]=array();
          $res[$i]['question']=$quiz['question'];
          $res[$i]['rank']=$quiz['rank'];
          $res[$i]['answer']=0;
          $res[$i]['answers']=array();
          
          
          $ans2=$this->selectDB("quiz_answers","WHERE `question_id`='{$quiz_id}'");
          for($j=0;$j<$ans2['n'];$j++) {
              $row2=mysql_fetch_assoc($ans2['p']);
              $res[$i]['answers'][$j]['text']=$row2['content'];
              $res[$i]['answers'][$j]['media']=$row2['media'];
              if($row2['right']=="1"){
                  $res[$i]['answer']=$j+1;
              }
          }

          
          
        }
        
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function searchQuestionnaireFree($request) {
        $res=array();
        $searchKey=$request['searchKey'];

        
        $sql="SELECT
        wheeldotube_gamedata.questionnaires.id,
        wheeldotube_gamedata.questionnaires.`name`,
        wheeldotube_gamedata.questionnaires.public,
        wheeldotube_gamedata.questionnaires.time,
        wheeldotube_gamedata.questionnaires.secret_key,
        wheeldotube_main.users.fname,
        wheeldotube_main.users.lname,
        wheeldotube_main.users.email,
        wheeldotube_main.users.image,
        wheeldotube_gamedata.questionnaires_tags.tag
        FROM
        wheeldotube_gamedata.questionnaires
        INNER JOIN wheeldotube_main.users ON wheeldotube_gamedata.questionnaires.creator = wheeldotube_main.users.id
        INNER JOIN wheeldotube_gamedata.questionnaires_tags ON wheeldotube_gamedata.questionnaires.id = wheeldotube_gamedata.questionnaires_tags.questionnaire_id
        WHERE LOWER(Concat(wheeldotube_gamedata.questionnaires_tags.tag,wheeldotube_gamedata.questionnaires.name,wheeldotube_main.users.fname,wheeldotube_main.users.lname,wheeldotube_main.users.email)) like LOWER(\"%$searchKey%\")"
        . "GROUP BY
        wheeldotube_gamedata.questionnaires.id";
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['questionnaires'][$i]=$row;
          $res['questionnaires'][$i]['creator']=$row['fname']." ".$row['lname']." (".$row['email'].")";
          $res['questionnaires'][$i]['h_time']=date("d/m/Y",$row['time']);
          $res['questionnaires'][$i]['tags']=array();
          $ans2=$this->selectDB("questionnaires_tags","WHERE `questionnaire_id`='{$row['id']}'");
          for($j=0;$j<$ans2['n'];$j++) {
              $row_tag=mysql_fetch_assoc($ans2['p']);
              $res['questionnaires'][$i]['tags'][]=ucfirst($row_tag['tag']);
          }
          
          
          $ans3=$this->selectDB("questionnaires_quiz","WHERE `questionnaire_id`='{$row['id']}'");
          $res['questionnaires'][$i]['noq']=$ans3['n'];
        }

        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function saveSettings($request) {
        $user_id=$_SESSION['login_user']['ID'];
        $user_data=$request['user_data'];
        $fields=array(
            "fname"=>$user_data['fname'],
            "lname"=>$user_data['lname'],
            "email"=>$user_data['email'],
            "image"=>$user_data['image']
        );
        $this->updateDB("users", $fields, $user_id);
        
        setcookie("user_login", "", time()-3600);
        unset($_SESSION['login_user']);
        
        $user_data=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        
        
        $user['ID']=$user_data['id'];
        $user['uid']=$user_data['unique_link'];
        $user['name']=$user_data['fname']." ".$user_data['lname'];
        $user['email']=$user_data['email'];
        $user['image']=$user_data['image'];
        $user['ghost']=$user_data['ghost'];
        $_SESSION['login_user']=$user;
        setcookie("user_login", $user['ID'], time()+3600*24*182);
        
        
        header('Content-Type:application/json');
        echo json_encode($request);
    }

    public function loadSettings($request) {
        $user_id=$_SESSION['login_user']['ID'];
        $res=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function addNewChannel($request) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        // create channel:
        $fields=array();
        $fields['unique_id']=substr(sha1(time()),0,10);
        $fields['user_id']=$user_id;
        $fields['user_private_channel']=0;
        $fields['name']=$request['channel_name'];
        $fields['small_icon']="media/css/dice/img/u_default.jpg";
        $fields['cover']="/media/img/deafult_banner.jpg";
        $fields['time']=time();
        $c_id=$this->insertDB("channels",$fields);
        //////////////////

        $res=$fields;

        header('Content-Type:application/json');
        echo json_encode($res);
    }


    public function saveEditGame($request) {
        $res=array();
        $gid=$request['gid'];
        $user_id=$_SESSION['login_user']['ID'];


        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");

        $game_id=$game['id'];

        $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game_id}'");
        $channel_id=$game_channel['channel_id'];
        $chceck_shared=$this->selectAssocRow("channle_admin","WHERE `channle_id`='{$channel_id}' AND `email`='{$_SESSION['login_user']['email']}'");
        
        
        
        
        if($game['owner']!=$user_id&&!$chceck_shared)
            die("GFY");

        $shared=false;
        if($chceck_shared&&$game['owner']!=$user_id)
            $shared=true;


        // wheeldotube_main games:
        if($request['game_data']['call_action_link'] && !(strpos($request['game_data']['call_action_link'], 'http://') === 0 || strpos($request['game_data']['call_action_link'], 'https://') === 0))
			$request['game_data']['call_action_link'] = "http://".$request['game_data']['call_action_link'];

        $fields=array();
        $fields['private']=$request['game_data']['private'];
        $fields['open_status']=$request['game_data']['open_status'];
        $fields['results_signup']=$request['game_data']['results_signup'];
        $fields['signup_headline']=$request['game_data']['signup_headline'];
        
        $fields['name']=$request['game_data']['name'];
        $fields['thumbnail']=$request['game_data']['thumbnail'];
        $fields['full_desc']=$request['game_data']['full_desc'];
        $fields['call_action_text']= $request['game_data']['call_action_text'];
        $fields['call_action_link']= $request['game_data']['call_action_link'];
        $fields['prize']=$request['game_data']['prize'];
        $fields['prize_text']=$request['game_data']['prize_text'];
        $fields['prize_time_limit']=$request['game_data']['prize_time_limit'];


        $fields['share_button']=$request['game_data']['share_button'];
        $fields['CTA_button']=$request['game_data']['CTA_button'];
        $fields['voucher']=$request['game_data']['voucher'];
        $fields['voucher_name']=$request['game_data']['voucher_name'];
        $fields['voucher_email_subject']=$request['game_data']['voucher_email']['subject'];
        $fields['voucher_email_content']=$request['game_data']['voucher_email']['content'];

        $this->updateDB("games",$fields,$game_id);
        /////////////////////////


        // assoc with channel:

        if(!$shared) {
            mysql_query("DELETE FROM `game_channel` WHERE `game_id`='{$game_id}'");
            $fields=array();
            $fields['game_id']=$game_id;
            $fields['channel_id']=$request['game_data']['channel']['id'];
            $this->insertDB("game_channel",$fields);
        }





        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        
        
        // games
        $quiz_game_data=$request['game_data']['quiz_game_data'];
        $check=$this->selectAssocRow("games","WHERE `game_id`='{$game_id}'");
        if($check) {
            $this->updateDB("games",array(
                "instruction_text"=>$quiz_game_data['instruction_text'],
                "instruction_img"=>$quiz_game_data['instruction_img']
            ),$check['id']);
        }
        else {
            $this->insertDB("games",array(
                "game_id"=>$game_id,
                "instruction_text"=>$quiz_game_data['instruction_text'],
                "instruction_img"=>$quiz_game_data['instruction_img']
            ));
        }
        

        // quiz_settings
        $quiz_settings=$this->selectAssocRow("quiz_settings","WHERE `game_id`='{$game_id}'");
        $fields=array();
        $fields['no_of_questions']=count($request['game_data']['quiz_data']);
        $fields['lang']=$request['game_data']['lang'];
        $this->updateDB("quiz_settings",$fields,$quiz_settings['id']);
        /////////////////





        // quiz_design:

        $quiz_design_r=$this->selectAssocRow("quiz_design","WHERE `game_id`='{$game_id}'");

        $quiz_design=$request['game_data']['game_design'];
        $fields=array();
        $fields['color_1']=$quiz_design['color_1'];
        $fields['color_2']=$quiz_design['color_2'];
        $fields['color_3']=$quiz_design['color_3'];
        $fields['color_4']=$quiz_design['color_4'];
        $fields['banner']=$quiz_design['banner'];
        $fields['background']=$quiz_design['background'];
        $this->updateDB("quiz_design",$fields,$quiz_design_r['id']);
        ///////////////




        // create quiz:
        $quiz=$request['game_data']['quiz_data'];
        mysql_query("DELETE FROM `quiz_game` WHERE `game_id`='{$game_id}'");
        foreach($quiz as $q):
            // quiz:

            if(isset($q['id'])) {
                $q_id=$q['id'];
                $fields=array();
                $fields['question']=$q['question'];
                $fields['media']=$q['media'];
                $fields['more_text']=$q['more_text'];
                $fields['rank']=$q['rank'];
                $this->updateDB("quiz",$fields,$q_id);
            }
            else {
                $fields=array();
                $fields['creator']=$user_id;
                $fields['time']=time();
                $fields['question']=$q['question'];
                $fields['media']=$q['media'];
                $fields['more_text']=$q['more_text'];
                $fields['rank']=$q['rank'];
                $q_id=$this->insertDB("quiz",$fields);
            }
            /////////

            mysql_query("DELETE FROM `quiz_answers` WHERE `question_id`='{$q_id}'");

            // quiz_answers:
            $answers=$q['answers'];
            $right=(int)$q['answer'];
            $c=1;
            foreach($answers as $answer):
                $fields=array();
                $fields['question_id']=$q_id;
                $fields['content']=$answer['text'];
                $fields['media']=$answer['media'];
                $fields['right']=$right==$c?1:0;
                $this->insertDB("quiz_answers",$fields);
                $c++;
            endforeach;
            ////////////////

            // quiz_game:
            $fields=array();
            $fields['game_id']=$game_id;
            $fields['quiz_id']=$q_id;
            $this->insertDB("quiz_game",$fields);
            /////////////
        endforeach;

        ///////////////
        
        
        
        
        /// test yourself:
        
        /*
         * "test_yourself_data":{
                "main_question":{text:"",media:""},
                questions:[
                    {
                        text:"",
                        media:"",
                        answers:[
                            {text:"",media:"",strength:0},
                            {text:"",media:"",strength:50},
                            {text:"",media:"",strength:100},
                            {text:"",media:"",strength:75}
                        ]
                    },
                    {
                        text:"",
                        media:"",
                        answers:[
                            {text:"",media:"",strength:0},
                            {text:"",media:"",strength:50},
                            {text:"",media:"",strength:100},
                            {text:"",media:"",strength:75}
                        ]
                    }
                ],
                results:[{text:"Test results",media:""},{text:"Test results",media:""}]
            }
         */
        
        
        // main_question:
        
        $test_yourself_data=$request['game_data']['test_yourself_data'];
        
        $fields=array(
            "main_question"=>$test_yourself_data['main_question']['text'],
            "main_question_image"=>$test_yourself_data['main_question']['media']
        );


        $this->updateDB("test_yourself_data",$fields,$test_yourself_data['main_question']['id']);
        
        
        
        $questions_pull=array();
        foreach($test_yourself_data['questions'] as $q):
            // question:
            $fields=array(
                "creator"=>$user_id,
                "time"=>time(),
                "text"=>$q['text'],
                "media"=>$q['media']
            );
        
            if(isset($q['id'])&&$q['id']!="0") {
                $q_id=$q['id'];
                $this->updateDB("test_yourself_data_questions", $fields, $q['id']);
            }
            else {
                $q_id=$this->insertDB("test_yourself_data_questions", $fields);
                // test_yourself_data_q_game
                $this->insertDB("test_yourself_data_q_game", array("game_id"=>$game_id,"q_id"=>$q_id));
            }
            
            $questions_pull[]=$q_id;
            //////////
            
            
            // answers:
            $answers_pull=array();
            foreach($q['answers'] as $answer):
                $fields=array(
                    "question_id"=>$q_id,
                    "text"=>$answer['text'],
                    "media"=>$answer['media'],
                    "strength"=>$answer['strength']
                );
            
                if(isset($answer['id'])&&$answer['id']!="0") {
                    $ans_id=$answer['id'];
                    $this->updateDB("test_yourself_data_answers", $fields, $answer['id']);
                }
                else {
                    $ans_id=$this->insertDB("test_yourself_data_answers", $fields);
                }
                $answers_pull[]=$ans_id;
            endforeach;
            
            
            
            // remove all deleted answers:
            $ans2=$this->selectDB("test_yourself_data_answers","WHERE `question_id`='{$q_id}'");
            for($j=0;$j<$ans2['n'];$j++) {
                $row2=mysql_fetch_assoc($ans2['p']);
                if(!in_array($row2['id'], $answers_pull)) {
                    //echo "delete:".$row2['id']."<br>";
                    $this->deleteDB("test_yourself_data_answers", $row2['id']);
                }
            }
            //////////////////////////////
            
            ///////////
        endforeach;
        
        // remove all deleted questions:
//        var_dump($questions_pull);
        
        $ans2=$this->selectDB("test_yourself_data_q_game","WHERE `game_id`='{$game_id}'");
        for($j=0;$j<$ans2['n'];$j++) {
            $row2=mysql_fetch_assoc($ans2['p']);
            
            if(!in_array($row2['q_id'], $questions_pull)) {
                //echo "delete:".$row2['q_id']."<br>";
                $this->deleteDB("test_yourself_data_q_game", $row2['id']);
                $this->deleteDB("test_yourself_data_questions", $row2['q_id']);
            }
            else {
                //echo "dont delete:".$row2['id']."<br>"; 
            }
        }
        ////////////////////////////////
        
        $results_pull=array();
        foreach($test_yourself_data['results'] as $result):
            
            $fields=array(
              "game_id"=>$game_id,
              "headline"=> $result['headline'],
              "text"=> $result['text'],
              "media"=> $result['media'],
              "cta"=> $result['cta'],
              "link"=> $result['link']  
            );
        
            if(isset($result['id'])&&$result['id']!="0") {
                $res_id=$result['id'];
                $this->updateDB("test_yourself_data_results", $fields, $result['id']);
            }
            else {
                $res_id=$this->insertDB("test_yourself_data_results", $fields);
            }
            $results_pull[]=$res_id;
            
        endforeach;
        
        
        // remove all deleted results:
        $ans2=$this->selectDB("test_yourself_data_results","WHERE `game_id`='{$game_id}'");
        for($j=0;$j<$ans2['n'];$j++) {
            $row2=mysql_fetch_assoc($ans2['p']);
            if(!in_array($row2['id'], $results_pull)) {
                //echo "delete:".$row2['id']."<br>";
                $this->deleteDB("test_yourself_data_results", $row2['id']);
            }
        }
        //////////////////////////////
        
        
        /*
        $main_question=$this->selectAssocRow("test_yourself_data","WHERE `game_id`='{$game_id}'");
        
        
        
        
        
        
        $questions=array();    
        $ans=$this->selectDB("test_yourself_data_q_game","WHERE `game_id`='{$game_id}'");
        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
            $question=$this->selectAssocRow("test_yourself_data_questions","WHERE `id`='{$row['q_id']}'");
            
            $questions[$i]['id']=$question['id'];
            $questions[$i]['text']=$question['text'];
            $questions[$i]['media']=$question['media'];
            $questions[$i]['answers']=array();
            $ans2=$this->selectDB("test_yourself_data_answers","WHERE `question_id`='{$question['id']}'");
            for($j=0;$j<$ans2['n'];$j++) {
                $row2=mysql_fetch_assoc($ans2['p']);
                $questions[$i]['answers'][$j]['id']=$row2['id'];
                $questions[$i]['answers'][$j]['text']=$row2['text'];
                $questions[$i]['answers'][$j]['media']=$row2['media'];
                $questions[$i]['answers'][$j]['strength']=$row2['strength'];
            }
        }
        
        
        $results=array();
        $ans=$this->selectDB("test_yourself_data_results","WHERE `game_id`='{$game_id}'");
        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
//            $results[$i]['id']=$row['id'];
            $results[$i]['text']=$row['text'];
            $results[$i]['media']=$row['media'];
        }
        
        $res['test_yourself_data']=array(
            "main_question"=>array(
                "id"=>$main_question['id'],
                "text"=>$main_question['main_question'],
                "media"=>$main_question['main_question_image']
            ),
            "questions"=>$questions,
            "results"=>$results
        );

        */

        header('Content-Type:application/json');
        echo json_encode($res);

    }


    public function getGameForEdit($request) {
        $res=array();
        $gid=$request['gid'];
        $user_id=$_SESSION['login_user']['ID'];


        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");

        $game_id=$game['id'];

        $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game_id}'");
        $channel_id=$game_channel['channel_id'];
        $chceck_shared=$this->selectAssocRow("channle_admin","WHERE `channle_id`='{$channel_id}' AND `email`='{$_SESSION['login_user']['email']}'");
        
        $channel=$this->selectAssocRow("channels","WHERE `id`='{$channel_id}'");
        
        
        
        if($game['owner']!=$user_id&&!$chceck_shared&&$channel['user_id']!=$user_id)
            die("GFT");


        $res['name']=$game['name'];
        $res['full_desc']=$game['full_desc'];
        
		$res['call_action_text']=$game['call_action_text'];
        $res['call_action_link']=$game['call_action_link'];

        $res['game_type']=array(
            "id"=>$game['game_type']
        );
        //$res['tags']=$game['tags'];
        $res['thumbnail']=$game['thumbnail'];
        $res['open_status']=$game['open_status'];
        $res['results_signup']=$game['results_signup'];
        $res['signup_headline']=$game['signup_headline'];
        $res['private']=$game['private'];
        $res['prize']=$game['prize'];
        $res['prize_text']=$game['prize_text'];
        $res['prize_time_limit']=$game['prize_time_limit'];

        $res['share_button']=$game['share_button'];
        $res['CTA_button']=$game['CTA_button'];
        $res['voucher']=$game['voucher'];
        $res['voucher_name']=$game['voucher_name'];
        $res['voucher_email']=array(
          "subject"=> $game['voucher_email_subject'],
          "content"=> $game['voucher_email_content']
        );

        $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game_id}'");



        $ans=$this->selectDB("channels","WHERE `user_id`='{$user_id}'");
        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);

            if($row['id']==$game_channel['channel_id']) {
                $res['channel_c']=$i;
            }

        }



        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );

        $quiz_settings=$this->selectAssocRow("quiz_settings","WHERE `game_id`='{$game_id}'");
        $res['lang']=$quiz_settings['lang'];



        
        


        
        if(!$res['quiz_game_data']=$this->selectAssocRow("games","WHERE `game_id`='{$game_id}'")) {
            $res['quiz_game_data']=array(
                "game_id"=>$game_id,
                "instruction_text"=>"",
                "instruction_img"=>""
            );
        }
        

        $res['quiz_data']=array();



        $ans=$this->selectDB("quiz_game","WHERE `game_id`='{$game_id}'");
        for($i=0;$i<$ans['n'];$i++) {
            $Q=array();
            $row=mysql_fetch_assoc($ans['p']);
            $quiz=$this->selectAssocRow("quiz","WHERE `id`='{$row['quiz_id']}'");
            $Q['id']=$quiz['id'];
            $Q['question']=$quiz['question'];
            $Q['media']=$quiz['media'];
            $Q['more_text']=$quiz['more_text'];
            $Q['rank']=$quiz['rank'];
            $Q['answer']=0;
            $Q['answers']=array();
            $ans2=$this->selectDB("quiz_answers","WHERE `question_id`='{$quiz['id']}'");
            $c=1;
            for($j=0;$j<$ans2['n'];$j++) {
                $row2=mysql_fetch_assoc($ans2['p']);
                $Q['answers'][$j]['text']=$row2['content'];
                $Q['answers'][$j]['media']=$row2['media'];
                if((int)$row2['right']==1) {
                    $Q['answer']=$c;
                }

                $c++;
            }

            $res['quiz_data'][]=$Q;
        }


        $quiz_design=$this->selectAssocRow("quiz_design","WHERE `game_id`='{$game_id}'");

        $res['game_design']['color_1']=$quiz_design['color_1'];
        $res['game_design']['color_2']=$quiz_design['color_2'];
        $res['game_design']['color_3']=$quiz_design['color_3'];
        $res['game_design']['color_4']=$quiz_design['color_4'];
        $res['game_design']['banner']=$quiz_design['banner'];
        $res['game_design']['background']=$quiz_design['background'];


        
        
        /*
         * "test_yourself_data":{
                "main_question":{text:"",media:""},
                questions:[
                    {
                        text:"",
                        media:"",
                        answers:[
                            {text:"",media:"",strength:0},
                            {text:"",media:"",strength:50},
                            {text:"",media:"",strength:100},
                            {text:"",media:"",strength:75}
                        ]
                    },
                    {
                        text:"",
                        media:"",
                        answers:[
                            {text:"",media:"",strength:0},
                            {text:"",media:"",strength:50},
                            {text:"",media:"",strength:100},
                            {text:"",media:"",strength:75}
                        ]
                    }
                ],
                results:[{text:"Test results",media:""},{text:"Test results",media:""}]
            },
         */
        
        $main_question=$this->selectAssocRow("test_yourself_data","WHERE `game_id`='{$game_id}'");
        
        
        $questions=array();    
        $ans=$this->selectDB("test_yourself_data_q_game","WHERE `game_id`='{$game_id}'");
        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
            $question=$this->selectAssocRow("test_yourself_data_questions","WHERE `id`='{$row['q_id']}'");
            
            $questions[$i]['id']=$question['id'];
            $questions[$i]['text']=$question['text'];
            $questions[$i]['media']=$question['media'];
            $questions[$i]['answers']=array();
            $ans2=$this->selectDB("test_yourself_data_answers","WHERE `question_id`='{$question['id']}'");
            for($j=0;$j<$ans2['n'];$j++) {
                $row2=mysql_fetch_assoc($ans2['p']);
                $questions[$i]['answers'][$j]['id']=$row2['id'];
                $questions[$i]['answers'][$j]['text']=$row2['text'];
                $questions[$i]['answers'][$j]['media']=$row2['media'];
                $questions[$i]['answers'][$j]['strength']=$row2['strength'];
            }
        }
        
        
        $results=array();
        $ans=$this->selectDB("test_yourself_data_results","WHERE `game_id`='{$game_id}'");
        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
//            $results[$i]['id']=$row['id'];
            $results[$i]['headline']=$row['headline'];
            $results[$i]['text']=$row['text'];
            $results[$i]['media']=$row['media'];
            $results[$i]['link']=$row['link'];
            $results[$i]['cta']=$row['cta'];
        }
        
        $res['test_yourself_data']=array(
            "main_question"=>array(
                "id"=>$main_question['id'],
                "text"=>$main_question['main_question'],
                "media"=>$main_question['main_question_image']
            ),
            "questions"=>$questions,
            "results"=>$results
        );

        //$res=$game;
        header('Content-Type:application/json');
        echo json_encode($res);
    }

    public function getGames($request) {
        $res=array();
        $cid=$request['cid'];
        $user_id=$_SESSION['login_user']['ID'];

        $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");

        if($user_id!==$channel['user_id'] && !$this->selectAssocRow("channle_admin","WHERE `channle_id`='{$channel['id']}' AND `email`='{$_SESSION['login_user']['email']}'"))
            die();

/*
         $sql="SELECT
        games.id,
        games.owner,
        games.unique_id,
        games.time,
        games.private,
        games.open_status,
        games.game_type,
        games.game_template,
        games.plays,
        games.name,
        games.thumbnail,
        games.full_desc
        FROM
        game_channel
        INNER JOIN games ON game_channel.game_id = games.id
        WHERE
        game_channel.channel_id = '{$channel['id']}'  ORDER BY games.time DESC";
*/
		$sql = 
		"Select games.id, games.owner, games.unique_id, games.time, games.private,
		  games.open_status,games.results_signup, games.game_type, games.game_template, games.plays,
		  games.name, games.thumbnail, games.full_desc
		From game_channel 
		Inner Join games On game_channel.game_id = games.id
		Where game_channel.channel_id = '{$channel['id']}'
		Order By games.time Desc";


        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
            $res[]=$row;
        }


        header('Content-Type:application/json');
        echo json_encode($res);
    }



    public function saveChannel($request) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        $cid=$request['cid'];
        $check=$this->selectAssocRow("channels","WHERE `id`='{$cid}' AND `user_id`='{$user_id}'");
        if($check):
            $fields=array();
            $fields['name']=$request['name'];
            $fields['description']=$request['description'];
            $fields['small_icon']=$request['small_icon'];
            $fields['cover']=$request['cover'];
            $this->updateDB("channels",$fields,$cid);
            $res['status']="ok";
        endif;

        header('Content-Type:application/json');
        echo json_encode($res);
    }

    public function resetPassword($request) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        $data=$request['data'];

        $res['status']="faild";
        $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        if($user) {
            $inputPasswort=create_pa($data['old_password']);
            $userPassword=$user['password'];

            if(is_equal_pa($userPassword,$inputPasswort)) {

                // reset password:
                $fields=array();
                $fields['password']=create_pa($data['new_password']);
                $this->updateDB("users",$fields,$user['id']);
                //////////////////


                $res['status']="ok";
            }
        }


        header('Content-Type:application/json');
        echo json_encode($res);
    }
	public function loadGameCallToAction($request) {
		// get Call to action details
		$game_id=$_SESSION['settings']['game_id'];
        $game_details = $this->selectAssocRow("games","WHERE `id`='{$game_id}'");
        if($game_details['call_action_text'] && $game_details['call_action_link']){
	        $res['call_action_text'] = $game_details['call_action_text'];
	        $res['call_action_link'] = $game_details['call_action_link'];
	        $res['call_action_btnText'] = "Go!";
        }
        else{
        	$res['call_action_text'] = "Want to win more points?";
        	$res['call_action_link'] = 'sendToFriend';
        	$res['call_action_btnText'] = "Send to a friend and win 100 points";
        } 
        
        header('Content-Type:application/json');
        echo json_encode($res);
	}
    public function sendGameInvitations($request) {
        $res=array();
        $cid=$request['cid'];
        $user_id=$_SESSION['login_user']['ID'];


        $channel=$this->selectAssocRow("channels","WHERE `id`='{$cid}'");
        $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");


        $subject=$request['email']['subject'];
        $content=$request['email']['content'];
        $game_link=$request['game_link'];

        $sql="SELECT
            users.fname,
            users.lname,
            users.email,
            users.unique_link
            FROM
            channel_user
            INNER JOIN users ON channel_user.user_id = users.id
            WHERE
            channel_user.channel_id = '$cid'";

        $ans=$this->selectQDB($sql);

        for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
            $email = $row['email'];
			$uid = $row['unique_link'];

            $ex=explode("#/",$game_link);
            $fixed_link=implode("?u={$row['unique_link']}#/",$ex);
            
            $fixed_link=$game_link."?u={$row['unique_link']}";
            
            
            $banner="http://www.wheeldo.co/media/img/deafult_banner.jpg";
            
            
            $pos = strpos($channel['cover'], "cloudinary");
            if ($pos !== false) {
                $banner=$channel['cover'];
                $banner=str_replace("h_160", "h_50", $banner);
                $banner=str_replace("w_1045", "w_300", $banner);
            }

            $parameters=array(
                "banner" => $banner ,
                "name" => $row['fname']." ".$row['lname'],
                "game_link" => $fixed_link,
                "cid"=>$channel['unique_id'],
                "uid"=>$uid,
                "content" => $content
            );


            $templateName="game_invitation";
            if(isset($request['lang'])&&$request['lang']=="he") {
                $templateName="game_invitation_he";
            }
            $body=file_get_contents(dirname(__FILE__)."/Emails/{$templateName}.html");
            foreach($parameters as $key=>$value):
                    $body=str_replace("[".$key."]", $value,$body);
            endforeach;



            $task = new PushTask('/send_email_processing', ['from'=>$channel['name'],'email'=>$email,'subject'=>$subject,'body'=>$body]);
            $task_name = $task->add();

            echo $task_name."<br>";
        }

        //header('Content-Type:application/json');
        echo json_encode($request);
    }

    public function getChannelUsersCouter($request) {
        $res=array();
        $cid=$request['cid'];
        $user_id=$_SESSION['login_user']['ID'];

        $channel=$this->selectDB("channel_user","WHERE `channel_id`='{$cid}'");
        $res['n']=$channel['n'];

        header('Content-Type:application/json');
        echo json_encode($res);
    }

    public function removeUserChannel($request) {
        $res=array();
        $cid=$request['cid'];
        $user_id=$_SESSION['login_user']['ID'];

        $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");

        if($user_id!==$channel['user_id'])
            die();


        $uid=$request['uid'];


        $user=$this->selectAssocRow("users","WHERE `unique_link`='{$uid}'");

        $check=$this->selectAssocRow("channel_user","WHERE `channel_id`='{$channel['id']}' AND `user_id`='{$user['id']}'");
        if($check) {
            $this->deleteDB("channel_user",$check['id']);
        }

        header('Content-Type:application/json');
        echo json_encode($res);
    }

    public function addUsers($request) {
        $res=array();
        $cid=$request['cid'];
        $user_id=$_SESSION['login_user']['ID'];

        $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");
        $channel_id=$channel['id'];

        if($user_id!==$channel['user_id'])
            die();


        $res['tasks']=array();

        foreach($request['users'] as $user):
            $task = new PushTask('/add_user', ['fname'=>$user['fname'],'lname'=>$user['lname'],'email'=>$user['email'],'channel_id'=>$channel_id]);
            $task_name = $task->add();
            $res['tasks'][]=$task_name;
        endforeach;

        $res['status']="ok";

        header('Content-Type:application/json');
        echo json_encode($res);
    }

    public function getChannelUsers($request) {
        $res=array();
        $cid=$request['cid'];
        $user_id=$_SESSION['login_user']['ID'];
        $channel=$this->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");

        $res['users']=array();
        $sql="SELECT
        users.fname,
        users.lname,
        users.image,
        users.unique_link
        FROM
        users
        INNER JOIN channel_user ON channel_user.user_id = users.id
        WHERE
        channel_user.channel_id = {$channel['id']}";
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['users'][]=$row;
        }

        header('Content-Type:application/json');
        echo json_encode($res);
    }


    public function saveUser($REQ) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        $data=json_decode(stripslashes($this->stripData($REQ['data'])),true);

        $fields=array();
        $fields['image']=$data['image'];
        $this->updateDB("users",$fields,$user_id);


        $user_data=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        $user['ID']=$user_data['id'];
        $user['uid']=$user_data['unique_link'];
        $user['name']=$user_data['fname']." ".$user_data['lname'];
        $user['email']=$user_data['email'];
        $user['image']=$user_data['image'];
        $user['ghost']=$user_data['ghost'];
        $_SESSION['login_user']=$user;

        foreach($data['my_channels'] as $channel):
            $fields=array();
            $fields['name']=$channel['name'];
            $fields['description']=$channel['description'];
            $fields['cover']=$channel['cover'];
            $this->updateDB("channels",$fields,$channel['id']);
        endforeach;

        echo json_encode($res);
    }

    public function loadGameDesign($REQ) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        $game_id=$REQ['game_id'];
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );

        //$game_design=$this->selectAssocRow("quiz_design","WHERE `template_id`='$game_id' AND `game_id`='1' AND `user_id`='0'");
        
        
        $game_design['color_1']="";
        $game_design['color_2']="";
        $game_design['color_3']="";
        $game_design['color_4']="#ffffff";
        $game_design['banner']="";
        $game_design['background']="http://res.cloudinary.com/wheeldo/image/upload/c_scale,h_700,w_1980/v1412165846/bg_zysqn4.jpg";
        

        echo json_encode($game_design);
    }
    
    
    public function gameFormRR($REQ) {
        
        //$data=$REQ['data'];
        
        $user_id=$REQ['user_id'];
        $c_id=$REQ['c_id'];
        $design_id=$REQ['design_id'];
        
        //// create game:
//        
        $datagame_id=$REQ['topic'];
        $game_unique_id=substr(sha1(time()),0,10);
        $sent_data=array();
        
        
        
        //var_dump($game);
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        
        $game=$this->selectAssocRow("questionnaires","WHERE `id`='{$datagame_id}'");
//        $ans=$this->selectDB("questionnaires_quiz","WHERE `questionnaire_id`='{$id}'");
             
        $quiz_data=array();
        $ans=$this->selectDB("questionnaires_quiz","WHERE `questionnaire_id`='{$datagame_id}'");
        for($i=0;$i<$ans['n'];$i++) {
            $row= mysql_fetch_assoc($ans['p']);
            $q=$this->selectAssocRow("quiz","WHERE `id`='{$row['quiz_id']}'");
            
            $q_id=$q['id'];
            
            $answers=array();
            $ans2=$this->selectDB("quiz_answers","WHERE `question_id`='{$q_id}'");
            $answer=0;
            for($j=0;$j<$ans2['n'];$j++) {
                $row2=mysql_fetch_assoc($ans2['p']);
                
                if($row2['right']=="1") {
                    $answer=$j+1;
                }
                $answers[]=array("text"=>$row2['content'],"media"=>$row2['media']);  
            }
            
            $quiz_data[]=array(
                "question"=>$q['question'],
                "rank"=>$q['rank'],
                "answer"=>$answer,
                "answers"=>$answers
            );
        }
        
        
        // load quiz design:
        
        $design=$this->selectAssocRow("quiz_design","WHERE `template_id`='{$design_id}'");
        //echo "WHERE `id`='{$design_id}'";
        ////////////////////
        
        
        @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );
        
        $game_design=array(
            "color_1"=>$design['color_1'],
            "color_2"=>$design['color_2'],
            "color_3"=>$design['color_3'],
            "color_4"=>$design['color_4'],
            "banner"=>$design['banner'],
            "background"=>$design['background']
        );
        
                
       
        $sent_data['data']=array(
            "name"=>$game['name'],
            "full_desc"=>"",
            "tags"=>"",
            "lang"=>"en",
            "call_action_text"=>"",
            "call_action_link"=>"",
            "thumbnail"=>"",
            "channel"=>array("id"=>$c_id),
            "game_type"=>array("id"=>2),
            "private"=>0,
            "open_status"=>0,
            "results_signup"=>0,
            
            "prize"=>0,
            "prize_text"=>"",
            "prize_time_limit"=>0,
            "voucher"=>0,
            "voucher_name"=>"",
            "voucher_email"=>array(
                "subject"=>"",
                "content"=>""
            ),
            "quiz_data"=>$quiz_data,
            "test_yourself_data"=>array(),
            "game_design"=>$game_design
        );
        
        //var_dump($sent_data['data']);
        
        $this->publishGame($sent_data);
            
        
        /////////////////
        
        //echo json_encode($REQ);
    }
        

    public function publishGame($REQ) {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        //$data=json_decode(stripslashes($this->stripData($REQ['data'])),true);
        
        $data=$REQ['data'];

        $game_unique_id=substr(sha1(time()),0,10);
        


        if($data['call_action_link']!="" && !(strpos($data['call_action_link'], 'http://') === 0 || strpos($data['call_action_link'], 'https://') === 0)) {
            $data['call_action_link'] = "http://".$data['call_action_link'];
        }
  
		
        // wheeldotube_main games:
        $fields=array();
        $fields['owner']=$user_id;
        $fields['unique_id']=$game_unique_id;
        $fields['time']=time();
        $fields['private']=$data['private'];
        $fields['open_status']=$data['open_status'];
        $fields['results_signup']=$data['results_signup'];
        $fields['signup_headline']=$data['signup_headline'];
        $fields['game_type']=(int)$data['game_type']['id'];
        $game_type=$fields['game_type'];
        $fields['game_template']=1;
        $fields['name']=$data['name'];
        $fields['thumbnail']=$data['thumbnail'];
        $fields['full_desc']=$data['full_desc'];
        
        $fields['call_action_text']=$data['call_action_text'];
        $fields['call_action_link']=$data['call_action_link'];


        $fields['prize']=$data['prize'];
        $fields['prize_text']=$data['prize_text'];
        $fields['prize_time_limit']=$data['prize_time_limit'];
        
        $fields['share_button']=$data['share_button'];
        $fields['CTA_button']=$data['CTA_button'];

        $fields['voucher']=$data['voucher'];
        $fields['voucher_name']=$data['voucher_name'];
        $fields['voucher_email_subject']=$data['voucher_email']['subject'];
        $fields['voucher_email_content']=$data['voucher_email']['content'];


        //var_dump($fields);
        $game_id=$this->insertDB("games",$fields);
        echo mysql_error();
        /////////////////////////


        // assoc with channel:
        $fields=array();
        $fields['game_id']=$game_id;
        $fields['channel_id']=$data['channel']['id'];

        $this->insertDB("game_channel",$fields);
        


        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $quiz_game_data=$data['quiz_game_data'];
        $this->insertDB("games",array(
            "game_id"=>$game_id,
            "instruction_text"=>$quiz_game_data['instruction_text'],
            "instruction_img"=>$quiz_game_data['instruction_img']
        ));
        
        // game_settings
        switch($game_type):
            case 1:
            case 2:
            case 4:
                $fields=array();
                $fields['game_id']=$game_id;
                $fields['no_of_questions']=count($data['quiz_data']);
                $fields['time_limit']=1;
                $fields['lang']=$data['lang'];
                $this->insertDB("quiz_settings",$fields); 
            break;
            case 3:
                $fields=array();
                $fields['game_id']=$game_id;
                $fields['no_of_questions']=count($data['test_yourself_data']['questions']);
                $fields['time_limit']=0;
                $fields['lang']=$data['lang'];
                $this->insertDB("quiz_settings",$fields);
            break;
        endswitch;
        /////////////////


        // game_design:
        switch($game_type):
            case 1:
            case 2:
            case 3:
            case 4:
                $quiz_design=$data['game_design'];
                $fields=array();
                $fields['game_id']=$game_id;
                $fields['template_id']=1;
                $fields['user_id']=$user_id;
                $fields['color_1']=$quiz_design['color_1'];
                $fields['color_2']=$quiz_design['color_2'];
                $fields['color_3']=$quiz_design['color_3'];
                $fields['color_4']=$quiz_design['color_4'];
                $fields['banner']=$quiz_design['banner'];
                $fields['background']=$quiz_design['background'];
                $this->insertDB("quiz_design",$fields);
            break;
        endswitch;
        ///////////////

        // create game content:
        switch($game_type):
            case 1:
            case 2:
            case 4:
                $quiz=$data['quiz_data'];
                foreach($quiz as $q):
                    // quiz:
                    $fields=array();
                    $fields['creator']=$user_id;
                    $fields['time']=time();
                    $fields['question']=$q['question'];
                    $fields['media']=$q['media'];
                    $fields['more_text']=$q['more_text'];
                    $fields['rank']=$q['rank'];


                    $q_id=$this->insertDB("quiz",$fields);
                    /////////


                    // quiz_answers:
                    $answers=$q['answers'];
                    $right=(int)$q['answer'];
                    $c=1;
                    foreach($answers as $answer):
                        $fields=array();
                        $fields['question_id']=$q_id;
                        $fields['content']=$answer['text'];
                        $fields['media']=$answer['media'];
                        $fields['right']=$right==$c?1:0;
                        $this->insertDB("quiz_answers",$fields);
                        $c++;
                    endforeach;
                    ////////////////

                    // quiz_game:
                    $fields=array();
                    $fields['game_id']=$game_id;
                    $fields['quiz_id']=$q_id;
                    $this->insertDB("quiz_game",$fields);
                    /////////////

                    // quiz tags:
                    $tags=explode(";",$data['tags']);
                    foreach($tags as $tag):
                        if($tag!="")
                            $this->insertDB("quiz_tags",array("quiz_id"=>$q_id,"tag"=> strtolower($tag)));
                    endforeach;
                    //////////////

                endforeach;
            break;
            case 3:
                
                //test_yourself_data
                $fields=array();
                $fields['game_id']=$game_id;
                $fields['main_question']=$data['test_yourself_data']['main_question']['text'];
                $fields['main_question_image']=$data['test_yourself_data']['main_question']['media'];
                $this->insertDB("test_yourself_data",$fields);
                ////////////////////
                
                $test_yourself_questions=$data['test_yourself_data']['questions'];
                foreach($test_yourself_questions as $q):
                    //"test_yourself_data_questions"
                    $fields=array();
                    $fields['creator']=$user_id;
                    $fields['time']=time();
                    $fields['text']=$q['text'];
                    $fields['media']=$q['media'];
                    
                    $q_id=$this->insertDB("test_yourself_data_questions",$fields);
                    /////////////////////////
                    
                    
                    
                    //test_yourself_data_answers
                    // quiz_answers:
                    $answers=$q['answers'];
                    $c=1;
                    foreach($answers as $answer):
                        $fields=array();
                        $fields['question_id']=$q_id;
                        $fields['text']=$answer['text'];
                        $fields['media']=$answer['media'];
                        $fields['strength']=$answer['strength'];
                        $this->insertDB("test_yourself_data_answers",$fields);
                        $c++;
                    endforeach;
                    ////////////////
                    
                    
                    // test_yourself_data_q_game:
                    $fields=array();
                    $fields['game_id']=$game_id;
                    $fields['q_id']=$q_id;
                    $this->insertDB("test_yourself_data_q_game",$fields);
                    /////////////
                    
                    
                    // test_yourself_data_q_tags:
                    $tags=explode(";",$data['tags']);
                    foreach($tags as $tag):
                        if($tag!="")
                            $this->insertDB("test_yourself_data_q_tags",array("q_id"=>$q_id,"tag"=> strtolower($tag)));
                    endforeach;
                    //////////////
                    
                    
                endforeach;
                
                
                // test_yourself_data_results
                $test_yourself_results=$data['test_yourself_data']['results'];
                foreach($test_yourself_results as $result):
                    $fields=array();
                    $fields['game_id']=$game_id;
                    $fields['headline']=$result['headline'];
                    $fields['text']=$result['text'];
                    $fields['media']=$result['media'];
                    $fields['cta']=$result['cta'];
                    $fields['link']=$result['link'];
                    $this->insertDB("test_yourself_data_results",$fields);
                endforeach;
                
                
            break;
        endswitch;
        
        
        

        ///////////////

        setcookie("newGameBackup","", time()-3600);
        $res['game_id']=$game_unique_id;

        echo json_encode($res);
    }


    public function getImageUploadURL() {
        $options = [ 'gs_bucket_name' => CloudStorageTools::getDefaultGoogleStorageBucketName() ];
        $url=CloudStorageTools::createUploadUrl('/img_uploader', $options);
        echo json_encode(array("url"=>$url,"bucket"=>CloudStorageTools::getDefaultGoogleStorageBucketName()));
    }
    
    public function getUploadURLG($REQ) {
        $options = [ 'gs_bucket_name' => CloudStorageTools::getDefaultGoogleStorageBucketName() ];
        $url=CloudStorageTools::createUploadUrl($REQ['url'], $options);
        echo json_encode(array("url"=>$url,"bucket"=>CloudStorageTools::getDefaultGoogleStorageBucketName()));
    }


    public function getFileData($REQ) {
        $res_mark=$REQ['res_mark'];
        $file_data=$this->selectAssocRow("file_data","WHERE `res_mark`='$res_mark'");
        //var_dump($file_data);
        file_put_contents('test.txt',"\r\n".print_r($file_data,1),FILE_APPEND);
        if($file_data) {
            $json=json_decode($file_data['data'],true);
            $json['status']="ok";
            $this->deleteDB("file_data",$file_data['id']);
        }
        else {
            $json['status']="faild";
        }

        echo json_encode($json);

    }

    public function getUploadUrl($REQ) {
        global $local;
        $to_url=$REQ['to_url'];
        $res['hash']=sha1(time());
        $res['url'] = "http://api.wheeldo.com/xlsx_to_json.php";
        $res['url_notify'] = "http://www.wheeldo.co/notify_file";
        if($local) {
            $res['url'] = "http://api.wheeldo.localhost/xlsx_to_json.php";
            $res['url_notify'] = "http://localhost:8080/notify_file";
        }
        echo json_encode($res);
    }
    
    
    public function createXslx($REQ) {
        
        $data_arr=$REQ['data_arr'];
        
        //var_dump($data_arr);
        $postData=$data_arr;

        $data = http_build_query($postData);
        $context = [
          'http' => [
            'method' => 'post',
            'X-Appengine-Inbound-Appid'=>'turnkey-rookery-535',
            'content' => $data
          ]
        ];
        
        
        //http://api.wheeldo.localhost/json_to_xlsx.php
        $context = stream_context_create($context);
        
        
        global $local;
        if($local) {
            $result = file_get_contents('http://api.wheeldo.localhost/json_to_xlsx.php', false, $context); 
            $download_link="http://api.wheeldo.localhost/exd/$result";
        }
        else {
            $result = file_get_contents('http://api.wheeldo.com/json_to_xlsx.php', false, $context);
            $download_link="http://api.wheeldo.com/exd/$result";
        }
        
        //$result
//        echo "<hr>";
//        echo $result;
        $res['link']=$download_link;
        echo json_encode($res);
    }
    


    public function getHash($REQ) {
        $res=array();
        $res['hash']=sha1(time());
        echo json_encode($res);
    }
    
    public function saveGameCoockie($REQ) {
        $new_game=$REQ['new_game'];
        setcookie("newGameBackup",json_encode($new_game), time()+3600*24);
    }
    
    public function removeGameCoockie() {
        setcookie("newGameBackup","", time()-3600);
    }
    
    public function checkIFCreateGameCookieExists() {
        $res=array();
        if(isset($_COOKIE["newGameBackup"])) {
            $res['status']="ok";
        }
        else {
            $res['status']="faild";
        }
        echo json_encode($res);
    }
    
    public function getGameCoockie() {
        $res=array();
        if(isset($_COOKIE["newGameBackup"])) {
            $res['status']="ok";
            $res['new_game']=json_decode($_COOKIE["newGameBackup"],true);
        }
        echo json_encode($res);
    }


    public function getLibrary() {
        $res=array();
        $user_id=$_SESSION['login_user']['ID'];
        
        $ans=$this->selectDB("user_library","WHERE `user_id`='{$user_id}'");
        $c=0;
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          if($row['thumbnail']!="") {
              $res[$c]=$row;
              $res[$c]['date']=date("d/m/y",$row['time']);
              $c++;
          }
          
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function deleteThumb($request) {
        $id=$request['id'];
        $user_id=$_SESSION['login_user']['ID'];
        $check=$this->selectAssocRow("user_library","WHERE `id`='{$id}' AND `user_id`='{$user_id}'");
        if($check) {
            $this->deleteDB("user_library",$id);
        }
        
        
        
    }
    
    public function resetGameRecords($request) {
        $gid=$request['gid'];
        $user_id=$_SESSION['login_user']['ID'];
        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}' AND `owner`='{$user_id}'");
        
        if(!$game){
            return;
        }
        $game_id=$game['id'];
        
        
        $records=$request['records'];
        

        
        $in_str="";
        
        $c=0;
        foreach($records as $record):
            if($c>0)
                $in_str.=",";
            $in_str.=$record;
            $c++;
        endforeach;

        
        
        
        
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        switch((int)$game['game_type']):
            case 1:
            case 2:
            case 4:
                //game_quiz_user
                mysql_query("DELETE FROM `game_quiz_user` WHERE `game_id`='{$game_id}' AND `user_id` IN ($in_str)");
                //quiz_user
                mysql_query("DELETE FROM `quiz_user` WHERE `game_id`='{$game_id}' AND `user_id` IN ($in_str)");
                // quiz_report
                mysql_query("DELETE FROM `quiz_report` WHERE `game_id`='{$game_id}' AND `user_id` IN ($in_str)");
                return;
            case  3:
                //test_yourself_data_user_q
                mysql_query("DELETE FROM `test_yourself_data_user_q` WHERE `game_id`='{$game_id}' AND `user_id` IN ($in_str)");
                //test_yourself_data_user_score
                mysql_query("DELETE FROM `test_yourself_data_user_score` WHERE `game_id`='{$game_id}' AND `user_id` IN ($in_str)");
                // test_yourself_data_report
                mysql_query("DELETE FROM `test_yourself_data_report` WHERE `game_id`='{$game_id}' AND `user_id` IN ($in_str)");
                return;
        endswitch;

    }
    

}


$gt=new getters();
$gt->$op($REQ);

























