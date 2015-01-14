<?php
class test_yourself extends dbop{
    
    public $game_data;
    public $game_design;
    public $settings;
    
    public function __construct($game_id,$user_id,$user) {
        
        $test_yourself_data=$this->selectAssocRow("test_yourself_data","WHERE `game_id`='{$game_id}'");
        
        
        //test_yourself_data_q_game
        $sql="SELECT
        test_yourself_data_questions.id,
        test_yourself_data_questions.text,
        test_yourself_data_questions.media,
        test_yourself_data_answers.id as ans_id,
        test_yourself_data_answers.text as ans_text,
        test_yourself_data_answers.media as ans_media,
        test_yourself_data_answers.strength as ans_strength
        FROM
        test_yourself_data_q_game
        INNER JOIN test_yourself_data_questions ON test_yourself_data_q_game.q_id = test_yourself_data_questions.id
        INNER JOIN test_yourself_data_answers ON test_yourself_data_questions.id = test_yourself_data_answers.question_id
        WHERE
        test_yourself_data_q_game.game_id = $game_id";
        $ans=$this->selectQDB($sql);
        
        
        $questions=array();
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $q_id=$row['id'];
          if(isset($questions[$q_id])) {
              $questions[$q_id]['answers'][]=array(
                "id"=>$row['ans_id'],
                "text"=>$row['ans_text'],
                "media"=>$row['ans_media'],
                "strength"=>$row['ans_strength']
              );

          }
          else {
              $questions[$q_id]['id']=$row['id'];
              $questions[$q_id]['text']=$row['text'];
              $questions[$q_id]['media']=$row['media'];
              $questions[$q_id]['answers']=array(
                  array(
                        "id"=>$row['ans_id'],
                        "text"=>$row['ans_text'],
                        "media"=>$row['ans_media'],
                        "strength"=>$row['ans_strength']
                      )
              );
          }
          
          
        }

        
        $ans=$this->selectDB("test_yourself_data_results","WHERE `game_id`='{$game_id}'");
        $results=array();
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $results[$i]=$row;
        }
        
        
        $game_data=array(
            "main_question"=>$test_yourself_data['main_question'],
            "main_question_image"=>$test_yourself_data['main_question_image'],
            "questions"=>$questions,
            "results"=>$results
        );
        
//        echo "<pre>";
//        print_r($game_data);
        $this->game_data=$game_data;
        
        
        // design:
        
        $this->game_design=$this->selectAssocRow("quiz_design","WHERE `game_id`='{$game_id}'");
        $this->settings=$this->selectAssocRow("quiz_settings","WHERE `game_id`='{$game_id}'");
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