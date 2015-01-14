<?php

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

class billing extends dbop{
    
    public function __construct() {
        parent::__construct();
    }
    
    private function getCurrentPeriod($timestamp) {
        $c=1;
        $last_timestamp=$timestamp;
        $next_timestamp=0;
        while(1) {
            $last_timestamp=$next_timestamp;
            $next_timestamp=strtotime("+$c month",$timestamp);
            if($next_timestamp>time()) {
                break;
            }
            $c++;
        }
        
        return array(
            "period_start"=>$last_timestamp,
            "period_end"=>$next_timestamp
        );  
    }
    
    public function createPlanUser($user_id) {
        
        $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");
        $reg_time=$user['reg_time'];
        
        $p=$this->getCurrentPeriod($reg_time);
        $plan_id=1;
        if((int)$user['admin']===1) {
            $plan_id=0;
        }
        $plan=$this->selectAssocRow("plans","WHERE `plan_id`=$plan_id");

        $sql="SELECT
        tokens_track.id,
        tokens_track.ip,
        tokens_track.user_agent,
        tokens_track.time,
        tokens_track.user_id,
        tokens_track.game_id,
        tokens_track.cost
        FROM
        tokens_track
        INNER JOIN games ON tokens_track.game_id = games.id
        WHERE
        tokens_track.cost = 1 AND
        games.`owner` = $user_id AND
        tokens_track.time BETWEEN {$p['period_start']} AND {$p['period_end']}";
        $ans=$this->selectQDB($sql);
        $left=(int)$plan['tokens_p_m']-$ans['n'];
        $use=100-floor($left/(int)$plan['tokens_p_m']*100);
        
        $fields=array(
            "user_id"=>$user_id,
            "plan_id"=>$plan_id,
            "name"=>$plan['name'],
            "period_start"=>$p['period_start'],
            "period_end"=>$p['period_end'],
            "plan_plays"=>$plan['tokens_p_m'],
            "left"=>$left,
            "use"=>$use
        );
        

        $id=$this->insertDB("users_plans",$fields);
        return $this->selectAssocRow("users_plans","WHERE `id`='{$id}'");
    }
    
    private function getLast30daysUser($user_id) {
        $start_time=strtotime("-30 day",time());
        $bins=array();

        for($d=1;$d<=30;$d++) {
            $d_1=$d-1;
            $start=strtotime("+$d_1 day",$start_time);
            $end=strtotime("+$d day",$start_time);
            $bins[$d]['date']=date("d/m",$end);
            
            $bins[$d]['admins']=0;
            $bins[$d]['plays']=0;
            
            $sql="SELECT
            tokens_track.id,
            tokens_track.time,
            tokens_track.cost
            FROM
            tokens_track
            INNER JOIN games ON tokens_track.game_id = games.id
            WHERE
            games.`owner` = $user_id AND
            tokens_track.time BETWEEN $start AND $end";
            
            $ans=$this->selectQDB($sql);
            for($i=0;$i<$ans['n'];$i++) {
              $row=mysql_fetch_assoc($ans['p']);
              
              if($row['cost']==1) {
                  $bins[$d]['plays']++;
              }
              else {
                  $bins[$d]['admins']++;
              }
              
            }  
        }
        
        return $bins;
    }
    
    
    public function getDashboard($request) {
        $res=array();
        $user_id=(int)$_SESSION['login_user']['ID'];
        
        $user_plan=$this->selectAssocRow("users_plans","WHERE `user_id`='{$user_id}'");
        if(!$user_plan) {
            $user_plan=$this->createPlanUser($user_id);
        }
        
        $user_plan['end_per']=date("d/m/Y",$user_plan['period_end']);
        
        $res['user_plan']=$user_plan;
        $res['Last30days']=$this->getLast30daysUser($user_id);
        
        header('Content-Type:application/json');
        echo json_encode($res);
    }
    
    public function playToken($request) {
        $res=array();
        $gid=$request['gid'];
        
        $user_id=(int)$_SESSION['login_user']['ID'];
        $user=$this->selectAssocRow("users","WHERE `id`='{$user_id}'");

        $game=$this->selectAssocRow("games","WHERE `unique_id`='{$gid}'");
        $game_id=(int)$game['id'];
        $game_channel=$this->selectAssocRow("game_channel","WHERE `game_id`='{$game['id']}'");
	$channel_id=$game_channel['channel_id'];

        // cost: ////////////////////////
        $token_cost=1;
        // case owner:
        if((int)$game['owner']===$user_id){
            $token_cost=0;
        }
        //case menager:
        if($this->selectAssocRow("channle_admin","WHERE `channle_id`='{$channel_id}' AND `email`='{$user['email']}'")) {
            $token_cost=0;
        }
        // case admin:
        if($user['admin']==1) {
            $token_cost=0;
        }
        /////////////////////////////////
        
        $check=$this->selectAssocRow("tokens_track","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
        if(!$check):
            $fields = array(
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'time' => time(),
                'user_id' => $user_id,
                'cost' => $token_cost,
                'game_id' => $game_id
            );
            $this->insertDB("tokens_track",$fields);
        endif;
        //header('Content-Type:application/json');
        echo json_encode($check);
    }
    
}

if(!$just_class):
    $bl=new billing();
    $bl->$op($REQ);
endif;