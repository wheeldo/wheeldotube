<?php 
require_once "sys/include.php";
include "../bin/top.php";
//$games_list=array("55c47de412","0cc3b1d6c0","02ef958a91","aed51d90b9","3a4f329bb9","e7e0f23d5a","4870892cfc","de26e64aed","79762a33ac","d7f9d15ee0","bc1a30401f","e07d8cc651","32e8c8390e","cb8a2d9076","2ebe0a49f7","9e0324fcf4","bbd307e7a2");
//
//$game_str="";
//$k=0;
//foreach($games_list as $game_link):
//    if($k!=0)
//        $game_str.=",";
//    $game_str.="'".$game_link."'";
//    $k++;
//endforeach;

$examples_row=$dbop->selectAssocRow("games_rows","WHERE `row`='Examples'");

$games_ids=json_decode($examples_row['game_ids'],true);
$k=0;
$games=array();
foreach($games_ids as $game_id):
    if($k>=(int)$examples_row['no'])
        break;
    $sql="SELECT
    games.id,
    games.unique_id,
    games.time,
    games.plays,
    games.`name`,
    games.thumbnail,
    games.prize,
    games.prize_text,
    games.prize_time_limit,
    games.winner,
    games.full_desc,
    users.fname,
    users.lname,
    users.image,
    users.unique_link,
    channels.unique_id AS cid,
    channels.`name` AS cname,
    channels.small_icon,
    users.fname AS user_fname,
    users.lname AS user_lname
    FROM
    channels
    INNER JOIN game_channel ON game_channel.channel_id = channels.id
    INNER JOIN games ON game_channel.game_id = games.id
    INNER JOIN users ON games.`owner` = users.id
    WHERE games.id IN ({$game_id})";
    $ans=$dbop->selectQDB($sql);
    $game_data=mysql_fetch_assoc($ans['p']);
    $games[$c]['name']=$game_data['name'];
    $games[$c]['thumbnail']=$game_data['thumbnail'];
    $games[$c]['full_desc']=(strlen($game_data['full_desc']) > 353) ? substr($game_data['full_desc'],0,350).'...' : $game_data['full_desc'];
    $games[$c]['link']="http://www.wheeldo.co/play/".$game_data['unique_id'];  
    $games[$c]['channel_name']=$game_data['cname'];
    $c++;

    $k++;
endforeach;


//
//$sql="SELECT
//games.id,
//games.unique_id,
//games.time,
//games.plays,
//games.`name`,
//games.thumbnail,
//games.prize,
//games.prize_text,
//games.prize_time_limit,
//games.winner,
//games.full_desc,
//users.fname,
//users.lname,
//users.image,
//users.unique_link,
//channels.unique_id AS cid,
//channels.`name` AS cname,
//channels.small_icon,
//users.fname AS user_fname,
//users.lname AS user_lname
//FROM
//channels
//INNER JOIN game_channel ON game_channel.channel_id = channels.id
//INNER JOIN games ON game_channel.game_id = games.id
//INNER JOIN users ON games.`owner` = users.id
//WHERE games.id IN ({$game_str})";
//
//$c=0;
//
//$ans=$dbop->selectQDB($sql);
//$games=array();
//for($i=0;$i<$ans['n'];$i++) {
//    $game_data=mysql_fetch_assoc($ans['p']);
//    $games[$c]['name']=$game_data['name'];
//    $games[$c]['thumbnail']=$game_data['thumbnail'];
//    $games[$c]['full_desc']=(strlen($game_data['full_desc']) > 353) ? substr($game_data['full_desc'],0,350).'...' : $game_data['full_desc'];
//    $games[$c]['link']="http://www.wheeldo.co/play/".$game_data['unique_id'];  
//    $games[$c]['channel_name']=$game_data['cname'];
//    $c++;
//}


//echo "<pre>";
//var_dump($games);

?>
<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssreset.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssfonts.css">
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/wheeldoMain.css">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="/website/media/js/general.js"></script>                
	</head>
	<body>
		<div id="mainContainer">
			<div id="headerRubber">
				<div id="headerWrapper">
					<?php
					include 'mainmenu.php';
					?>
				</div>
			</div>
			<div id="block01RubberNH">
				<div id="block01Wrapper" class="block01_pricing">
					<h1>GAME EXAMPLES</h1>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_pricing">
                                        
                                    <?php
                                    foreach($games as $game):?>
                                    <div class="game_block">
                                        <div class="thumbnail_wrap"><a href="<?=$game['link']?>" target="_blank"><img src="<?=$game['thumbnail'];?>" /></a></div>
                                        <div class="main_game_wrap">
                                            <h2><a href="<?=$game['link']?>"><?=$game['name'];?></a></h2>
                                            <p><?=$game['full_desc'];?></p>
                                            <p>By <span class="marked"><?=$game['channel_name']?></span></p>
                                            <a href="<?=$game['link']?>" target="_blank" class="play_now">Play now!</a>
                                        </div>
                                        
                                        <div class="info_wrap">
                                            
                                        </div>
                                    </div>
                                    <?endforeach;?>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block03Wrapper" class="block03_pricing">
				</div>
			</div>
			<div id="footerRubber">
				<div id="footerWrapper">
					<?php
					include 'mainfooter.php';
					?>
				</div>
			</div>
		</div>
		<?php
		include 'signup.php';
		?>
		<?php
                    include "sys/bottom_scripts.php";
                ?>
	</body>
</html>
