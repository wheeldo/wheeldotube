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

class report extends dbop{
    public function __construct() {
        parent::__construct();
        return;
    }
    
    
    private function gameTypeName($type) {
        
        switch((int)$type):
            case 1:
                return "Pro Quiz";
                
            case 2:
                return "Quiz";
            
            case 3:
                return "Personality Test";
                
        endswitch;

    }
    
    

    public function loadGeneralData($request) {
        $res=array();
        $gid=$request['gid'];
        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");
        
        
        $channels="";
        $ans=$this->selectDB("game_channel","WHERE `game_id`='{$game['id']}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $channel=$this->selectAssocRow("channels","WHERE `id`='{$row['channel_id']}'");
          if($i!=0)
              $channels.=", ";
          $channels.=$channel['name'];
        }
        
        $res['raw'][]=array("ID",$gid." (".$game['id'].")");
        $res['raw'][]=array("Name",$game['name']);
        $res['raw'][]=array("Type",$this->gameTypeName($game['game_type']));
        $res['raw'][]=array("Channel",$channels);
        $res['raw'][]=array("Create",date("d/m/Y",$game['time']));
        $res['raw'][]=array("Plays",$game['plays']);
        $res['thumbnail']=$game['thumbnail'];
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function trafficDataLoad($request) {
        $res=array();
        $gid=$request['gid'];
        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");

        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        switch((int)$game['game_type']):
            case 2:
                $res['total']=0;
                $res['referres']=array();
                $res['referres_data']=array();
                $res['players']=array();
                $ans=$this->selectDB("quiz_report","WHERE `game_id`='{$game['id']}'");
                for($i=0;$i<$ans['n'];$i++) {
                  $row=mysql_fetch_assoc($ans['p']);
                  $res['players'][]=$row;
                  $referrer=$row['referrer'];
                  if(isset($res['referres'][$referrer])) {
                      $res['referres'][$referrer]++;
                      $res['referres_data'][$referrer][]=$this->getUserMin($row['user_id']);
                  }
                  else {
                      $res['referres'][$referrer]=1;
                      $res['referres_data'][$referrer][]=$this->getUserMin($row['user_id']);
                  }
                  $res['total']++;
                }
            break;
            case 3:
                $res['total']=0;
                $res['referres']=array();
                $res['referres_data']=array();
                $res['players']=array();
                $ans=$this->selectDB("test_yourself_data_report","WHERE `game_id`='{$game['id']}'");
                for($i=0;$i<$ans['n'];$i++) {
                  $row=mysql_fetch_assoc($ans['p']);
                  $res['players'][]=$row;
                  $referrer=$row['referrer'];
                  if(isset($res['referres'][$referrer])) {
                      $res['referres'][$referrer]++;
                      $res['referres_data'][$referrer][]=$this->getUserMin($row['user_id']);
                  }
                  else {
                      $res['referres'][$referrer]=1;
                      $res['referres_data'][$referrer][]=$this->getUserMin($row['user_id']);
                  }
                  $res['total']++;
                }
            break;
        endswitch;
                
        header('Content-Type:application/json');
        echo json_encode($res);    
                
    }
    
    
    private function getUserMin($user_id) {
        
        @mysql_select_db( "wheeldotube_main" , $this->getConn() ) or die( "error - unable to select database" );
        $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        $user_bin=array(
            "ID"=>$user['id'],
            "name"=>$user['fname']." ".$user['lname'],
            "email"=>$user['email'],
            "image"=>$user['image'],
            "country"=>$user['country']
        );
        
        return $user_bin;
    }
    
    public function resultsDataLoad($request) {
        $res=array();
        $gid=$request['gid'];
        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");

        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        switch((int)$game['game_type']):
            case 2:
                $res['total_results']=0;
                $res['players_list']=array();
                $ans=$this->selectDB("game_quiz_user","WHERE `game_id`='{$game['id']}'");
                for($i=0;$i<$ans['n'];$i++) {
                  $row=mysql_fetch_assoc($ans['p']);
                  if((int)$row['user_id']<1) {
                      continue;
                  }
                  
                  @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die("Error - unable to select database");
                  
                  $more_data=$this->selectAssocRow("quiz_report","WHERE `user_id`='{$row['user_id']}' AND `game_id`='{$game['id']}'");
                  $res['players_list'][$i]=$this->getUserMin($row['user_id']);
                  $res['players_list'][$i]['time']=$more_data?date("d/m/Y",$more_data['time']):"_";
                  $res['players_list'][$i]['skip_reg']=$more_data?(int)$more_data['skip_reg']:0;
                  $res['players_list'][$i]['score']=$row['score'];
                  $res['players_list'][$i]['strikes']=$row['strikes'];
                  $res['total_results']++;
                }
                
                @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
                $res['leaders']=array();
                $ans=$this->selectDB("game_quiz_user","WHERE `game_id`='{$game['id']}' ORDER BY score DESC LIMIT 0,10");
                for($i=0;$i<$ans['n'];$i++) {
                  $row=mysql_fetch_assoc($ans['p']);
                  $more_data=$this->selectAssocRow("quiz_report","WHERE `user_id`='{$row['user_id']}' AND `game_id`='{$row['game_id']}'");
                  $res['leaders'][$i]=$this->getUserMin($row['user_id']);
                  $res['leaders'][$i]['score']=$row['score'];
                }
            break;
            case 3:
                
                //game results:
                $res=array();
                $ans=$this->selectDB("test_yourself_data_results","WHERE `game_id`='{$game['id']}'");
                for($i=0;$i<$ans['n'];$i++) {
                  $row=mysql_fetch_assoc($ans['p']);
                  $results[]=$row['text'];
                }
                $res['game_results']=$results;
                //////////////
                
                $res['total_results']=0;
                $res['players_list']=array();
                $ans=$this->selectDB("test_yourself_data_user_score","WHERE `game_id`='{$game['id']}'");
                
                
                for($i=0;$i<$ans['n'];$i++) {
                  $row=mysql_fetch_assoc($ans['p']);
                  @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die("Error - unable to select database");
                  $more_data=$this->selectAssocRow("test_yourself_data_report","WHERE `user_id`='{$row['user_id']}' AND `game_id`='{$row['game_id']}'");
                  $res['players_list'][$i]=$this->getUserMin($row['user_id']);
                  $res['players_list'][$i]['start_time']=$row['start_time'];
                  $res['players_list'][$i]['time']=date("d/m/Y",$row['start_time']);
                  $res['players_list'][$i]['skip_reg']=$more_data?(int)$more_data['skip_reg']:0;
                  $res['players_list'][$i]['score']=$row['score'];
                  $res['players_list'][$i]['result']=$this->getResultText($row['score'],$results);
                  $res['total_results']++;
                }
                
                
            break;
        endswitch;
                
        header('Content-Type:application/json');
        echo json_encode($res);   
    }
    
    private function getResultText($score,$results) {
        $l=count($results);
        $part=100/$l;
        if($score==100) {
            $score=99;
        }
        $r=floor($score/$part)+1;
        return $r;
    }
    
    public function leadsDataLoad($request) {
        $res=array();
        $gid=$request['gid'];
        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");
        
        
        
        
        $ans=$this->selectDB("game_leads","WHERE `game_id`='{$game['id']}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $res[$i]=$row;
          unset($res[$i]['id']);
          unset($res[$i]['game_id']);
          unset($res[$i]['user_id']);
          $res[$i]['reg_time']=date("F j, Y",$row['reg_time']);
        }
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function funnelDataLoad($request) {
        $res=array();
        $gid=$request['gid'];
        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");
        switch((int)$game['game_type']):
            case 2:
                $tbl="quiz_report";
            break;
            case 3:
                $tbl="test_yourself_data_report";
            break;
        endswitch;
        
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
    }
    
    public function showUserMoreData($request) {
        $res=array();
        $gid=$request['gid'];
        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");
        $user_id=$request['user_id'];
        $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        $game_id=$game['id'];
        $start_time=$request['start_time'];
        @mysql_select_db( "wheeldotube_gamedata" , $this->getConn() ) or die( "error - unable to select database" );
        $score=$this->selectAssocRow("test_yourself_data_user_score","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}' AND `start_time`='{$start_time}'");
        $res['score']=$score['score'];
        
        $res['user_name']=$user['fname']." ".$user['lname'];
        $res['start_date']=date("m/d/Y h:i",$start_time);
        
        $res['questions']=array();
        $ans=$this->selectDB("test_yourself_data_user_q","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}' AND `start_time`='{$start_time}'");
        for($i=0;$i<$ans['n'];$i++) {
          $row=mysql_fetch_assoc($ans['p']);
          $q_id=$row['q_id'];
          $q=$this->selectAssocRow("test_yourself_data_questions","WHERE `id`='{$q_id}'");
          $ans_id=$row['ans_id'];
          $answer=$this->selectAssocRow("test_yourself_data_answers","WHERE `id`='{$ans_id}'");
          $res['questions'][$i]['q']=$q['text'];
          $res['questions'][$i]['ans']=$answer['text']." (".$answer['strength']." points)";
        }
        
        echo json_encode($res);
    }
}

$ut=new report();
$ut->$op($REQ);



