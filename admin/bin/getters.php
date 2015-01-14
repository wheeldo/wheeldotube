<?php
require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;

require_once 'google/appengine/api/taskqueue/PushTask.php';
use \google\appengine\api\taskqueue\PushTask;

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

if(isset($_SESSION['login'])) {
   
}
else {
    die("Blocked!");
}

class getters extends dbop{
    
    public function __consteuct() {
        return;
    }
    
    private function stripData($data) {
        $strippedData=str_replace ("___amp___","&",$data);
        return $strippedData;
    }
    
    public function setApprovedGame($request) {
        $this->updateDB("games",array("approved"=>$request['approved']),$request['game_id']);
        header('Content-Type:application/json');
        echo json_encode($request);
    }
    
    public function saveEditQuestionnaire($request) {
        $res=array();
        $questionnaire_id=$request['q_id'];
        $questionnaire=$request['questionnaire'];
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $this->updateDB("questionnaires",array(
            "name"=>$questionnaire['name'],
            "public"=>$questionnaire['public_q']
        ),$questionnaire_id);
        
        // tags:
        mysql_query("DELETE FROM `questionnaires_tags` WHERE `questionnaire_id`='{$questionnaire_id}'");
        $tags=explode(";",$questionnaire['tags']);
        foreach($tags as $tag):
            $this->insertDB("questionnaires_tags",array(
                "questionnaire_id"=>$questionnaire_id,
                "tag"=>strtolower($tag)
            ));
        endforeach;
        
        // questions:
        mysql_query("DELETE FROM `questionnaires_quiz` WHERE `questionnaire_id`='{$questionnaire_id}'");
        foreach($questionnaire['questions'] as $q):
            $q_id=$q['id'];
            $this->insertDB("questionnaires_quiz",array(
                "questionnaire_id"=>$questionnaire_id,
                "quiz_id"=>$q_id
            ));
        endforeach;

        header('Content-Type:application/json');
        echo json_encode($request);
    }
    
