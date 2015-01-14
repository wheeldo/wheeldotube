<?php
require_once 'top.php';





////////////////////////////////////// update plays quiz counters: //////////////////////////////////////////////////
$last_cron=$dbop->selectAssocRow("games_cron","ORDER BY `time` DESC LIMIT 0,1");

$last_game_user_id=$last_cron['last_game_user_id'];



@mysql_select_db( "wheeldotube_gamedata" , $dbop->getConn() ) or die( "error - unable to select database" );



$games_to_add_playes=array();
$ans=$dbop->selectDB("game_quiz_user","WHERE `id`>'{$last_game_user_id}'");
$last_id=0;
$c=0;
for($i=0;$i<$ans['n'];$i++) {
  $row=mysql_fetch_assoc($ans['p']);
  $game_id=$row['game_id'];
  if(isset($games_to_add_playes[$game_id]))
      $games_to_add_playes[$game_id]++;
  else
      $games_to_add_playes[$game_id]=1;
  $last_id=$row['id'];
  $c++;
}
echo "<pre>";


@mysql_select_db( "wheeldotube_main" , $dbop->getConn() ) or die( "error - unable to select database" );
foreach($games_to_add_playes as $game_id=>$nop):
    $game=$dbop->selectAssocRow("games","WHERE `id`='{$game_id}'");
    $plays=(int)$game['plays'];
    $plays+=$nop;
    $dbop->updateDB("games",array('plays'=>$plays),$game['id']);
endforeach;

if($last_id!=0)
    $dbop->insertDB("games_cron",array("time"=>time(), "last_game_user_id"=>$last_id));
echo "Quiz: done for $c recorders<br>";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////




////////////////////////////////////// update plays testyourself counters: //////////////////////////////////////////////////

$last_cron_testyourself=$dbop->selectAssocRow("games_cron_testyourself","ORDER BY `time` DESC LIMIT 0,1");
$last_id=$last_cron_testyourself?$last_cron_testyourself['last_cron_id']:0;

@mysql_select_db( "wheeldotube_gamedata" , $dbop->getConn() ) or die( "error - unable to select database" );
$ans=$dbop->selectDB("test_yourself_data_user_score","WHERE `id`>'{$last_id}'");

$c=0;

$games_to_add=array();
$last_updated_id=false;
for($i=0;$i<$ans['n'];$i++) {
  $row=mysql_fetch_assoc($ans['p']);
  $game_id=$row['game_id'];
  
  if(isset($games_to_add[$game_id])) {
      $games_to_add[$game_id]++;
  }
  else {
      $games_to_add[$game_id]=1;
  } 
  
  
  $last_updated_id=$row['id'];
}

@mysql_select_db( "wheeldotube_main" , $dbop->getConn() ) or die( "error - unable to select database" );


foreach($games_to_add as $game_id=>$add):
    $game=$dbop->selectAssocRow('games',"WHERE `id`='{$game_id}'");
    $plays=(int)$game['plays'];
    $dbop->updateDB("games",array("plays"=>$plays+$add),$game_id);
endforeach;

if($last_updated_id) {
    $dbop->insertDB("games_cron_testyourself",array("time"=>time(),"last_cron_id"=>$last_updated_id));
}


echo "Testyourself: done for ".count($games_to_add)." recorders<br>";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// check if game is over:
$games_to_update=array();
$ans=$dbop->selectDB("games","WHERE `prize`='1' AND `winner`=''");
for($i=0;$i<$ans['n'];$i++) {
    $game=mysql_fetch_assoc($ans['p']);
    $game_id=$game['id'];
    $end_time=$game['time']+(int)$game['prize_time_limit']*3600;


    @mysql_select_db( "wheeldotube_gamedata" , $dbop->getConn() ) or die( "error - unable to select database" );
  
  
    $ans2=$dbop->selectDB("game_quiz_user","WHERE `game_id`='{$game_id}' AND `ghost`='0' ORDER BY `score` DESC");
    for($j=0;$j<$ans2['n'];$j++) {
        $row1=mysql_fetch_assoc($ans2['p']);
        $last_time_row=$dbop->selectAssocRow("quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$row1['user_id']}' ORDER BY `time` DESC");
        $last_time=$last_time_row['time'];
        if($last_time<$end_time) {
            $winner=$row1;
            break;
        }
    }
    
    
    $games_to_update[$game_id]=$winner;
}



@mysql_select_db( "wheeldotube_main" , $dbop->getConn() ) or die( "error - unable to select database" );
//var_dump($games_to_update);

foreach($games_to_update as $game_id=>$winner):
    $dbop->updateDB("games",array("winner"=>$winner['user_name']),$game_id);
endforeach;
        

//////////////////