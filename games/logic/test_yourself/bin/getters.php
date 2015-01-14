<?php

require_once 'top_functions.php';
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

//$op($REQ);




class getters extends dbop{
    
    public function __consteuct() {
        return;
    }

    public function regUser($REQ) {
        $res=array();
        $q_id=$REQ['q_id'];
        $q=$_SESSION['quiz'][$q_id];
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        
        
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function setScore($REQ) {
        $res=array();
        $game_id=$_SESSION['settings']['game_id'];
        $user_id=$_SESSION['user_data']['id'];
        $score=$REQ['score'];
        $start_time=$REQ['start_time'];
        
        //$_SESSION['scores'][$game_id]=$score;
        
        
        
        //test_yourself_data_user_score
        $fields=array(
            "game_id"=>$game_id,
            "user_id"=>$user_id,
            "start_time"=>$start_time,
            "score"=>$score
        );
        $this->insertDB("test_yourself_data_user_score",$fields);
        
        header('Content-Type:application/json');
        echo json_encode($fields);
        
    }
    
    public function getScore($REQ) {
        $res=array();
        $game_id=$_SESSION['settings']['game_id'];
        $user_id=$_SESSION['user_data']['id'];
            
//        $res['session']=$_SESSION['scores'];
//        $res['score']=isset($_SESSION['scores'][$game_id])?$_SESSION['scores'][$game_id]:false;
        
        
        $score=$this->selectAssocRow("test_yourself_data_user_score","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}' ORDER BY `start_time` DESC");
        $res['score']=$score?$score['score']:0;
        
        header('Content-Type:application/json');
        echo json_encode($res);
        
    }
    
    public function setAns($REQ) {
        $res=array();
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        $start_time=$REQ['start_time'];
        $q_id=$REQ['q_id'];
        $ans_id=$REQ['ans_id'];
        
        
        
        //test_yourself_data_user_q
        $fields=array(
            "game_id"=>$game_id,
            "user_id"=>$user_id,
            "start_time"=>$start_time,
            "q_id"=>$q_id,
            "ans_id"=>$ans_id
        );
        $this->insertDB("test_yourself_data_user_q",$fields);
        
        
        
        header('Content-Type:application/json');
        echo json_encode($_SESSION['user_data']);
    }
    
    
    public function loadGameEnd() {
        $res=array();
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        
        $this->updateDB("game_quiz_user", array("finish"=>1), $game_quiz_user['id']);
        
        $ans=$this->selectDB("quiz_user","WHERE `user_id`='{$user_id}' AND `game_id`='{$game_id}'");
        
        $res['total']=0;
        $res['right']=0;
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          
          if((int)$row['correct']==1) {
              $res['right']++;
          }
          
          $res['total']++;
        }

        $res['score']=$game_quiz_user['score'];
        
        
        
        $res['leaders']=array();
        $ans=$this->selectDB("game_quiz_user","WHERE `game_id`='{$game_id}'  ORDER BY `score` DESC LIMIT 0,3");
         for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
            $res['leaders'][$i]['name']=$row['user_name'];
            $res['leaders'][$i]['score']=$row['score'];
         }
         
         
         
         $res['your_rank']="--";
         $ans=$this->selectDB("game_quiz_user","WHERE `game_id`='{$game_id}'  ORDER BY `score` DESC");
         for($i=0;$i<$ans['n'];$i++) {
            $row=mysql_fetch_assoc($ans['p']);
            if($user_id==$row['user_id'])
                $res['your_rank']=$this->rank_text_from_number($i+1);
         }
         
         $res['total_players']=$ans['n'];
        
        header('Content-Type:application/json');
        echo json_encode($res);
        
    }
    
    public function initReport() {
        $this->reportInit();
    }
    
    public function reportInit() {
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        $test_yourself_data_report=$this->selectAssocRow("test_yourself_data_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(!$test_yourself_data_report) {
            $this->insertDB("test_yourself_data_report",array("game_id"=>$game_id,"user_id"=>$user_id,"time"=>time()));
            $test_yourself_data_report=$this->selectAssocRow("test_yourself_data_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        }
        return $test_yourself_data_report;
    }
    
    public function reportParaSet($request) {
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $para=$request['para'];
        $val=$request['val'];
        
        $test_yourself_data_report=$this->selectAssocRow("test_yourself_data_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(!$test_yourself_data_report) {
            $test_yourself_data_report=$this->reportInit();
        }
        $this->updateDB("test_yourself_data_report",array($para=>$val),$test_yourself_data_report['id']);
        
    }
            
    public function setReferrer($request){
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $test_yourself_data_report=$this->selectAssocRow("test_yourself_data_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(!$test_yourself_data_report) {
            $test_yourself_data_report=$this->reportInit();
        }
        
        if($test_yourself_data_report['referrer']=="") {
            $this->updateDB("test_yourself_data_report",array("referrer"=>$request['referrer']),$test_yourself_data_report['id']);
        }
        header('Content-Type:application/json');
        echo json_encode($test_yourself_data_report);
    }

}


$gt=new getters();
$gt->$op($REQ);

