    public function loadQuestionnaire($request) {
        $res=array();
        $q_id=$request['q_id'];
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $questionnaire=$this->selectAssocRow("questionnaires","WHERE `id`='{$q_id}'");
        $res['name']=$questionnaire['name'];
        
        $res['public_q']=$questionnaire['public'];
        $res['private_code']=$questionnaire['secret_key'];
        $tags=array();
        
        $ans2=$this->selectDB("questionnaires_tags","WHERE `questionnaire_id`='{$questionnaire['id']}'");
        for($j=0;$j<$ans2['n'];$j++) {
          $row2=mysql_fetch_assoc($ans2['p']);
          $tags[]=ucfirst($row2['tag']);
        }
        
        $res['tags']=implode(";",$tags);
        
        $res['questions']=array();
        
        $sql="SELECT
        quiz.id,
        quiz.question
        FROM
        questionnaires_quiz
        INNER JOIN quiz ON questionnaires_quiz.quiz_id = quiz.id
        WHERE
        questionnaires_quiz.questionnaire_id = '$q_id'";
        
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['questions'][]=$row;
        }
        
        
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function searchQuestionnaireFree($request) {
        $res=array();
        $searchKey=$request['searchKey'];
        
        
        $sql="SELECT
        wheeldotube_gamedata.questionnaires.id,
        wheeldotube_gamedata.questionnaires.name,
        wheeldotube_gamedata.questionnaires.public,
        wheeldotube_gamedata.questionnaires.time,
        wheeldotube_gamedata.questionnaires.secret_key,
        wheeldotube_main.users.fname,
        wheeldotube_main.users.lname,
        wheeldotube_main.users.email,
        wheeldotube_main.users.image
        FROM
        wheeldotube_gamedata.questionnaires
        INNER JOIN wheeldotube_main.users ON wheeldotube_gamedata.questionnaires.creator = wheeldotube_main.users.id

        WHERE LOWER(Concat(wheeldotube_gamedata.questionnaires.name,wheeldotube_main.users.fname,wheeldotube_main.users.lname,wheeldotube_main.users.email)) like LOWER(\"%$searchKey%\")
        GROUP BY
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
    
    public function getTagsQuestionnaire() {
        $res=array();
                
        $sql="SELECT
        questionnaires_tags.tag
        FROM
        questionnaires_tags
        GROUP BY
        questionnaires_tags.tag";

        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res[]=  ucfirst($row['tag']);
        }

        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function searchQuestionnaireTags($request) {
        $res=array();
        $res['questionnaires']=array();
        
        $tags=explode(";",$request['tags']);

        $in_str="";
        $c=0;
        foreach($tags as $tag):
            if($c!=0)
                $in_str.=",";
            $in_str.="'".$tag."'";
            $c++;
        endforeach;
        
        //echo $in_str;
        
        $sql="SELECT
        wheeldotube_gamedata.questionnaires.id,
        wheeldotube_gamedata.questionnaires.name,
        wheeldotube_gamedata.questionnaires.public,
        wheeldotube_gamedata.questionnaires.time,
        wheeldotube_gamedata.questionnaires.secret_key,
        wheeldotube_main.users.fname,
        wheeldotube_main.users.lname,
        wheeldotube_main.users.email,
        wheeldotube_main.users.image
        FROM
        wheeldotube_gamedata.questionnaires
        INNER JOIN wheeldotube_gamedata.questionnaires_tags ON wheeldotube_gamedata.questionnaires.id = wheeldotube_gamedata.questionnaires_tags.questionnaire_id
        INNER JOIN wheeldotube_main.users ON wheeldotube_gamedata.questionnaires.creator = wheeldotube_main.users.id
        WHERE
                questionnaires_tags.tag IN ($in_str)
        GROUP BY
                questionnaires_tags.questionnaire_id";
        
        //echo $sql;
        
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
    
    public function countQie($request) {
        $res=array();
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectDB("questionnaires");
        $res['all_qie_no']=$ans['n'];
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function addToQuestionnaire($request) {
        $ids=$request['ids'];
        $questionnaire_id=$request['questionnaire_id'];
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        foreach($ids as $quiz_id):            
            $check=$this->selectAssocRow("questionnaires_quiz","WHERE `questionnaire_id`='{$questionnaire_id}' AND `quiz_id`='{$quiz_id}'");
            if(!$check):
                $this->insertDB("questionnaires_quiz",array(
                    "questionnaire_id"=>$questionnaire_id,
                    "quiz_id"=>$quiz_id
                 ));
            endif;            
        endforeach;
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function loadQuestionnaires($request) {
        $sql="SELECT
        wheeldotube_gamedata.questionnaires.id,
        wheeldotube_gamedata.questionnaires.`name`,
        wheeldotube_main.users.fname,
        wheeldotube_main.users.lname
        FROM
        wheeldotube_gamedata.questionnaires
        INNER JOIN wheeldotube_main.users ON wheeldotube_gamedata.questionnaires.creator = wheeldotube_main.users.id";
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res[]=$row;
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function createQuestionnaire($request) {
        $res=array();
        
        $ids=$request['ids'];
        $questionnaire=$request['questionnaire'];
        
        
        //questionnaires
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $questionnaire_id=$this->insertDB("questionnaires",array(
            "name"=>ucfirst($questionnaire['name']),
            "public"=>$questionnaire['public_q'],
            "secret_key"=>$questionnaire['private_code'],
            "time"=>time(),
            "creator"=>$_SESSION['login']['id']
        ));
        
        
        foreach($ids as $quiz_id):            
            $check=$this->selectAssocRow("questionnaires_quiz","WHERE `questionnaire_id`='{$questionnaire_id}' AND `quiz_id`='{$quiz_id}'");
            if(!$check):
                $this->insertDB("questionnaires_quiz",array(
                    "questionnaire_id"=>$questionnaire_id,
                    "quiz_id"=>$quiz_id
                 ));
            endif;            
        endforeach;
        
        
        //tags
        $tags=explode(";",$questionnaire['tags']);
        foreach($tags as $tag):
            $this->insertDB("questionnaires_tags",array(
                "questionnaire_id"=>$questionnaire_id,
                "tag"=>  strtolower($tag)
            ));
        endforeach;

        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function countQ($request) {
        $res=array();
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectDB("quiz");
        $res['all_q_no']=$ans['n'];
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function saveQ($request) {
        $res=array();
        $q_id=$request['q_id'];
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        
        $fields=array(
          "question"=>$request['question'],
          "rank"=>$request['rank']
        );
        $this->updateDB("quiz", $fields, $q_id);
        
        // answers:
        $ids=array();
        $ans=$this->selectDB("quiz_answers","WHERE `question_id`='{$q_id}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $ids[$row['id']]=1;
        }

        
        foreach($request['answers'] as $answer):
            $ans_id=$answer['id'];
            unset($answer['id']);
            
            if($ans_id==0) {
                $this->insertDB("quiz_answers",$answer);
            }
            else {
                unset($ids[$ans_id]);
                $this->updateDB("quiz_answers",$answer,$ans_id);
            }
        
        endforeach;
        
        
        
        foreach($ids as $id=>$e):
            $this->deleteDB("quiz_answers", $id);
        endforeach;

        ///////////
        
        
        // tags:
        $tags=explode(";",$request['tags']);
        
        mysql_query("DELETE FROM `quiz_tags` WHERE `quiz_id`='{$q_id}'");
        foreach($tags as $tag):
            $fields=array(
               "quiz_id"=>$q_id,
               "tag"=>strtolower($tag)
            );
            $this->insertDB("quiz_tags",$fields);
        endforeach;

        ////////
        
    }
    
    public function loadQ($request) {
        $res=array();
        $q_id=$request['q_id'];
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $res['q']=$this->selectAssocRow("quiz","WHERE `id`='{$q_id}'");
        
        $res['q']['answers']=array();
        $ans=$this->selectDB("quiz_answers","WHERE `question_id`='{$q_id}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['q']['answers'][]=$row;
        }
        
        
        $tags=array();
        $ans=$this->selectDB("quiz_tags","WHERE `quiz_id`='{$q_id}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $tags[]=$row['tag'];
        }
        $res['q']['tags']=implode(";",$tags);
        

        header('Content-Type:application/json');
        echo json_encode($res);
    }
    public function searchQCreator($request) {
        $res=array();
        $res['questions']=array();
        $searchKey=$request['searchKey'];
        
        
//        $creators=array();
//        $sql="SELECT * FROM user WHERE LOWER(Concat(email,fname,lname)) like LOWER(\"%$searchKey%\")";
//        $ans=$this->selectQDB($sql);
//        for($i=0;$i<$ans['n'];$i++) {
//          $row=mysql_fetch_assoc($ans['p']);
//          $creators[]=$row['id'];
//        }
        
        $sql="SELECT
        wheeldotube_gamedata.quiz.id,
        wheeldotube_gamedata.quiz.creator,
        wheeldotube_gamedata.quiz.question,
        wheeldotube_gamedata.quiz.time
        FROM
        wheeldotube_main.users
        INNER JOIN wheeldotube_gamedata.quiz ON wheeldotube_gamedata.quiz.creator = wheeldotube_main.users.id
         WHERE LOWER(Concat(wheeldotube_main.users.email,wheeldotube_main.users.fname,wheeldotube_main.users.lname)) like LOWER(\"%$searchKey%\")";
        
        

        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['questions'][$i]=$row;
          $res['questions'][$i]['h_time']=date("d/m/Y",$row['time']);
          $res['questions'][$i]['tags']=array();
          
          $ans2=$this->selectDB("quiz_tags","WHERE `quiz_id`='{$row['id']}'");
          for($j=0;$j<$ans2['n'];$j++) {
              $row_tag=mysql_fetch_assoc($ans2['p']);
              $res['questions'][$i]['tags'][]=ucfirst($row_tag['tag']);
          }
            
            
            
          @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );
          $creator_id=$row['creator'];
          $creator=$this->selectAssocRow("users","WHERE `id`='{$creator_id}'");
          $res['questions'][$i]['creator']=$creator['fname']." ".$creator['lname']." (".$creator['email'].")";
          $res['questions'][$i]['creator_image']=$creator['image'];
          @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        }

        header('Content-Type:application/json');
        echo json_encode($res);
        
    }
    
    public function searchQFree($request) {
        $res=array();
        $res['questions']=array();
        $searchKey=$request['searchKey'];
        
        $sql="SELECT
        quiz.id,
        quiz.creator,
        quiz.time,
        quiz.question
        FROM
        quiz
        INNER JOIN quiz_answers ON quiz.id = quiz_answers.question_id

        WHERE LOWER(Concat(quiz.question,quiz_answers.content)) like LOWER(\"%$searchKey%\")
        GROUP BY
        quiz.id";
        
        //echo $sql;
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['questions'][$i]=$row;
          $res['questions'][$i]['h_time']=date("d/m/Y",$row['time']);
          $res['questions'][$i]['tags']=array();
          
          $ans2=$this->selectDB("quiz_tags","WHERE `quiz_id`='{$row['id']}'");
          for($j=0;$j<$ans2['n'];$j++) {
              $row_tag=mysql_fetch_assoc($ans2['p']);
              $res['questions'][$i]['tags'][]=ucfirst($row_tag['tag']);
          }
            
            
            
          @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );
          $creator_id=$row['creator'];
          $creator=$this->selectAssocRow("users","WHERE `id`='{$creator_id}'");
          $res['questions'][$i]['creator']=$creator['fname']." ".$creator['lname']." (".$creator['email'].")";
          $res['questions'][$i]['creator_image']=$creator['image'];
          @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        }

        header('Content-Type:application/json');
        echo json_encode($res);
        
    }
    
    public function searchTags($request) {
        $res=array();
        
        $res['questions']=array();
        
        
        $tags=explode(";",$request['tags']);

        $in_str="";
        $c=0;
        foreach($tags as $tag):
            if($c!=0)
                $in_str.=",";
            $in_str.="'".$tag."'";
            $c++;
        endforeach;
        
        //echo $in_str;
        
        $sql="SELECT
        quiz.question,
        quiz.creator,
        quiz.id,
        quiz.time
        FROM
        quiz_tags
        INNER JOIN quiz ON quiz_tags.quiz_id = quiz.id
        WHERE
        quiz_tags.tag IN ($in_str)
        GROUP BY
        quiz_tags.quiz_id";
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['questions'][$i]=$row;
          $res['questions'][$i]['h_time']=date("d/m/Y",$row['time']);
          
          $res['questions'][$i]['tags']=array();
          $ans2=$this->selectDB("quiz_tags","WHERE `quiz_id`='{$row['id']}'");
          for($j=0;$j<$ans2['n'];$j++) {
              $row_tag=mysql_fetch_assoc($ans2['p']);
              $res['questions'][$i]['tags'][]=ucfirst($row_tag['tag']);
          }
            
            
            
          @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );
          $creator_id=$row['creator'];
          $creator=$this->selectAssocRow("users","WHERE `id`='{$creator_id}'");
          $res['questions'][$i]['creator']=$creator['fname']." ".$creator['lname']." (".$creator['email'].")";
          $res['questions'][$i]['creator_image']=$creator['image'];
          @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        }

        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function getTags() {
        $res=array();
                
        $sql="SELECT
        quiz_tags.tag
        FROM
        quiz_tags
        GROUP BY
        quiz_tags.tag";

        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res[]=  ucfirst($row['tag']);
        }

        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function saveUserData($request) {
        $user_data=$request['user_data'];
        $user_id=$user_data['id'];
        unset($user_data['id']);
        unset($user_data['h_time']);
        unset($user_data['password']);
        
        if(isset($user_data['new_password'])) {
            $user_data['password']=create_pa($user_data['new_password']);
            unset($user_data['new_password']);
        }
        $this->updateDB("users", $user_data, $user_id);
        
        
        header('Content-Type:application/json');
        echo json_encode($user_data);
    }
    
    public function loadUserData($request) {
        $user_id=$request['user_id'];
        $res=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        $res['h_time']=date("d/m/Y",$res['reg_time']);
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function deleteUser($request) {
        $user_id=$request['user_id'];
        
        $this->deleteDB("users", $user_id);
        
        
        mysql_query("DELETE FROM `channel_user` WHERE `user_id`='{$user_id}'");
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function getUsers($request) {
        $res=array();
        
        $search=$request['searchKeyUsers'];
        $dates=$request['dates'];
        
        $dates_query="";
        if($dates!="") {
            $ex=explode(" - ",$dates);
            $from=strtotime($ex[0]);
            $to=strtotime($ex[1])+24*3600;
            $dates_query=" AND users.reg_time BETWEEN $from AND $to ";
        }
        
        $sql="SELECT
        users.id,
        users.email,
        users.fname,
        users.lname,
        users.image,
        users.labels,
        from_unixtime(users.reg_time, '%Y %D %M %h:%i:%s') AS clock_time
        FROM
        users
        WHERE LOWER(Concat(email, '', fname, '', lname)) like LOWER(\"%$search%\")
        AND `ghost`=0
        $dates_query
        ORDER BY
        users.reg_time DESC";

        $res['users']=array();
        $ans=$this->selectQDB($sql);
        
        
        $channels_list=array();
        $channels_list_names=array();

        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['users'][$i]=$row;
          
          $res['users'][$i]['labels']=explode("|",$row['labels']);
          
          $res['users'][$i]['channels_str']="";
          $res['users'][$i]['channels_ids']="0";
          
          $sql2="SELECT
            channel_user.channel_id,
            channels.`name`
            FROM
            channel_user
            INNER JOIN channels ON channel_user.channel_id = channels.id
            WHERE
            channel_user.user_id = '{$row['id']}'";
          $ans2=$this->selectQDB($sql2);
          for($j=0;$j<$ans2['n'];$j++) {
              $r=mysql_fetch_assoc($ans2['p']);
              if($r['channel_id']!=0) {
                  $res['users'][$i]['channels_ids'].=",".$r['channel_id'];
                  if($res['users'][$i]['channels_str']!="") {
                      $res['users'][$i]['channels_str'].=", ";
                  }
                  $res['users'][$i]['channels_str'].=$r['name'];
                  $channels_list[]=$r['channel_id'];
                  
                  if(!isset($channels_list_names[$r['channel_id']])) {
                    $channels_list_names[$r['channel_id']]=$r['name'];
                  }
              }   
          }
        }
        
        $channels_list=array_unique($channels_list);
        
        $res['channels']=array();
        $res['channels'][0]="All";
        foreach($channels_list as $cid):
            $res['channels'][$cid]=$channels_list_names[$cid];
        endforeach;
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function addUserLabel($request) {
        $name=$request['name'];
        $this->insertDB("users_labels",array("name"=>$name));
    }
    
    public function loadUserLables() {
        $res['labels']=array();
        $ans=$this->selectDB("users_labels");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['labels'][$row['id']]=$row['name'];
        }
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function applyLabel($request) {
        $label_id=$request['label'];
        $ids=$request['ids'];
        $label=$this->selectAssocRow("users_labels","WHERE `id`='{$label_id}'");
        
        foreach($ids as $user_id):
            $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
            if($user) {
                $labels=explode("|",$user['labels']);
                
                if(!in_array($label['name'], $labels)) {
                    
                    if($labels[0]=="") {
                        unset($labels[0]);
                    }
                    
                    $labels[]=$label['name'];
                    $this->updateDB("users",array("labels"=>implode("|",$labels)),$user_id);
                }
            }
        endforeach;
        
        header('Content-Type:application/json');
        echo json_encode($request);
    }
    
    public function removeLabel($request) {
        $user_id=$request['user_id'];
        $label_index=$request['label_index'];
        
        $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        $labels=explode("|",$user['labels']);
        
        unset($labels[$label_index]);
        $this->updateDB("users",array("labels"=>implode("|",$labels)),$user_id);
        
    }
        
    
    public function getRow($request) {
        $res=array();
        $row=$request['row'];
        $res['game_ids']=array();
        $res['no']=8;
                
                
        $check=$this->selectAssocRow("games_rows","WHERE `row`='{$request['row']}'");
        if($check) {
            $res['game_ids']=json_decode($check['game_ids'],true);
            $res['no']=$check['no'];
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function saveRow($request) {
        $res=array();
        
        $fields=array();
        $fields['row']=$request['row'];
        $fields['no']=$request['no'];
        $fields['game_ids']=json_encode($request['game_ids']);
        $check=$this->selectAssocRow("games_rows","WHERE `row`='{$request['row']}'");
        
        if($check) {
            $this->updateDB("games_rows", $fields, $check['id']);
        }
        else {
            $this->insertDB("games_rows", $fields);
        }
        
        header('Content-Type:application/json');
        echo json_encode($request);
    }
    
    public function getGamesForChoose($request) {
        $this->getGames($request);
    }
    
    public function deleteGame($request) {
        $res=array();
        
        $game_id=$request['game_id'];
        
        $this->deleteDB("games",$game_id);
        header('Content-Type:application/json');
        echo json_encode($request);
    }
    
    public function getGames($request) {
        $res=array();
                
        $sql="SELECT
        games.id,
        games.`owner`,
        games.unique_id,
        games.time,
        from_unixtime(games.time, '%Y %D %M %h:%i:%s') AS clock_time,
        games.private,
        games.open_status,
        games.game_type,
        games.game_template,
        games.plays,
        games.name,
        games.thumbnail,
        games.full_desc,
        games.prize,
        games.approved,
        games.prize_text,
        games.prize_time_limit,
        games.winner,
        games.voucher,
        games.voucher_name,
        games.voucher_email_subject,
        games.voucher_email_content,
        users.fname,
        users.lname,
        channels.`name` as c_name
        FROM
        games
        INNER JOIN users ON games.`owner` = users.id
        INNER JOIN game_channel ON games.id = game_channel.game_id
        INNER JOIN channels ON game_channel.channel_id = channels.id
        ORDER BY
        games.time DESC";
        
        $res['games']=array();
        $ans=$this->selectQDB($sql);
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['games'][$row['id']]=$row;
          $res['games'][$row['id']]['id']=(int)$row['id'];
        }
        
        
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    
    public function getCampaigns() {
        $res=array();
        $ans=$this->selectDB("campaigns");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $row['time']=date("d/m/Y",$row['time']);
          $res['campaigns'][]=$row;
        }
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function createCampaign($request) {
        $name=$request['name'];
        $this->insertDB("campaigns",array(
            "name"=>ucfirst($name),
            "time"=>time()
        ));
        
        $this->getCampaigns();
    }
    
    public function delete_campaign($request) {
        $id=$request['id'];
        $this->deleteDB("campaigns", $id);
        $this->getCampaigns();
    }
    
    public function getCampaign($request) {
        $res=array();
        $id=$request['id'];
        
        $res['campaign']=$this->selectAssocRow("campaigns","WHERE `id`='{$id}'");
        $res['pages']=array();
        
        $ans=$this->selectDB("campaign_pages","WHERE `campaign`='{$id}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res['pages'][$i]=$row;
          $res['pages'][$i]['time']=date("d/m/Y",(int)$res['pages'][$i]['time']);
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function page_campaign_save($request) {
        
        $data=$request['data'];
        
        if($data['id']==0) {
            unset($data['id']);
            $data['link']=md5(time());
            $data['time']=time();
            $this->insertDB("campaign_pages",$data);
        }
        else {
            $id=$data['id'];
            unset($data['id']);
            $this->updateDB("campaign_pages",$data,$id);
        }
        
        $this->getCampaign(array("id"=>$data['campaign']));
    }
    
    public function deleteCampaignPage($request) {
        $id=$request['id'];
        $this->deleteDB("campaign_pages",$id);
    }
    
    public function saveCampaign($request) {
        $this->updateDB("campaigns",array(
            "name"=>$request['name']
        ),$request['id']);
        $this->getCampaigns();
    }
    
    public function saveCloseValue($request) {
        $this->updateDB("campaign_pages",array(
            "close"=>$request['close_val']
        ),$request['page_id']);
    }
    
    public function getImageUploadURL() {
        $options = [ 'gs_bucket_name' => CloudStorageTools::getDefaultGoogleStorageBucketName() ];
        $url=CloudStorageTools::createUploadUrl('/img_uploader', $options);
        echo json_encode(array("url"=>$url,"bucket"=>CloudStorageTools::getDefaultGoogleStorageBucketName()));
    }
    
    public function getFileData($REQ) {
        $res_mark=$REQ['res_mark'];
        $file_data=$this->selectAssocRow("file_data","WHERE `res_mark`='$res_mark'");
        //var_dump($file_data);
        @file_put_contents('test.txt',"\r\n".print_r($file_data,1),FILE_APPEND);
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
    
    
    public function createXslx($REQ) {
        
        $data_arr=$REQ['data_arr'];
        
        //var_dump($data_arr);
        $postData=$data_arr;

        $data = http_build_query($postData);
        $context = [
          'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                  "Content-Length: ".strlen($data)."\r\n".
                  "User-Agent:MyAgent/1.0\r\n",
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
    

   
    
}


$gt=new getters();
$gt->$op($REQ);

























