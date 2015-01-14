<?php
require_once "top.php";
$request=$_SERVER['REQUEST_URI'];
$ex1=explode("?",$request);
$requestEX=explode("/",$ex1[0]);

$new_req_uri=array();
foreach($requestEX as $r):
    if($r!="")
    $new_req_uri[]=$r;
endforeach;



$p_data=array(
    "url"=>"",
    "title"=>"",
    "image"=>"",
    "description"=>""
);
switch($new_req_uri[0]):
    case "play":
        $gid=$new_req_uri[1];
        $game = $dbop->selectAssocRow("games","WHERE `unique_id`='{$gid}'");
        $p_data['url']='http://'.$_SERVER['HTTP_HOST'].'/play/'.$gid;
        $p_data['title']=$game['name'];
        $p_data['image']=$game['thumbnail'];
        $p_data['description']=$game['full_desc'];
    break;

    case "channel":
        $cid=$new_req_uri[1];
        $channel = $dbop->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");
        $p_data['url']='http://'.$_SERVER['HTTP_HOST'].'/channel/'.$cid;
        $p_data['title']=$channel['name'];
        $p_data['image']=$channel['small_icon'];
        $p_data['description']=$channel['description'];
    break;

    default:
        $p_data['url']='http://'.$_SERVER['HTTP_HOST'].'/';
        $p_data['title']="Wheeldo";
        $p_data['image']="http://www.wheeldo.co/media/img/wheeldo_s_logo.jpg";
        $p_data['description']="Share your content with games | Create your game in minutes";
    break;
    
endswitch;


if(strpos($p_data['image'],"http://") === false) {
    $p_data['image']="http://www.wheeldo.co/".$p_data['image'];
}



//echo "<pre>";
//print_r($p_data);
?>
<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
<head>
<title><?=$p_data['title']?></title>
<meta name="description" content="<?=$p_data['description']?>">
<meta property="og:type" content="website" />
<meta property="og:url" content="<?=$p_data['url']?>" />
<meta property="og:title" content="<?=$p_data['title']?>" />
<meta property="og:image" content="<?=$p_data['image']?>" />
<meta property="og:description" content="<?=$p_data['description']?>" />
</head>
<body>
    <h1><?=$p_data['title']?></h1>
</body>
</html>