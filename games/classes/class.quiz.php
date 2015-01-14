<?php
class quiz extends dbop{
    
    public $quiz_game_data;
    public $quiz;
    public $settings;
    public $game_quiz_user;
    public $game_design;
    public $game_progress = array(
        "total"=>0,
        "answerd"=>0
    );
    
    public function __construct($game_id,$user_id,$user,$extanded=false) {
        
        $this->settings=$this->selectAssocRow("quiz_settings","WHERE `game_id`='{$game_id}'");
        $this->game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        $this->quiz_game_data=$this->selectAssocRow("games","WHERE `game_id`='{$game_id}'");
        
        
        $design=$this->selectAssocRow("quiz_design","WHERE `game_id`='{$game_id}'");
        if($design) {
            $this->game_design=$design;
        }
        else {
            $this->game_design=$this->selectAssocRow("quiz_design","WHERE `game_id`='0'");
        }
        
        
        if(!$this->game_quiz_user) {
            $fields=array();
            $fields['game_id']=$game_id;
            $fields['user_id']=$user_id;
            $fields['user_name']=$user['fname']." ".$user['lname'];
            $fields['ghost']=$user['ghost'];
            $this->insertDB("game_quiz_user",$fields);
            $this->game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        }
        if($this->game_quiz_user['ghost']=="1" && $user['ghost']=="0") {
            $fields=array();
            $fields['user_name']=$user['fname']." ".$user['lname'];
            $fields['ghost']=$user['ghost'];
            $this->updateDB("game_quiz_user",$fields,$this->game_quiz_user['id']);
            $this->game_quiz_user=$this->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        }

        $noq=$this->settings['no_of_questions'];
        
        $sql="SELECT
        quiz.id AS q_id,
        quiz.question,
        quiz.media,
        quiz.more_text,
        quiz.rank,
        quiz_answers.content,
        quiz_answers.media as q_media,
        quiz_answers.right,
        quiz_answers.id AS ans_id
        FROM
        quiz_game
        INNER JOIN quiz ON quiz_game.quiz_id = quiz.id
        INNER JOIN quiz_answers ON quiz.id = quiz_answers.question_id
        WHERE
        quiz_game.game_id = '{$game_id}'";
        
        $quiz=array();
        $ans=$this->selectQDB($sql);
        
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $quiz[$row['q_id']]['question']=$row['question'];
          $quiz[$row['q_id']]['media']=$row['media'];
          $quiz[$row['q_id']]['more_text']=$row['more_text'];
          $quiz[$row['q_id']]['rank']=$row['rank'];
          if((int)$row['right']==1) {
            $quiz[$row['q_id']]['right']=(int)$row['ans_id'];
          }
          if($extanded) {
          $quiz[$row['q_id']]['answers'][$row['ans_id']]=array(
                        "content"=>$row['content'],
                        "media"=>$row['q_media'],
                  );
          }
          else {
              $quiz[$row['q_id']]['answers'][$row['ans_id']]=$row['content'];
          }
          
          
        }

        
        $Qc=0;
        $quiz_final=array();
        $q_left=0;
        foreach($quiz as $q_id=>$q):
            
            if($Qc>$noq)
              break;
            
            
            
            $this->game_progress['total']++;
            // check if user answer this q:
            $checkIfAnswer=$this->selectAssocRow("quiz_user","WHERE `question_id`='{$q_id}' AND `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
            if($checkIfAnswer&&!$extanded)
                continue;
            ///////////////////////////////
            
            $n_answers=$this->shuffle_assoc($q['answers']);
            $quiz_final[$q_id]=$q;
            $quiz_final[$q_id]['answers']=$n_answers;
            $Qc++;
            $q_left++;
            $this->game_progress['answerd']++;
        endforeach;
        
        
        $this->game_progress['answerd']=$this->game_progress['total']-$q_left+1;
        

        $this->quiz=$quiz_final;
        return;
    }
    
    private function shuffle_assoc($array) {
        $keys = array_keys($array);

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return $array;
    }
    
    
}
