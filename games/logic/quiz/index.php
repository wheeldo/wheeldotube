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
        <meta property="fb:app_id" content="219145361621410"/>
        <link rel="stylesheet" href="/vendor/bootstrap-2.3.1/css/bootstrap-select.min.css" media="screen" />
        <link rel="stylesheet" href="/vendor/bootstrap-2.3.1/css/bootstrap.css" media="screen" />
        <link rel="stylesheet" href="/vendor/bootstrap-2.3.1/css/bootstrap-responsive.min.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/games/logic/quiz/media/css/style.css">
        <?php if($lang==="he") {?>
            <link rel="stylesheet" type="text/css" href="/games/logic/quiz/media/css/rtl.css">
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
        </script>
        <style>
            
            .wrapper,
            .reg_wrapper{
                background-image:url('<?=$_SESSION['design']['background']?>');
            }
            
            body,
             #score-hello {
                color:<?=$_SESSION['design']['color_1']?>;
            }

             .leader {
                border-color:<?=$_SESSION['design']['color_1']?>;
            }

             .timer,
             .strikes {
                background-color:<?=$_SESSION['design']['color_1']?>;
            }


             .timer,
             .strikes {
                color:<?=$_SESSION['design']['color_2']?>;
            }



             .option,
             .min_button{
                background: <?=$_SESSION['design']['color_3']?>; /* Old browsers */
                /* IE9 SVG, needs conditional override of 'filter' to 'none' */
                background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2U2ZWZmNSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNhMGMzZGEiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
                background: -moz-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%, <?=$_SESSION['design']['color_4']?> 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?=$_SESSION['design']['color_3']?>), color-stop(100%,<?=$_SESSION['design']['color_4']?>)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_4']?> 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_4']?> 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(top,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_4']?> 100%); /* IE10+ */
                background: linear-gradient(to bottom,  <?=$_SESSION['design']['color_3']?> 0%,<?=$_SESSION['design']['color_4']?> 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?=$_SESSION['design']['color_3']?>', endColorstr='<?=$_SESSION['design']['color_4']?>',GradientType=0 ); /* IE6-8 */
            }

             .game_options .option,
             .min_button{
                border-color:<?=$_SESSION['design']['color_4']?>;
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
        <script src="/games/logic/quiz/app/app.js?t=<?=time()?>"></script>
        <script src="/games/logic/quiz/app/directives/directives.js?t=<?=time()?>"></script>
        <script src="/games/logic/quiz/app/includes/js/dictionary.js?t=<?=time()?>"></script>
        <script src="/games/logic/quiz/app/includes/js/general.js?t=<?=time()?>"></script>
        <script src="/games/logic/quiz/app/controllers/controllers.js?t=<?=time()?>"></script>
        <script src="/games/logic/quiz/app/services/Services.js?t=<?=time()?>"></script>
        <!--<script src="/app/services/fb.js?t=<?=time()?>"></script>-->
        <script src="/games/logic/quiz/app/factories/wheeldo_app.js"></script>
    </body>
</html>