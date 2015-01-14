<?php
require_once 'top_functions.php';
$have_sasseion=isset($_SESSION['user'])?true:false;


function stripData($data) {
    $strippedData=str_replace ("___amp___","&",$data);
    return $strippedData;
}

$REQ=array();
foreach($_GET as $key=>$value):
    $REQ[mysql_real_escape_string($key)]=mysql_real_escape_string($value);
endforeach;
unset($_GET);

foreach($_POST as $key=>$value):
    $REQ[$key]=$value;
endforeach;
unset($_POST);

$op=$REQ['op'];

if(!function_exists($op))
    die();


$op($REQ);


function report($REQ) {
    global $dbop;
    $res=array();
    $appID=$REQ['appID'];
    
    $res['q_types_q']=array();
    $ans=$dbop->selectDB("q_types_q","WHERE `appID`='{$appID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      $res['q_types_q'][$i]['q']=$row;
      $res['q_types_q'][$i]['stats']=$dbop->selectAssocRow('q_types_q_stats',"WHERE `q_id`='{$row['q_id']}'");
      $res['q_types_q'][$i]['user_data']=array();
      
      $ans2=$dbop->selectDB("q_types_q_user_data","WHERE `appID`='{$appID}' AND `q_id`='{$row['q_id']}'");
        for($j=0;$j<$ans2['n'];$j++) {
          $row2=mysql_fetch_assoc($ans2['p']);
          $res['q_types_q'][$i]['user_data'][]=$row2;
      }
      
      //$dbop->selectAssocRow('q_types_q_user_data',"WHERE `q_id`='{$row['q_id']}'");
    }
    
    $res['scores']=array();
    $ans=$dbop->selectDB("scores","WHERE `appID`='{$appID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      $res['scores'][]=$row;
    }
    
    
    $res['users']=array();
    $ans=$dbop->selectDB("users","WHERE `appID`='{$appID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      
      
      $res['users'][]=$row;
    }

    $result_array=array();
    $ans=$dbop->selectDB("users","WHERE `appID`='{$appID}'");
    $res['new_score_by_id']=array();
    for($i=0;$i<$ans['n'];$i++) {

        $user=mysql_fetch_assoc($ans['p']);
        
        $newScore=reCalculateScore($appID,$user['user_id']);
        $res['new_score_by_id'][$user['user_id']]=$newScore;
        if($user['userDepartment']!="") {
            if(!isset($result_array['Department']))
                $result_array['Department'][]=$user['userDepartment'];
            else {
                if(!in_array($user['userDepartment'], $result_array['Department'])) {
                    $result_array['Department'][]=$user['userDepartment'];
                }
            }
        }
        
        if($user['userPosition']!="") {
            if(!isset($result_array['Position']))
                $result_array['Position'][]=$user['userPosition'];
            else {
                if(!in_array($user['userPosition'], $result_array['Position'])) {
                    $result_array['Position'][]=$user['userPosition'];
                }
            }
        }
        
        if($user['userLevel']!="") {
            if(!isset($result_array['Level']))
                $result_array['Level'][]=$user['userLevel'];
            else {
                if(!in_array($user['userLevel'], $result_array['Level'])) {
                    $result_array['Level'][]=$user['userLevel'];
                }
            }
        }
        
        
        
        for($k=1;$k<=10;$k++):
            if($user["general_field_$k"]!="") {
                if(!isset($result_array["Custom_$k"]))
                    $result_array["Custom_$k"][]=$user["general_field_$k"];
                else {
                    if(!in_array($user["general_field_$k"], $result_array["Custom_$k"])) {
                        $result_array["Custom_$k"][]=$user["general_field_$k"];
                    }
                }
            }
            
        endfor;
    }

    $res['filters']=$result_array;
    
    header('Content-Type:application/json');
    echo json_encode($res);
}

