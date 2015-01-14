<?php
require_once 'top_functions.php';


$REQ=array();
foreach($_GET as $key=>$value):
    $REQ[mysql_real_escape_string($key)]=mysql_real_escape_string($value);
endforeach;
unset($_GET);

if(isset($REQ['action']) && $REQ['action']=="land") {
    unset($_SESSION['login']);
    unset($_SESSION['user']);
    unset($_SESSION['user_as_json']);
    // landing login:
    $session = WheelDoSession::createSession($REQ['token'], $REQ['appID']);
    $url="http://api.wheeldo.com/API.php";
    $postArray=array();
    $postArray['login']='y';
    $postArray['key']='x';


    // check for access:
    $postArray=array();
    $postArray['request']='canAccess';
    $postArray['accessType']=2; // 1 for edit, 2 for view only
    $postArray = array_merge($postArray,$session->getSessionData());

    $response=doRequest($url,$postArray);
    
    $response_as_array=json_decode($response,true);

    if($response_as_array['canAccess']==1) {
        // move to the game:
        $_SESSION['login']=$session->getSessionData();
        // get user data:
        $postArray=array();
        $postArray['request']='getUser';
        $postArray = array_merge($postArray,$session->getSessionData());
        $response=doRequest($url,$postArray);
        $response_as_array=json_decode($response,true);
        $_SESSION['user']['name']=ucfirst($response_as_array['name']);
        $_SESSION['user']['photo']=$response_as_array['photo'];
        $_SESSION['user']['ID']=$response_as_array['ID'];
        $_SESSION['user']['teamID']=$response_as_array['teamID'];
        $_SESSION['user_as_json']=json_encode($_SESSION['user']);
        
        
        
        
        


        $result=routeUser($_SESSION['user']);
        header("location:".BASEURL."/#/".$result['route']);
    }
    else {
        die("No premissions!");
    }
}

if(isset($_SESSION['user'])) {
    routeUser($_SESSION['user']);
}


function routeUser($user) {
    // default route:
    $result=array();
    $result['route']="wall";
    /////////////////

    global $REQ;
    global $dbop;
    
    $appID=isset($REQ['appID'])?$REQ['appID']:$_SESSION['login']['appConfig'];
    $userID=$_SESSION['user']['ID'];
            
            
    
    $user_quiz=array();
    
    $ans=$dbop->selectDB("q_types_q_user_data", "WHERE `appID`='{$appID}' AND `userID`='{$userID}' AND `p`='0'");
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      $user_quiz[]=$row['q_id'];
    }

    
    $result['user_quiz']=$user_quiz;
    
    $ans=$dbop->selectDB("q_types_q", "WHERE `appID`='{$appID}'");
    $total_question=$ans['n'];
    for($i=0;$i<$ans['n'];$i++) {
      $row=mysql_fetch_assoc($ans['p']);
      $q_id=$row['q_id'];
      if(!in_array($q_id,$user_quiz)) {
          // bingo
          $result['route']="stage".$row['type']."/".$q_id;
          $result['stage']=($i+1);
          break;
      }
    }
    
    
    if(count($user_quiz)==0) {
        if(isset($_SESSION['instructions']) && $_SESSION['instructions']==$userID) {
            
        }
        else {
            $result['route']="instructions";
            $_SESSION['instructions']=$userID;
        }
    }
    
    
    if(count($user_quiz)===$total_question) {
        if(isset($_SESSION['total_question']) && $_SESSION['total_question']==$userID) {
            
        }
        else {
            $result['route']="resulTotal";
            $_SESSION['total_question']=$userID;
        }
    }
    
    echo json_encode($result);
    return $result;
}