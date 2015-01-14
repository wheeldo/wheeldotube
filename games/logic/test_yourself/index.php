<?php

function var_dumpi($var) {
    echo '<pre style="border:1px solid gray;padding:10px;margin:10px;background-color:#D6D6D6;direction:ltr;">';
    print_r($var);
    echo "</pre>";
}

function get_timestamp($local){
    //return ($local)?'':time();
    return time();
}


$data=$game_data->game_data;


$c=0;
foreach($data['results'] as $result):
    if($result['headline']=="") {
        $data['results'][$c]['headline']=$result['text'];
        $data['results'][$c]['text']="";
    }
    $c++;
endforeach;
//var_dumpi($_SESSION);
$_SESSION['settings']=$game_data->settings;
$_SESSION['design']=$game_data->game_design;
$_SESSION['game_tube']=$game_tube;
$_SESSION['user_data']=$user_data;

$lang=$_SESSION['settings']['lang'];

//var_dumpi($_SESSION['design']);



$user_image=$_SESSION['user_data']['image']!=""?$_SESSION['user_data']['image']:"http://res.cloudinary.com/wheeldo/image/upload/c_fill,h_54,w_54/v1385030174/User_default_qgwkye.jpg";


$powered_show=true;
//if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
//
//    // grabbing referer:
//    $my_sites = array(
//        "http://www.wheeldo.com",
//        "http://www.wheeldo.co",
//        "http://wheeldo.co",
//        //"http://www.wheeldo.info",
//        "http://localhost",
//    );
//
//    foreach($my_sites as $site):
//        $ref_url=substr($_SERVER['HTTP_REFERER'], 0, strlen($site));
//        if($ref_url === $site):
//            $powered_show=false;
//        endif;
//    endforeach;
//    
//}
//else {
//    $powered_show=false;
//}
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
        <script src="/vendor/jquery/jquery-1.10.2.js"></script>
        <script src="/vendor/jquery/ui/jquery-ui-1.10.3.js"></script>
        <!--[if lte IE 8]>
            <script src="/media/js/JSON-js-master/json2.js"></script>
        <![endif]-->
        <script>     
        var USER=<?=json_encode($userVisible)?>;
        var user_image='<?=$user_image?>';
        var banner='<?=$_SESSION['design']['banner']?>';
        var open_status=<?=$game_tube['open_status']?>;
        var results_signup=<?=$game_tube['results_signup']?>;
        var signup_headline='<?=$game_tube['signup_headline']?>';
        var ug=<?=$user_data['ghost']?>;
        var lang="<?=$lang?>";
        var gid="<?=$gid?>";
        var share_button=<?=$game_tube['share_button']?>;
        var CTA_button=<?=$game_tube['CTA_button']?>;
        var call_action_link='<?=$game_tube['call_action_link']?>';
        var call_action_text='<?=$game_tube['call_action_text']?>';
        var voucher=<?=$game_tube['voucher']?>;
        var voucher_name='<?=$game_tube['voucher_name']?>';
        var logic_dir='<?=$logic_dir?>';
        var __isMobile=<?=$__isMobile?"true":"false"?>;
        var data=<?=json_encode($data)?>;
        var start_time='<?=time()?>';
        
        var referrer=<?=$referrer?'"'.$referrer.'"':"false"?>;
        </script>
        <style>
            
            
            <?php if(!$powered_show) {?>
                .powered_by {
                    display:none;
                }
            <? }?>
            
            <?php if($_SESSION['design']['banner']): ?>
            .banner_wrapper {
                background-image:url('<?=$_SESSION['design']['banner']?>');
            }
            <?php endif; ?>
            
                        
            <?php if($_SESSION['design']['background']): ?>
            body {
                background-image:url('<?=$_SESSION['design']['background']?>');
            }
            <?php endif; ?>
            
            <?php if($_SESSION['design']['color_4']): ?>
            body {
                color:<?=$_SESSION['design']['color_4']?>;
                border-color:<?=$_SESSION['design']['color_4']?>;
            }
            
            .accordion-group,
            .accordion-inner,
            .answer{
                border-color:<?=$_SESSION['design']['color_4']?>;
            }
            <?php endif; ?>
            
            
        </style>
        <? if($__isMobile){ ?>
            <link rel="stylesheet" type="text/css" href="/games/logic/<?=$logic_dir?>/media/css/style_mobile.css?t=<?=get_timestamp($local)?>">
        <? } ?>
        <div id="fb-root"></div>
    </head>
    <body class="<?if($hu){?>hu<?}?>">
        
        <div id="blur"></div>
        <div class="wrapper" ng-view=""></div>
        <script src='/vendor/angularjs-1.0.7/angular.min.js'></script>
        <script src='/vendor/angularjs-1.0.7/angular-resource.min.js'></script>
        <script src='/vendor/angularjs-1.0.7/angular-cookies.min.js'></script>
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