function reCalculateScore($appID,$userID) {
    global $dbop;
    $score=0;

    $ans=$dbop->selectDB("q_types_q","WHERE `appID`='{$appID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      $score+=(int)calculateScoreQ($appID,$userID,$row['q_id'],$row['type']);
    }
    
    return $score;
}
    
    
    
function calculateScoreQ($appID,$userID,$q_id,$type) {
    global $dbop;
    $res=array();
    $res['earn']=0;
    $user_ans=$dbop->selectAssocRow("q_types_q_user_data","WHERE `appID`='$appID' AND `q_id`='$q_id' AND `userID`='{$userID}' and `p`=1 ");
    if(!$user_ans) return 0;
    switch((int) $type):
        case 1:
            $data=json_decode(stripslashes($user_ans['data']),true);
            $userValue=$data['value'];
            
            $q_stat=$dbop->selectAssocRow("q_types_q_stats","WHERE `appID`='{$appID}' AND `q_id`='{$q_id}' AND `p`='0'");
            $value=(int)$q_stat['stats'];
            $counter=(int)$q_stat['counter'];
            $avg=$value/$counter;
            $dif=(abs($avg-$userValue));
            $res['dif']=floor($dif);
            //////////////////////
            // formula ///////////
            $max_points=120;
            $earn=((100-$dif)/100)*120;
            //////////////////////
            $res['earn']=ceil($earn);
        break;
        case 3:
            $data=json_decode(stripslashes($user_ans['data']),true);
            
            
            
            $q_stat=$dbop->selectAssocRow("q_types_q_stats","WHERE `appID`='{$appID}' AND `q_id`='{$q_id}' AND `p`='0'");
            $options=json_decode(stripslashes($q_stat['stats']),true);
            $counter=(int)$q_stat['counter'];
            
            $c=0;
            $max_points=120;
            $options_c=count($options);
            $points_per_answer=ceil($max_points/$options_c);
            
            
            
            $points=0;
            $perC=100;
            $per_per_answer=ceil(100/$options_c);
            foreach($options as $option):
                $avg=ceil($option/$counter*100);
                if(in_array($c, $data['options'])&&$avg>=50) {
                    $points+=$points_per_answer;
                    $perC-=$per_per_answer;
                }
               
                if(!in_array($c, $data['options'])&&$avg<=50) {
                    $points+=$points_per_answer;
                    $perC-=$per_per_answer;
                }
                $c++;
            endforeach;
            $res['dif']=floor($perC);
            $res['earn']=ceil($points);
        break;
    endswitch;
    
    
    return $res['earn'];
}


function reportFilteredWS($request) {
    $res=array();
    global $dbop;
    $appID=$request['appID'];
    $data=json_decode(stripslashes(stripData($request['data'])),true);
    
    
    $str="";
    $filteredArray=array();
    foreach($data as $fltr=>$row):
        $filter=replaceNamesReport($fltr);
        $str.=" AND (";
        $filteredArray[$filter]=array();
        $c=0;
        foreach($row as $val):
            if($c!=0)
                $str.=" OR ";
            $str.=" `$filter`='$val' ";
            $filteredArray[$filter][]=$val;
        
            $c++;
        endforeach; 
        
        $str.=")";
    endforeach;
    
    
    
    
    $usersIds=array();
    $sql="SELECT * FROM `users` WHERE `appID`='$appID' $str";
    $p=mysql_query($sql);
    $n=mysql_num_rows($p);
    for($i=0;$i<$n;$i++):
        $r=mysql_fetch_assoc($p);
        $usersIds[]=$r['user_id'];
    endfor;

    
    
    $res['q_types_q']=array();
    $ans=$dbop->selectDB("q_types_q","WHERE `appID`='{$appID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      $res['q_types_q'][$i]['q']=$row;
      $res['q_types_q'][$i]['stats']=$dbop->selectAssocRow('q_types_q_stats',"WHERE `q_id`='{$row['q_id']}'");
      $res['q_types_q'][$i]['user_data']=array();
      
      $ans2=$dbop->selectDB("q_types_q_user_data","WHERE `appID`='{$appID}' AND `q_id`='{$row['q_id']}'");
        for($j=0;$j<$ans2['n'];$j++) {
          $row2=mysql_fetch_assoc($ans2['p']);
          if(in_array($row2['userID'],$usersIds)) {
                $res['q_types_q'][$i]['user_data'][]=$row2;
          }
      }
    }
    
    
    $res['scores']=array();
    $ans=$dbop->selectDB("scores","WHERE `appID`='{$appID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      if(in_array($row['userID'],$usersIds))
        $res['scores'][]=$row;
    }
    
    
    $ans=$dbop->selectDB("users","WHERE `appID`='{$appID}'");
    for($i=0;$i<$ans['n'];$i++) {
        $user=mysql_fetch_assoc($ans['p']);
        if(in_array($user['user_id'],$usersIds))
            $res['users'][]=$user;
    }
    
    
    header('Content-Type:application/json');
    echo json_encode($res);
}

