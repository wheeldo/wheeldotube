<?php
//var_dump($game_data);
$_SESSION['game_data']=$game_data;
$_SESSION['user_data']=$user_data;
$_SESSION['template']=$template;
$_SESSION['settings']=$game_data->settings;
$_SESSION['design']=$game_data->game_design;
$_SESSION['game_tube']=$game_tube;


$_SESSION['quiz']=$game_data->quiz;
$_SESSION['game_progress']=$game_data->game_progress;
$_SESSION['open_status']=$game_tube['open_status'];


//var_dumpi($user_data);
function get_timestamp($local){
    //return ($local)?'':time();
    return time();
}

if(isset($_SESSION['invited_by'])) {
    $key=$gid."_".$_SESSION['invited_by']."_".$_SESSION['login_user']['uid'];
    $check=$dbop->selectAssocRow("game_invitations","WHERE `key`='{$key}' AND `payed_up`=0");
    @mysql_select_db( "wheeldotube_main" , $dbop->getConn() ) or die( "error - unable to select database" );
    $invite_by_user=$dbop->selectAssocRow("users","WHERE `unique_link`='{$_SESSION['invited_by']}'");
    @mysql_select_db( "wheeldotube_gamedata" , $dbop->getConn() ) or die( "error - unable to select database" );
    $game_id=$game_tube['id'];
    $user_id=$invite_by_user['id'];


    $check_if_score=$dbop->selectAssocRow("game_quiz_user","WHERE `game_id`='{$game_id}' AND `user_id`='{$user_id}'");
    if($check_if_score && $check) {
        // add score:
        $new_score=(int)$check_if_score['score']+100;
        $dbop->updateDB("game_quiz_user",array("score"=>$new_score),$check_if_score['id']);
        $dbop->updateDB("game_invitations",array("payed_up"=>1,"payed_up_time"=>time()),$check['id']);
        unset($_SESSION['invited_by']);
    }

    //var_dumpi($check_if_score);
}



function var_dumpi($var) {
    echo '<pre style="border:1px solid gray;padding:10px;margin:10px;background-color:#D6D6D6;direction:ltr;">';
    print_r($var);
    echo "</pre>";
}

//var_dumpi($_SESSION['settings']);
//var_dumpi($_SESSION['open_status']);
$user_image=$_SESSION['user_data']['image']!=""?$_SESSION['user_data']['image']:"http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_54,w_54/v1385030174/User_default_qgwkye.jpg";

$lang=$_SESSION['settings']['lang'];
?>
<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" ng-app="TubeApp" id="ng-app">
    <head id="head">
        <title ng-bind="page.title">hello</title>
        <meta name="description" content="{{page.description}}">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta property="fb:app_id" content="1511607775728782"/>
        <!--   tmp  -->
        <link rel="stylesheet" href="/vendor/bootstrap-2.3.1/css/bootstrap-select.min.css" media="screen" />
        <link rel="stylesheet" href="/vendor/bootstrap-2.3.1/css/bootstrap.css" media="screen" />
        <link rel="stylesheet" href="/vendor/bootstrap-2.3.1/css/bootstrap-responsive.min.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/games/logic/<?=$logic_dir?>/media/css/style.css?t=<?=get_timestamp($local)?>">
        <?php if($lang==="he") {?>
            <link rel="stylesheet" type="text/css" href="/games/logic/<?=$logic_dir?>/media/css/rtl.css">
        <?php }?>
        <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <!--[if lte IE 8]>
            <script src="/media/js/JSON-js-master/json2.js"></script>
        <![endif]-->
        <script>
        var USER=<?=json_encode($userVisible)?>;
        var user_image='<?=$user_image?>';
        var game_quiz_user=<?=json_encode($game_data->game_quiz_user)?>;
        var banner='<?=$_SESSION['design']['banner']?>';
        var open_status=<?=$game_tube['open_status']?>;
        var ug=<?=$user_data['ghost']?>;
        var lang="<?=$lang?>";
        var gid="<?=$gid?>";
        var voucher=<?=$game_tube['voucher']?>;
        var voucher_name='<?=$game_tube['voucher_name']?>';
        var logic_dir='<?=$logic_dir?>';
        var __isMobile=<?=$__isMobile?"true":"false"?>;
        </script>
        <style>
        	<?php if($_SESSION['design']['banner']): ?>
            .banner_wrapper {
                background-image:url('<?=$_SESSION['design']['banner']?>');
            }
            <?php endif; ?>

            .big_blue_button,
            .link_end{
                background: <?=$_SESSION['design']['color_2']?>;
                background: -moz-linear-gradient(top,  <?=$_SESSION['design']['color_2']?> 0%, <?=$_SESSION['design']['color_3']?> 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?=$_SESSION['design']['color_2']?>), color-stop(100%,<?=$_SESSION['design']['color_3']?>));
                background: -webkit-linear-gradient(top,  <?=$_SESSION['design']['color_2']?> 0%,<?=$_SESSION['design']['color_3']?> 100%);
                background: -o-linear-gradient(top,  <?=$_SESSION['design']['color_2']?> 0%,<?=$_SESSION['design']['color_3']?> 100%);
                background: -ms-linear-gradient(top,  <?=$_SESSION['design']['color_2']?> 0%,<?=$_SESSION['design']['color_3']?> 100%);
                background: linear-gradient(to bottom,  <?=$_SESSION['design']['color_2']?> 0%,<?=$_SESSION['design']['color_3']?> 100%);
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?=$_SESSION['design']['color_2']?>', endColorstr='<?=$_SESSION['design']['color_3']?>',GradientType=0 );
                color:<?=$_SESSION['design']['color_1']?>;
            }

            .big_blue_button:hover,
            .link_end:hover{
                background: <?=$_SESSION['design']['color_3']?>;
                background: -moz-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%, <?=$_SESSION['design']['color_2']?> 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?=$_SESSION['design']['color_3']?>), color-stop(100%,<?=$_SESSION['design']['color_2']?>));
                background: -webkit-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_2']?> 100%);
                background: -o-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_2']?> 100%);
                background: -ms-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_2']?> 100%);
                background: linear-gradient(to bottom,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_2']?> 100%);
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?=$_SESSION['design']['color_3']?>', endColorstr='<?=$_SESSION['design']['color_2']?>',GradientType=0 );
            }
            
            
        </style>
        <div id="fb-root"></div>
    </head>
    <body class="<?if($hu){?>hu<?}?>">
        <div id="blur"></div>
        <div class="wrapper" ng-view=""></div>
        <script src='/vendor/angularjs-1.0.7/angular.min.js'></script>
        <script src='/vendor/angularjs-1.0.7/angular-resource.min.js'></script>
        <script src="/vendor/bootstrap-2.3.1/js/bootstrap-select.min.js"></script>
        <script src="/vendor/ui.bootstrap/ui-bootstrap-tpls-0.6.0.min.js"></script>
        <script src="/media/js/angular-sanitize.min.js"></script>
        <script src="/vendor/angular-touch/angular-touch.min.js"></script>
        <!-- App libs -->
        <script src="/vendor/google-plus-signin/google-plus-signin.js"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/services/fb.js?t=<?=time()?>"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/app.js?t=<?=time()?>"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/directives/directives.js?t=<?=time()?>"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/includes/js/dictionary.js?t=<?=time()?>"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/includes/js/general.js?t=<?=time()?>"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/controllers/controllers.js?t=<?=get_timestamp($local)?>"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/services/Services.js?t=<?=time()?>"></script>
        <script src="/games/logic/<?=$logic_dir?>/app/factories/wheeldo_app.js"></script>
    </body>
</html>