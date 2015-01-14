<?php
require "bin/top.php";
function check_user_agent_not_real_user($server) {
    //return true;
    if(!isset($server['HTTP_USER_AGENT']))
        return true;
    $HTTP_USER_AGENT=$server['HTTP_USER_AGENT'];
    if(strpos($HTTP_USER_AGENT,"facebook") !== false) return true;
    if(strpos($HTTP_USER_AGENT,"linkedin") !== false) return true;
    if(strpos($HTTP_USER_AGENT,"google") !== false) return true;
    return false;
}

//var_dump(check_user_agent_not_real_user("facebookexternalhit/1.1 (+https://www.facebook.com/externalhit_uatext.php)"));

if(check_user_agent_not_real_user($_SERVER)) {
    require 'bin/bot_fix.php';
    die();
}

$skin="dice";
if(isset($_GET['skin'])) {
    $skin=$_GET['skin'];
}

$u=0;
if(isset($_GET['u'])) {
    $u=$_GET['u'];
    include "bin/instant_login.php";
    die();
}

$bodyClass="";
if(isset($_COOKIE["sm_open"])&&$_COOKIE["sm_open"]=="1") {
    $bodyClass="sm_open";
}

$DS= DIRECTORY_SEPARATOR;
$detect = new Mobile_Detect;

$__isMobile=$detect->isMobile() && !$detect->isTablet();
//$__isMobile=true;

//var_dump($_SESSION['login_user']);

if($__isMobile || !isset($_SESSION['login_user'])) {
    $bodyClass="";
}



$server0=explode(".",$_SERVER['REMOTE_ADDR']);
$serverStart=$server0[0];
if($serverStart=="10" || $serverStart=="127" || $serverStart=="::1") $local=1;
else $local=0;

function get_timestamp($local){
    return ($local)?'':time();
}


$x_uri=explode("/",$_SERVER['REQUEST_URI']);
$uri=$x_uri[1];
$show_chat=true;
// disable chat:
if($uri=="share_game" || $uri=="play") {
    $show_chat=false;
}

if($__isMobile) {
    $show_chat=false;
}

///////////////
//



// detect admin
$user=$dbop->selectAssocRow("users","WHERE `id`='{$_SESSION['login_user']['ID']}'");
$user_admin=false;
if($user['admin'] || isset($_SESSION['user_admin'])) {
        $user_admin=true;
        if(!isset($_SESSION['user_admin']))
            $_SESSION['user_admin']=$user['id'];
}
////////////////
?>
<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" ng-app="TubeApp" id="ng-app">
    <head id="head">
        <link rel="icon" type="image/png" href="/media/img/favicon.gif">
        <title ng-bind="page.title">Wheeldo</title>
        <meta name="description" content="">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <meta property="fb:app_id" content="219145361621410"/>
        
        <meta property="og:type" content="website" />
        <meta property="og:url" content="http://www.wheeldo.co" />
        <meta property="og:title" content="Wheeldo | Play. Engage. Share." />
        <meta property="og:image" content="http://www.wheeldo.co/media/img/wheeldo_share.jpg" />
        <meta property="og:description" content="Engage And Convert Your Online Traffic With Games! Create a game for your website, mailing list and social channels in minutes!" />

        <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-2.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="/vendor/bootstrap-2.3.1/css/bootstrap-responsive.min.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/media/css/<?=$skin?>/cssreset-context-min.css">
        <link rel="stylesheet" type="text/css" href="/media/css/<?=$skin?>/style.css?t=<?=get_timestamp($local)?>">
        <link rel="stylesheet" type="text/css" href="/media/css/<?=$skin?>/font-awesome.css">
        <script src="/vendor/jquery/jquery-1.10.2.js"></script>
        <script src="/vendor/jquery/ui/jquery-ui-1.10.3.js"></script>
        
        


<!--        <script src="/vendor/twitter/widgets.js"></script>-->
        <!--[if lte IE 8]>
            <script src="/media/js/JSON-js-master/json2.js"></script>
        <![endif]-->
        <!--<div id="fb-root"></div>
        <script>
        window.fbAsyncInit = function() {
            FB.init({
              appId      : '1511607775728782',
              status     : true,
              xfbml      : true
            });
         };
        (function() {
            var e = document.createElement('script'); e.async = true;
            e.src = document.location.protocol +
            '//connect.facebook.net/en_US/all.js';
            document.getElementById('fb-root').appendChild(e);
        }());-->
		<script>
        var skin="<?=$skin?>";
        var __isMobile=<?=$__isMobile?"true":"false"?>;
        var u='<?=$u?>';
        var no_frame=false;
        </script>
        <script src="/vendor/twitter/widgets.js"></script>

    </head>
    <body class="<?=$bodyClass?>">
        <div id="blur"></div>
        <?php if($user_admin){ ?>
        <div ng-controller="aCtrl">
            <div id="admin_panel" ng-show="show_admin_bar">
                YIPI! Hey <?=ucfirst($user['fname'])?>.
                |
                User view:
                <form ng-submit="search_user_view()">
                    <input ng-minlength="1" style="width:110px;" ng-model="user_view_search" type="txet" placeholder="ID|UID|Name|Email" />
                    <button type="submit">@</button>
                </form>
                <span ng-show="users">
                    <select ng-model="user" ng-options="k as v for (k, v) in users"></select>
                    <button ng-click="setUserView()">Set view</button>
                </span>
                <button ng-click="resetUserView()">reset</button>
                <div class="panel_close" ng-click="hiedBar()"></div>
            </div>
            
            <div ng-hide="show_admin_bar" class="panel_open" ng-click="showBar()"></div>
        </div>
        <?php } ?>
        <div class="wrapper" ng-view=""></div>

        <!--[if lt IE 9]>
        <div style="text-align:justify; width:600px; margin:0 auto; margin-top:20px; font-size:18px;">
        	<div style="background-color:#25abe0; padding:1em; margin-bottom:1em;">
        		<img src="/media/css/dice/img/wheeldo_logo.png" />
        	</div>
        	<p style="font-weight:bold;">Browser Upgrade Required</p>
			<br/>
        	<p>Unfortunately, the browser that you are using does not support the technologies required for the Wheeldo experience.</p>
			<br/>
        	<p>Please consider
        		<a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" target="_blank" style="color:#25abe0;">upgrading your version of Internet Explorer</a>
        		<br/>or downloading a more modern browser such as<br/>
        		<a href="https://www.google.com/chrome/browser/" target="_blank" style="color:#25abe0;">Google Chrome</a> or
        		<a href="http://www.mozilla.org/en-US/firefox/new/" target="_blank" style="color:#25abe0;">Mozilla Firefox</a>.</p>
			<br/>
        	<p>We apologize for the inconvenience.</p>

        	<p>- The Wheeldo Team</p>
        </div>
        <![endif]-->

        <script src='/vendor/angularjs-1.0.7/angular.min.js'></script>
        <script src='/vendor/angularjs-1.0.7/angular-resource.min.js'></script>
        <script src='/vendor/angularjs-1.0.7/angular-cookies.min.js'></script>
