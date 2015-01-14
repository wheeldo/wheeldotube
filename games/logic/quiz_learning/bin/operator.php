<?php
require_once 'top_functions.php';


if(!isset($_SESSION['login']))
    die();


$appID=$_SESSION['login']['appConfig'];
$userID=$_SESSION['user']['ID'];



$check_post=file_get_contents("php://input");
if(!$check_post)
    die();


$data = json_decode(file_get_contents("php://input"));
$op=$data->op;

$op($data);

function wallActions($data) {
    global $appID;
    global $userID;
    $wall = new wall($appID);
    $action=isset($data->action)?$data->action:'getWall';
    switch($action):
        case "addPost":
            $post_text=strip_tags($data->post_text);
            $post_to=$data->post_to;
            $wall->addPost($post_to,0,$userID,0,$post_text,true);
        break;
        case "like":
            $post_to=$data->post_to;
            $wall->likePost($post_to,$userID);
        break;
        case "getWall":
        default:
            
        break;
    endswitch;  
    
    header('Content-Type:application/json');
    $wall_json=$wall->getWall();
    echo json_encode($wall_json);
}