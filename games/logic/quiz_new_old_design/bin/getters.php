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
    
    public function burnQ($REQ) {
        $res=array();
        $q_id=$REQ['q_id'];
        $q=$_SESSION['quiz'][$q_id];
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];

        $fields=array();
        $fields['game_id']=$game_id;
        $fields['user_id']=$user_id;
        $fields['question_id']=$q_id;
        $fields['correct']=0;
        $fields['score']=0;
        $fields['time']=time();

        $this->insertDB("quiz_user",$fields);
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        $this->updateDB("game_quiz_user",array("strikes"=>(int)$game_quiz_user['strikes']+1),$game_quiz_user['id']);
        
        header('Content-Type:application/json');
        echo json_encode($res);
        
    }
    
    public function getScore() {
        global $dbop;
        $res=array();
        
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        
        $res['score']=(int)$game_quiz_user['score'];
        header('Content-Type:application/json');
        echo json_encode($res);
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
    
    private function rank_text_from_number($num) {
        $num = (int)$num;
        if($num==0) return "";
        
        if($num==1||($num%10==1&&$num!=11))
            return $num."st";
        if($num==2||($num%10==2&&$num!=12))
            return $num."nd";
        if($num==3||($num%10==3&&$num!=13))
            return $num."rd";
        
        return $num."th";
    }
    
    public function useSkip($REQ) {
        $res=array();
        $q_id=$REQ['q_id'];
        $q=$_SESSION['quiz'][$q_id];
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if((int)$game_quiz_user['use_skip']==0) {
            $this->updateDB("game_quiz_user",array("use_skip"=>1),$game_quiz_user['id']);
            $_SESSION['skip'][$q_id]=1;
            
            
            $fields=array();
            $fields['game_id']=$game_id;
            $fields['user_id']=$user_id;
            $fields['question_id']=$q_id;
            $fields['correct']=0;
            $fields['score']=0;
            $fields['time']=time();

            $this->insertDB("quiz_user",$fields);
            
            $res['status']="ok";
        }
        else {
            $res['status']="faild";
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    
    public function useAdd60($REQ) {
        $res=array();
        $q_id=$REQ['q_id'];
        $q=$_SESSION['quiz'][$q_id];
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if((int)$game_quiz_user['use_plus_60']==0) {
            $this->updateDB("game_quiz_user",array("use_plus_60"=>1),$game_quiz_user['id']);
            $_SESSION['reduce_points'][$q_id]=30;
            $res['status']="ok";
        }
        else {
            $res['status']="faild";
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function get5050($REQ) {
        $res=array();
        $q_id=$REQ['q_id'];
        $q=$_SESSION['quiz'][$q_id];
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(count($q['answers'])>=3 && (int)$game_quiz_user['use_5050']==0) {
            $pull=array();
            foreach($q['answers'] as $ans_id=>$ans):
                if($ans_id!=$q['right'])
                    $pull[]=$ans_id;
            endforeach;

            $random_key = array_rand($pull, count($q['answers'])-2);
            if(!is_array($random_key))
                $random_key=array($random_key);
            foreach($random_key as $key):
                $res['dis'][]=$pull[$key];
            endforeach;
            $res["status"]="ok";
            $this->updateDB("game_quiz_user",array("use_5050"=>1),$game_quiz_user['id']);
        }
        else {
            $res["status"]="faild";
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function checkAns($REQ){
        global $dbop;
        $res=array();
        
        $user_id=$_SESSION['user_data']['id'];
        $game_id=$_SESSION['settings']['game_id'];
        
        $game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        
        
        
        $ans_id=$REQ['ans_id'];
        $q_id=$REQ['q_id'];
        $score=(int)$REQ['a_t'];
        
        
        
        $check=$this->selectAssocRow("quiz_user","WHERE `user_id`='{$user_id}' AND `game_id`='{$game_id}' AND `question_id`='{$q_id}'");
        
        if($check){
            $res['status']="faild";
        }
        else {
            $q=$_SESSION['quiz'][$q_id];
            $correct=0;
            if((int)$ans_id==(int)$q['right']) {
                $res['answer']="right";
                $correct=1;
                
                if(isset($_SESSION['reduce_points'][$q_id])) {
                    $score=$score-$_SESSION['reduce_points'][$q_id];
                    if($score<0)
                        $score=0;
                }
                $this->updateDB("game_quiz_user",array("score"=>(int)$game_quiz_user['score']+$score),$game_quiz_user['id']);
            }
            else {
                if(!isset($_SESSION['skip'][$q_id])) {
                    $this->updateDB("game_quiz_user",array("strikes"=>(int)$game_quiz_user['strikes']+1),$game_quiz_user['id']);
                }                
                $score=0;
                $res['answer']="wrong";
                $res['right_answer']=(int)$q['right'];
            }

            $fields=array();
            $fields['game_id']=$game_id;
            $fields['user_id']=$user_id;
            $fields['question_id']=$q_id;
            $fields['correct']=$correct;
            $fields['score']=$score;
            $fields['time']=time();

            $this->insertDB("quiz_user",$fields);
            //echo mysql_error();

            $res['status']="ok";
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }


        
    public function loadQuestions($REQ) {
        
        $res=array();
        $i=0;
        foreach($_SESSION['quiz'] as $q_id=>$q):
            unset($q['right']);
            unset($q['rank']);
            $res[$i]=$q;
            $res[$i]['q_id']=$q_id;
            $i++;
        endforeach;
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function getDictionary($REQ) {
        global $dbop;
        $res=array();

        $dictionary=array();
        $dictionary['he']=array();
        $dictionary['en']=array();

        $lang=$REQ['lang'];


        $ans=$dbop->selectDB("dictionary");

        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res[$row['key']]=$row[$lang];
        }
        echo json_encode($res);
    }
    
   

  
}


$gt=new getters();
$gt->$op($REQ);

