<!--        <script src='/vendor/angularjs-1.2.17/angular-route.min.js'></script>-->
        <script src="/vendor/bootstrap-2.3.1/js/bootstrap-select.min.js"></script>
        <script src="/vendor/ui.bootstrap/ui-bootstrap-tpls-0.6.0.min.js"></script>
        <script src="/media/js/angular-sanitize.min.js"></script>
        <script src="/vendor/angular-touch/angular-touch.min.js"></script>

        <script src="/vendor/mobile-nav/mobile-nav.js"></script>
        <script src="/vendor/ckeditor/ckeditor.js"></script>
        <script src="/vendor/ckeditor/adapters/jquery.js"></script>
        <script src="/vendor/angular-google-chart-gh-pages/ng-google-chart.js"></script>
        <link rel="stylesheet" href="/vendor/mobile-nav/mobile-nav.css">

        <!-- App libs -->
        <script src="/app/app.js?t=<?=time()?>"></script>
        <script src="/app/directives/directives.js?t=<?=time()?>"></script>
        <script src="/app/includes/js/general.js?t=<?=time()?>"></script>
        <script src="/app/controllers/controllers.js?t=<?=time()?>"></script>
        <script src="/app/controllers/game.js?t=<?=time()?>"></script>
        <script src="/app/services/Services.js?t=<?=get_timestamp($local)?>"></script>
        <script src="/app/services/fb.js?t=<?=time()?>"></script>
        <script src="/vendor/angular-file-upload-master/angular-file-upload.js"></script>
        <script src="/vendor/spectrum/spectrum.js"></script>
        <script src="/vendor/google-plus-signin/google-plus-signin.js"></script>
        <link rel="stylesheet" href="/vendor/spectrum/spectrum.css" />
        
        <script type="text/javascript" src="/vendor/d3-funnel-charts/d3.v2.min.js"></script>
        <script type="text/javascript" src="/vendor/d3-funnel-charts/d3-funnel-charts.js"></script>
        <?php if($show_chat){?>
        <script type='text/javascript'>				
            (function () { var done = false;					
                var script = document.createElement('script');					
                script.async = true;					
                script.type = 'text/javascript';					
                script.src = 'https://app.purechat.com/VisitorWidget/WidgetScript';					
                document.getElementsByTagName('HEAD').item(0).appendChild(script);					
                script.onreadystatechange = script.onload = function (e) {						
                    if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {							
                        var w = new PCWidget({ c: '590a9578-125c-4461-b718-1b2943132d36', f: true });							
                        done = true;						
                    }					
                };				
            })();			
        </script>
        <?}?>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-51437874-1', 'wheeldo.co');
          ga('send', 'pageview');

        </script>
		<!-- Google Code for Remarketing Tag -->
		<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 989485590;
		var google_custom_params = window.google_tag_params;
		var google_remarketing_only = true;
		/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/989485590/?value=0&amp;guid=ON&amp;script=0"/>
		</div>
		</noscript>
		<!-- Facebook Code for Remarketing Tag -->
		<script>(function() {
	  		var _fbq = window._fbq || (window._fbq = []);
	  		if (!_fbq.loaded) {
	  			var fbds = document.createElement('script');
	  			fbds.async = true; fbds.src = '//connect.facebook.net/en_US/fbds.js';
	  			var s = document.getElementsByTagName('script')[0];
	  			s.parentNode.insertBefore(fbds, s); _fbq.loaded = true;
	  		}
	  		_fbq.push(['addPixelId', "438954486246342"]);
  		})();
		window._fbq = window._fbq || [];
		window._fbq.push(["track", "PixelInitialized", {}]);
		</script>
		<noscript><img height="1" width="1" border="0" alt="" style="display:none" src="https://www.facebook.com/tr?id=1234567890&amp;ev=NoScript" /></noscript>
                
                
                <!-- SessionCam Client Integration v6.0 -->
                <script type="text/javascript">
                //<![CDATA[
                var scRec=document.createElement('SCRIPT');
                scRec.type='text/javascript';
                scRec.src="//d2oh4tlt9mrke9.cloudfront.net/Record/js/sessioncam.recorder.js";
                document.getElementsByTagName('head')[0].appendChild(scRec);
                //]]>
                </script>
                <!-- End SessionCam -->
    </body>
</html>