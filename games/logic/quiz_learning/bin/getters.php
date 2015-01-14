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
    
    private function stripData($data) {
        $strippedData=str_replace ("___amp___","&",$data);
        return $strippedData;
    }
    
    public function loadGameProgress() {
        $res=array();
        
        $res['game_progress']=$_SESSION['game_progress'];
        
        header('Content-Type:application/json');
        echo json_encode($res);
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
    
    public function saveAns($REQ) {
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        $q_id=$REQ['q_id'];
        
        $fields=array(
            "game_id"=>$game_id,
            "user_id"=>$user_id,
            "question_id"=>$q_id,
            "ans_id"=>$REQ['ans_id'],
            "correct"=>$REQ['correct'],
            "score"=>$REQ['correct']?100:0,
            "time"=>time()
        );
        
        $check=$this->selectAssocRow("quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}' AND `question_id`='{$q_id}'");
        if($check) {
            $this->updateDB("quiz_user",$fields,$check['id']);
        }
        else {
            $this->insertDB("quiz_user",$fields);
        }
        
        // set score:
        $score=0;
        $ans=$this->selectDB("quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $score+=(int)$row['score'];
        }
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        $this->updateDB("game_quiz_user",array("score"=>$score),$game_quiz_user['id']);
        
        
    }
    
    public function gameEndData() {
        $res=array();
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        // set score:
        $score=0;
        $total=0;
        $right=0;
        $ans=$this->selectDB("quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        $res['user_answers']=array();
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['user_answers'][$row['question_id']]=$row['ans_id'];
          $score+=(int)$row['score'];
          $total++;
          if((int)$row['correct']===1){
              $right++;
          }
        }
        
        $res['score']=$score;
        $res['right']=$right;
        $res['total']=$total;
        
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    
    public function initReport() {
        $this->reportInit();
    }
    
    
    public function reportInit() {
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        $quiz_report=$this->selectAssocRow("quiz_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(!$quiz_report) {
            $this->insertDB("quiz_report",array("game_id"=>$game_id,"user_id"=>$user_id,"time"=>time()));
            $quiz_report=$this->selectAssocRow("quiz_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        }
        return $quiz_report;
    }

    
    public function setReferrer($request){
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $quiz_report=$this->selectAssocRow("quiz_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(!$quiz_report) {
            $quiz_report=$this->reportInit();
        }
        
        if($quiz_report['referrer']=="") {
            $this->updateDB("quiz_report",array("referrer"=>$request['referrer']),$quiz_report['id']);
        }
        
        header('Content-Type:application/json');
        echo json_encode($quiz_report);
    }

    
    
    public function reportParaSet($request) {
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $para=$request['para'];
        $val=$request['val'];
        
        $quiz_report=$this->selectAssocRow("quiz_report","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(!$quiz_report) {
            $quiz_report=$this->reportInit();
        }
        $this->updateDB("quiz_report",array($para=>$val),$quiz_report['id']);
        
    }
  
}


$gt=new getters();
$gt->$op($REQ);

























