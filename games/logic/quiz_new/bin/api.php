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

    
    header('Content-Type:application/json');
    echo json_encode($res);
}


function duplicate($request) {
    global $dbop;
    $oldID=$request['appID'];
    $newID=$request['ex'];
    
    // games
    $check1=$dbop->selectAssocRow('game',"WHERE `appID`='{$newID}'");
    if(!$check1)
        $dbop->insertDB("game",array("appID"=>$newID));

    
    
    

    // copy boxes:
    $ans=$dbop->selectDB("boxes","WHERE `appID`='{$oldID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      
      $row['appID']=$newID;
      unset($row['id']);
      $dbop->insertDB("boxes",$row);
    }
    
    
    // copy level messages:
    $ans=$dbop->selectDB("levels_message","WHERE `appID`='{$oldID}'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      
      $row['appID']=$newID;
      unset($row['id']);
      $dbop->insertDB("levels_message",$row);
    }
    
    
    
    
    echo "duplicate set!";
}


// GuessWot/api/onStart/[appID]
function onStart($request) {
    global $dbop;
    $appID=$request['appID'];
    

    
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

        $check=$dbop->selectAssocRow('user',"WHERE `user_id`='{$user['ID']}' AND `appID`='$appID'");
        
        //var_dump($check);
        if($check){
            $dbop->updateDB('user',$fields,$check['id']);
        }
        else {
            $dbop->insertDB('user',$fields);
        }
        echo mysql_error();
        
    endforeach;
    
    
    $postArray=array();
    $postArray['request']='getUserOrgLogo';
    $postArray['function_data[userID]']=$user['ID'];
    $postArray = array_merge($postArray,$session->getSessionData());
    $orgLogo=doRequest($url,$postArray);
    
    //////////
    //orgLogo
    //$dbop->updateDB('game_info',array("start"=>time(),"game_set"=>1,"orgLogo"=>$orgLogo),$appID,"appID");
    
    
    echo "onStart set!";
}


function editWS($request) {
    global $dbop;
    $res=array();

    $edit_op=$request['edit_op'];
    $copyID=$request['copyID'];
    
    switch($edit_op):
        case "get":
            
            
        break;
        case "set":
            
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

    $check=$dbop->selectAssocRow('user',"WHERE `user_id`='{$user['userID']}' AND `appID`='$appID'");
    if($check){
        $dbop->updateDB('user',$fields,$check['id']);
    }
    else {
        $dbop->insertDB('user',$fields);
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
    
    mysql_query("DELETE FROM `user` WHERE `user_id`='{$userID}' AND `appID`='{$appID}'");
    
    $res['status']="ok";
    header('Content-Type:application/json');
    echo json_encode($res);
}