function replaceNamesReport($name) {
    switch($name):
        case "Department":
            return "userDepartment";
        break;
        case "Position":
            return "userPosition";
        break;
        case "Level":
            return "userLevel";
        break;
        default:
            return str_replace("Custom_", "general_field_", $name);
        break;
    endswitch;
}


// GuessWot/api/duplicate/[oldID]/[newID]

function duplicate($request) {
    global $dbop;
    $oldID=$request['appID'];
    $newID=$request['ex'];
    
    // games
    $check1=$dbop->selectAssocRow('games',"WHERE `appID`='{$newID}'");
    if(!$check1)
        $dbop->insertDB("games",array("appID"=>$newID));
    
    // game_info
    $check2=$dbop->selectAssocRow('game_info',"WHERE `appID`='{$newID}'");
    if($check2)
        $dbop->deleteDB('game_info',$check2['id']);
    
    $oldRow=$dbop->selectAssocRow('game_info',"WHERE `appID`='{$oldID}'");
    
    unset($oldRow['id']);
    $oldRow['game_set']=0;
    $oldRow['appID']=$newID;
    $dbop->insertDB("game_info",$oldRow);
    
    
    
    mysql_query("DELETE FROM `q_types_q` WHERE `appID`='{$newID}'");
    // copy questions:
    $ans=$dbop->selectDB("q_types_q","WHERE `appID`='{$oldID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      
      $row['appID']=$newID;
      unset($row['id']);
      $dbop->insertDB("q_types_q",$row);
    }
    
    
    
    
    echo "duplicate set!";
}


// GuessWot/api/onStart/[appID]
function onStart($request) {
    global $dbop;
    $appID=$request['appID'];
    
    $check=$dbop->selectAssocRow('game_info',"WHERE `appID`='{$appID}'");
    
//    if($check['game_set']==1)
//        die("onStart not set!");
    
    
    
    
    /////////////
    $session = WheelDoSession::createSession(0, $appID);
    
    $url="http://api.wheeldo.com/APIAD.php";
    
    $postArray=array();
    $postArray['request']='teamByAppID';
    $postArray['function_data[appID]']=$appID;
    $postArray = array_merge($postArray,$session->getSessionData());
    $response=doRequest($url,$postArray);
    $response_as_array=json_decode($response,true);

    foreach($response_as_array as $user):
        set_time_limit(2);
        //print_r($user);
        $fields=array();
        $fields['appID']=$appID;
        $fields['user_id']=$user['ID'];
        $fields['user_name']=$user['name'];
        $fields['userDepartment']=$user['userDepartment'];
        $fields['userPosition']=$user['userPosition'];
        $fields['userLevel']=$user['userLevel'];
        $fields['general_field_1']=$user['general_field_1'];
        $fields['general_field_2']=$user['general_field_2'];
        $fields['general_field_3']=$user['general_field_3'];
        $fields['general_field_4']=$user['general_field_4'];
        $fields['general_field_5']=$user['general_field_5'];
        $fields['general_field_6']=$user['general_field_6'];
        $fields['general_field_7']=$user['general_field_7'];
        $fields['general_field_8']=$user['general_field_8'];
        $fields['general_field_9']=$user['general_field_9'];
        $fields['general_field_10']=$user['general_field_10'];
        $fields['photo']=$user['photo'];
        $fields['empID']=$user['empID'];
        $fields['hashedEmail']=$user['hashedEmail'];

        $check=$dbop->selectAssocRow('users',"WHERE `user_id`='{$user['ID']}' AND `appID`='$appID'");
        if($check){
            $dbop->updateDB('users',$fields,$check['id']);
        }
        else {
            $dbop->insertDB('users',$fields);
        }
        
    endforeach;
    
    
    $postArray=array();
    $postArray['request']='getUserOrgLogo';
    $postArray['function_data[userID]']=$user['ID'];
    $postArray = array_merge($postArray,$session->getSessionData());
    $orgLogo=doRequest($url,$postArray);
    
    //////////
    //orgLogo
    $dbop->updateDB('game_info',array("start"=>time(),"game_set"=>1,"orgLogo"=>$orgLogo),$appID,"appID");
    
    
    echo "onStart set!";
}

