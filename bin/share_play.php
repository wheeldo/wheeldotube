<?php
$_gid=$_GET['gid'];

$extra_url="";
if(isset($_GET['r'])) {
    $extra_url="?r=".$_GET['r'];
}

require_once "top.php";
$game = $dbop->selectAssocRow("games","WHERE `unique_id`='{$_gid}'");
/*
 * Array
(
    [id] => 146
    [owner] => 691
    [unique_id] => 46c2aa3d8b
    [time] => 1400092847
    [private] => 0
    [open_status] => 1
    [game_type] => 1
    [game_template] => 1
    [plays] => 3
    [name] => ×¡×™×¤×•×¨×™ ×™×œ×“×™×
    [thumbnail] => http://lh5.ggpht.com/2lAL0fpgRTGPTLYKs0P0_A8Bhn5yOz2CjfGcBaGuycw_nMmctWTcABclBaG04zRG3g_YqmmO08aDESGuA9pa_pU0CUPOHVyR
    [full_desc] => ×‘×ž×©×—×§ ×–×” ×ª×œ×ž×“×• ×•×ª×—×–×¨×• ×œ×™×œ×“×•×ª. ×›×œ ×©×¦×¨×™×š ×”×•× ×œ×”×™×–×›×¨ ×‘×¡×™×¤×•×¨×™× ×©×§×¨××ª× ×œ×¤× ×™ ×¢×©×¨×™× ×©× ×” ××• ×œ×”×™×–×›×¨ ×‘×ž×” ×©×§×¨××ª× ××ž×© ×œ×™×œ×“×™× ×©×œ×›×.
×ª×”× ×•!
    [prize] => 1
    [prize_text] =>
    [prize_time_limit] => 24
    [winner] => Daria Eichler beeri _
    [voucher] => 0
    [voucher_name] =>
    [voucher_email_subject] =>
    [voucher_email_content] =>
)
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title></title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta property="og:type" content="website" />
		<meta property="og:url" content="<?='http://'.$_SERVER['HTTP_HOST'].'/play?gid='.$_gid?>" />
		<meta property="og:title" content="<?=$game['name']?>" />
		<meta property="og:image" content="<?=$game['thumbnail']?>" />
		<meta property="og:description" content="<?=htmlspecialchars($game['full_desc'])?>" />
	</head>
	<body>
		<script>
		(function(){
			location = 'http://<?=$_SERVER['HTTP_HOST']?>/play/<?=$_gid.$extra_url?>';
		})();
		</script>
	</body>
</html>
