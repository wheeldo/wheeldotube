<?php
$just_class=true;
require 'billing.php';

$billing=new billing();


$last_cron=$dbop->selectAssocRow("billing_cron","ORDER BY `time` DESC LIMIT 0,1");

if($last_cron) {
    $last_tokens_track_id=$last_cron['last_tokens_track_id'];
}
else {
    $last_tokens_track_id=0;
}


$ans=$dbop->selectDB("tokens_track","WHERE `id`>'{$last_tokens_track_id}'");
$last_id=0;
$c=0;

$user_ids_collector=array();

for($i=0;$i<$ans['n'];$i++) {
  $row=mysql_fetch_assoc($ans['p']);
  
  if($row['cost']=="1") {
    $game_user_id=$dbop->selectAssocRow("games","WHERE `id`='{$row['game_id']}'");
    $owner=$game_user_id['owner'];
    $user_ids_collector[$owner][]=$row['time'];
  }
  
  $last_id=(int)$row['id'];
  $c++;
}


$time=time();
//$time=1412553600;
foreach($user_ids_collector as $user_id=>$collect):

    $user_plan=$dbop->selectAssocRow("users_plans","WHERE `user_id`='{$user_id}'");
    if(!$user_plan) {
        // if no plan create initial plan:
        $billing->createPlanUser($user_id);
        continue;
    }
    
    $period_start=$user_plan['period_start'];
    $period_end=$user_plan['period_end'];
    
    if((int)$user_plan['period_end']<$time) {
        vd("Creaing new period for user ".$user_id);
        // if billing period ended, creating new period:
        $period_start=(int)$user_plan['period_end'];
        $period_end=strtotime("+1 month",$new_period_start);
        
        $add_to_user=0;
        foreach($collect as $timestamp):
            if($timestamp>=$period_start&&$timestamp<$period_end) {
                $add_to_user++;
            }
        endforeach;
        
        $left=$user_plan['plan_plays']-$add_to_user;
        $use=100-floor($left/(int)$user_plan['plan_plays']*100);
        
        $dbop->updateDB("users_plans",array(
            "period_start"=>$period_start,
            "period_end"=>$period_end,
            "left"=>$left,
            "use"=>$use
        ),$user_plan['id']);
        
        ////////////////////////////////////////////////
    }
    else {
        vd("Updating usage for user ".$user_id);
        // if still in billing period, add the usage:
        $add_to_user=0;
        foreach($collect as $timestamp):
            if($timestamp>=$period_start&&$timestamp<$period_end) {
                $add_to_user++;
            }
        endforeach;
        
        $left=$user_plan['left']-$add_to_user;
        $use=100-floor($left/(int)$user_plan['plan_plays']*100);
        
        $dbop->updateDB("users_plans",array(
            "left"=>$left,
            "use"=>$use
        ),$user_plan['id']);
        /////////////////////////////////////////////
    }
    
    
    
    
endforeach;

if($last_id!=0) {
    $dbop->insertDB("billing_cron",array("time"=>time(), "last_tokens_track_id"=>$last_id));
}