function cron($request) {
    set_time_limit(5*60);
    $debug=false;
    if(!$debug)
        ob_start();
    echo "<h5>Cron job:</h5><pre>";
    global $dbop;
    $ans=$dbop->selectDB("game_info","WHERE `start`>0 AND `game_set`='1' AND `end_time`>0 AND `cron_done`=0");
    for($i=0;$i<$ans['n'];$i++) {
        $row=mysql_fetch_assoc($ans['p']);
        $appID=$row['appID'];
        $end_time=$row['end_time'];
        $game_info_id=$row['id'];
        echo "<hr />";
        echo "<h5>AppID: $appID</h5>";
        // get last cron //
        $ans_game_crons=$dbop->selectDB("game_crons","WHERE `appID`='$appID' ORDER BY `time` DESC");
        $last_cron=mysql_fetch_assoc($ans_game_crons['p']);
        if(!$last_cron) {
            // first cron
            $res=sendBoxesToPercent($appID,50);
            $fields=array();
            $fields['appID']=$appID;
            $fields['time']=time();
            $fields['cron_json']=$res;
            $fields['cron_counter']=1;
            $dbop->insertDB("game_crons",$fields);
           // var_dump($res);
        }
        else {
            // check if less than 24 hours to end:
            
            $timetoEnd=($end_time-time())/3600; // in hours
            $timeFromLastCron=(time()-$last_cron['time'])/3600; // in hours
            $cron_counter=(int)$last_cron['cron_counter'];
            $cron_counter++;
            
            
//            echo "timetoEnd: ".$timetoEnd;
//            
//            echo "timeFromLastCron: ".$timeFromLastCron;
            
            if($timetoEnd<24) {
                echo "last time";
                // send for all the rest:
                $res=sendBoxesToPercent($appID,100);
                $fields=array();
                $fields['appID']=$appID;
                $fields['time']=time();
                $fields['cron_json']=$res;
                $fields['cron_counter']=$cron_counter;
                $dbop->insertDB("game_crons",$fields);
                // update that cron is done:
                $dbop->updateDB("game_info",array("cron_done"=>1),$game_info_id); 
            }
            elseif($timeFromLastCron>24) {
                echo "$cron_counter time";
                // send for 50% out of the rest:
                $res=sendBoxesToPercent($appID,50);
                $fields=array();
                $fields['appID']=$appID;
                $fields['time']=time();
                $fields['cron_json']=$res;
                $fields['cron_counter']=$cron_counter;
                $dbop->insertDB("game_crons",$fields);
            }
        }
    }

    if(!$debug) {
    $content = ob_get_clean();
    $subject="GuessWot Cron";
    $url="http://api.wheeldo.com/APIAD.php";
    $postArray=array();
    $postArray['request']='sendMailFromName';
    $postArray['function_data[appID]']=0;
    $postArray['function_data[fromName]']='System';
    $postArray['function_data[userID]']=71;
    $postArray['function_data[subject]']=$subject;
    $postArray['function_data[content]']=$content;

    echo "<pre>";
    }
}

function editWS($request) {
    global $dbop;
    $res=array();

    $edit_op=$request['edit_op'];
    $copyID=$request['copyID'];
    
    switch($edit_op):
        case "get":
            $res['game_info']=$dbop->selectAssocRow("game_info","WHERE `appID`='{$copyID}'");
            
            $ans=$dbop->selectDB("q_types");
            $types=array();
            for($i=0;$i<$ans['n'];$i++) {
              $row=mysql_fetch_assoc($ans['p']);
              $types[]=$row;
            }
            $res['q_types']=$types;
            $res['questions']=array();
            
            
            
            $ans=$dbop->selectDB("q_types_q","WHERE `appID`='{$copyID}'");
            for($i=0;$i<$ans['n'];$i++) {
                    $row=mysql_fetch_assoc($ans['p']);
                    $q=json_decode($row['text'],true);
                    $res['questions'][$i]=$q;
            }
            
        break;
        case "set":
            $data=json_decode(stripslashes(stripData($request['data'])),true);
            $fields=array();
            $fields['anonymous']=$data['game_info']['anonymous'];
            $dbop->updateDB("game_info",$fields,$data['game_info']['id']);
            
            
            mysql_query("DELETE FROM `q_types_q` WHERE `appID`='{$copyID}'");
            $c=0;
            foreach($data['questions'] as $q):
                $q_id=$q['q_id'];    
            
                if($q_id==0)
                    $q_id=$copyID."_".$c."_".time();
                $fields=array();
                $fields['appID']=$copyID;
                $fields['q_id']=$q_id;
                $fields['type']=$q['type'];
                
                $q['q_id']=$q_id;
                $fields['text']=json_encode($q);
                $dbop->insertDB("q_types_q",$fields);
                echo mysql_error();
                $c++;
            endforeach;
            $res['status']="ok";
        break;
    endswitch;
    
    
    
    
    
    header('Content-Type:application/json');
    echo json_encode($res);
}


function addUser($request) {
    global $dbop;
    $res=array();
    $appID=$request['appID'];
    $userID=$request['ex'];
    

    /////////////
    $url="http://api.wheeldo.com/APIAD.php";
    
    $postArray=array();
    $postArray['request']='userByID';
    $postArray['function_data[userID]']=$userID;
    $postArray = array_merge($postArray);
    $response=doRequest($url,$postArray);
    $user=json_decode($response,true);
    set_time_limit(2);
    //print_r($user);
    $fields=array();
    $fields['appID']=$appID;
    $fields['user_id']=$user['userID'];
    $fields['user_name']=$user['userName'];
    $fields['userDepartment']=$user['userDepartment'];
    $fields['userPosition']=$user['userPosition'];
    $fields['userLevel']=$user['userLevel'];
    $fields['photo']=$user['userPhotoID'];
    $fields['empID']=$user['userEmpID'];
    $fields['hashedEmail']=$user['hashedEmail'];

    $check=$dbop->selectAssocRow('users',"WHERE `user_id`='{$user['userID']}' AND `appID`='$appID'");
    if($check){
        $dbop->updateDB('users',$fields,$check['id']);
    }
    else {
        $dbop->insertDB('users',$fields);
    }
    
    
    $token=getToken($appID,$userID);
    
    
    $res['link']="land/$appID/$token";
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}


function removeUser($request) {
    global $dbop;
    $res=array();
    $appID=$request['appID'];
    $userID=$request['ex'];
    
    mysql_query("DELETE FROM `users` WHERE `user_id`='{$userID}' AND `appID`='{$appID}'");
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}
