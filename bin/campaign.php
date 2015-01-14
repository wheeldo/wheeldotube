<?php
require 'top.php';
$ex=explode("/",$_SERVER['REQUEST_URI']);
$l=$ex[count($ex)-1];
$campaign_page=$dbop->selectAssocRow("campaign_pages","WHERE `link`='{$l}'");
if(!$campaign_page) {
    die("Error!");
}
$campaign=$dbop->selectAssocRow("campaigns","WHERE `id`='{$campaign_page['campaign']}'");

$c_link="http://".$_SERVER['HTTP_HOST']."/cmp/".$l;
$game_link="http://".$_SERVER['HTTP_HOST']."/play/".$campaign_page['game']."?r=Campaign";

$data = file_get_contents("php://input");
$data_array=json_decode($data,true);
if(is_array($data_array)) {

    //registration proccess:
    $just_class=true;
    require 'operators.php';
    $op=new operators();
    $data_array['subscribe']=1;
    $new_password=substr(sha1(time()),0,10);
    ob_start();
    $op->regNewUserGame(array(
        "reg_data"=>$data_array,
        "gid"=>$campaign_page['game']
    ));
    ob_get_clean();
    
    header('Content-Type:application/json');
    echo json_encode(array("game_link"=>$game_link));
    die();
}



//vd($campaign_page);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?=$campaign['name']?> - <?=$campaign_page['title']?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta property="fb:app_id" content="1511607775728782"/>
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?=$c_link?>" />
        <meta property="og:title" content="<?=$campaign['name']?> - <?=$campaign_page['title']?>" />
        <meta property="og:image" content="<?=$campaign_page['image']?>" />
        <meta property="og:description" content="<?=convert_html_to_text($campaign_page['desc'])?>" />
        <style>
            html,
            body {
                font-family: Arial;
                margin:0;
                padding:0;
                height:100%;
                color:#373832;
            }
            
            h1,h2,h3 {
                margin:0;
                padding:0;
            }
            
            body {
                background-repeat:repeat;
                background-position: center top;
                <? if($campaign_page['image']!="") {?>background-image:url('<?=$campaign_page['image']?>');<?}?>
            }
            
            div {
                position: relative;
                margin:0px auto;
            }
            .wrapper {
                width:100%;
                max-width:1100px;
                border:0px solid black;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }
            
            .footer {
                height:80px;
                line-height:80px;
                width:100%;
                background-color:#000000;
                color:#ffffff;
                position:fixed;
                bottom:0px;
            }
            
            #logo {
                
            }
            
            .clr {
                clear:both;
            }
            
            .half {
                width:50%;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                border:0px solid red;
                margin-top:80px;
            }
            
            .left {
                float:left;
                padding-right:15px;
            }
            
            .right {
                float:right;
                padding-left:15px;
            }
            
            h1 {
                font-size:34px;
                line-height:34px;
                font-weight:bold;
                margin-bottom:15px;
            }
            
            h2 {
                font-size:24px;
                font-weight:normal;
            }
            
            h3 {
                font-size:22px;
                margin-bottom:15px;
            }
            
            .logo {
                margin-top:50px;
            }
            
            .form input[type="text"],
            .form input[type="email"]{
                display:block;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                width:100%;
                max-width:250px;
                height:32px;
                line-height:32px;
                border:1px solid #98B724;
                margin:12px 0px;
                padding:0px 5px;
                font-size:16px;
            }
            
            .form button {
                background-color:#98B724;
                color:#ffffff;
                border:1px solid #ABABAB;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                -webkit-border-radius: 3px;
                border-radius: 3px;
                -webkit-box-shadow: 2px 2px 3px 1px #8C8C8C;
                box-shadow: 2px 2px 3px 1px #8C8C8C;
                height:40px;
                width:100%;
                max-width:200px;
                font-size:24px;
                cursor:pointer;
            }
            
            .cta {
                background-color:#98B724;
                color:#ffffff;
                border:1px solid #ABABAB;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                -webkit-border-radius: 3px;
                border-radius: 3px;
                -webkit-box-shadow: 2px 2px 3px 1px #8C8C8C;
                box-shadow: 2px 2px 3px 1px #8C8C8C;
                height:40px;
                width:100%;
                max-width:200px;
                font-size:24px;
                cursor:pointer;
            }
            
            .wait {
                display:none;
            }
            
            .footer a {
                color:#ffffff;
                text-decoration:none;
                display:block;
                float:left;
                margin-right:20px;
                background-position:left center;
                background-repeat:no-repeat;
            }
            
            .footer a.fb {
                background-image:url('/media/img/Facebook.png');
                padding-left:20px;
            }
            
            .footer a.tw {
                background-image:url('/media/img/Twitter.png');
                padding-left:35px;
            }
            
            .footer a.in {
                background-image:url('/media/img/Linkedin.png');
                padding-left:35px;
            }
            
            .fb_connect {
                display:block;
                margin-top:15px;
            }
            
            
            @media screen and (max-width:1120px) {
                .wrapper {
                    padding:0px 20px;
                }
            }
            
            @media screen and (max-width:600px) {
                body {
                    padding-bottom:150px;
                }
                
                .logo {
                    margin-top:10px;
                }
                .half {
                    width:100%;
                    margin-top:20px;
                }
                
                .left,.right {
                    padding:0;
                }
                
                h1 {
                    font-size:28px;
                    margin-bottom:5px;
                }
            }
        </style>
    </head>
    <body ng-app="login" id="ng-app">
        <div id="fb-root"></div>
        <div ng-controller="AppController">
            <div class="wrapper">
                <div class="logo">
                    <img id="logo" src="/website/media/img/logo.png" />
                </div>

                <div class="half left">
                    <h1><?=$campaign['name']?></h1>
                    <h2>New Week, New Quiz, New prize.</h2>
                    <?php if($campaign_page['close']=="1"){ ?>
                    <div class="form">
                        <a class="fb_connect" ng-controller="appCtrl" href="javascript:void(0)" ng-click="loginNoReload(fb_coonect_succesided)">
                                <img class="arr" data-role="fb-login-btn" src="/media/img/facebook-connect-logo.png" style="max-width: 176px;margin-bottom:5px" />
                        </a>
                        OR:
                        <form ng-submit="join()">
                            <input type="text" name="fname" ng-model="user.fname" placeholder="First name" />
                            <input type="text" name="lname" ng-model="user.lname" placeholder="Last name" />
                            <input type="email" name="email" ng-model="user.email" placeholder="Email" />
                            <button type="submit">Play now!</button>
                        </form>
                    </div>
                    <div class="wait">
                        <div class="text">Please wait...</div>   
                        <img src="/media/img/ajax-loader_pink.gif" />
                    </div>
                    <?}?>
                </div>

                <div class="half right">
                    <h3><?=$campaign_page['title']?></h3>
                    <?=$campaign_page['desc']?>
                    
                    
                    <?php if($campaign_page['close']=="0"){ ?>
                    <br><br>
                    <button type="submit" class="cta" ng-click="playGameNow()">Play now!</button>
                    <?}?>
                </div>

                <br class="clr" />

            </div>
            <div class="footer">
                <div class="wrapper">
                    <a class="fb" ng-href="https://www.facebook.com/sharer.php?u=<?=$c_link?>" target="_blank">Share via Facebook</a>
                    <a class="tw" ng-href="https://twitter.com/share?url=<?=$c_link?>&text={{cmp_name}}" target="_blank">Tweet</a>
                    <a class="in" ng-href="http://www.linkedin.com/shareArticle?mini=true&url=<?=$c_link?>&title={{escape(cmp_name)}}&source=http://www.wheeldo.co" target="_blank">Share via Linkedin</a>
                    <span style="float:right;">&copy; Wheeldo 2014</span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
                window.jQuery || document.write("<script src='http://code.jquery.com/jquery-1.10.2.min.js'>"+"<"+"/script>");
        </script>
        <script src='/vendor/angularjs-1.0.7/angular.min.js'></script>
        <script src='/vendor/angularjs-1.0.7/angular-resource.min.js'></script>
        <script src="/app/services/fb.js?t=<?=time()?>"></script>
        <script>(function(i, s, o, g, r, a, m) {
                        i['GoogleAnalyticsObject'] = r;
                        i[r] = i[r] || function() {
                                (i[r].q = i[r].q || []).push(arguments)
                        }, i[r].l = 1 * new Date();
                        a = s.createElement(o), m = s.getElementsByTagName(o)[0];
                        a.async = 1;
                        a.src = g;
                        m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
                ga('create', 'UA-51437874-1', 'wheeldo.co');
                ga('send', 'pageview');



        </script>
        <script>
            function setProcessing() {
                $(".form").hide();
                $(".wait").show();
            }
            
            function stopProcessing() {
                
            }
            
            var app = angular.module('login', []);
            

            app.directive('formAutofillFix', function() {
                return function(scope, elem, attrs) {
                  // Fixes Chrome bug: https://groups.google.com/forum/#!topic/angular/6NlucSskQjY
                  elem.prop('method', 'POST');

                  // Fix autofill issues where Angular doesn't know about autofilled inputs
                  if(attrs.ngSubmit) {
                    setTimeout(function() {
                      elem.unbind('submit').submit(function(e) {
                        e.preventDefault();
                        elem.find('input, textarea, select').trigger('input').trigger('change').trigger('keydown');
                        scope.$apply(attrs.ngSubmit);
                      });
                    }, 0);
                  }
                };
              });
              
              app.run(function ($rootScope) {
                window.fbAsyncInit = function () {
                    FB.init({
                        appId:'1511607775728782',
                        status:true,
                        cookie:true,
                        xfbml:true
                    });

                    FB.Event.subscribe('auth.statusChange', function(response) {
                        $rootScope.$broadcast("fb_statusChange", {'status': response.status});
                    });
                };

                (function (d) {
                    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
                    if (d.getElementById(id)) {
                        return;
                    }
                    js = d.createElement('script');
                    js.id = id;
                    js.async = true;
                    js.src = "//connect.facebook.net/en_US/all.js";
                    ref.parentNode.insertBefore(js, ref);
                }(document));
            });
            
            app.factory('Facebook', function ($rootScope) {
                return {
                    getLoginStatus:function () {
                        FB.getLoginStatus(function (response) {
                            $rootScope.$broadcast("fb_statusChange", {'status':response.status});
                        }, true);
                    },
                    login:function () {
                        FB.getLoginStatus(function (response) {
                            switch (response.status) {
                                case 'connected':
                                    $rootScope.$broadcast('fb_connected', {facebook_id:response.authResponse.userID});
                                    break;
                                case 'not_authorized':
                                case 'unknown':
                                    /*FB.login(function (response) {
                                        if (response.authResponse) {
                                            $rootScope.$broadcast('fb_connected', {
                                                facebook_id:response.authResponse.userID,
                                                userNotAuthorized:true
                                            });
                                        } else {
                                            $rootScope.$broadcast('fb_login_failed');
                                        }
                                    }, {scope:'read_stream, publish_stream, email'});*/
                                   top.location = '/fb_auth';
                                    break;
                                default:
                                    FB.login(function (response) {
                                        if (response.authResponse) {
                                            $rootScope.$broadcast('fb_connected', {facebook_id:response.authResponse.userID});
                                            $rootScope.$broadcast('fb_get_login_status');
                                        } else {
                                            $rootScope.$broadcast('fb_login_failed');
                                        }
                                    });
                                    break;
                            }
                        }, true);
                    },
                    logout:function () {
                        FB.logout(function (response) {
                            if (response) {
                                $rootScope.$broadcast('fb_logout_succeded');
                            } else {
                                $rootScope.$broadcast('fb_logout_failed');
                            }
                        });
                    },
                    unsubscribe:function () {
                        FB.api("/me/permissions", "DELETE", function (response) {
                            $rootScope.$broadcast('fb_get_login_status');
                        });
                    }
                };
            });

            function AppController ($scope,$http) {
                $scope.escape = escape;
                $scope.gid='<?=$campaign_page['game']?>';
                $scope.cmp_name='<?=$campaign['name']?> - <?=$campaign_page['title']?>';
                $scope.join = function() {
                    if(!this.user || !this.user.fname) {
                        alert("Please fill out your first name!");
                        return;
                    }
                    if(!this.user.lname) {
                        alert("Please fill out your last name!");
                        return;
                    }
                    if(!this.user.email) {
                        alert("Please fill out your Email address!");
                        return;
                    }
                    
                    $(".form").hide();
                    $(".wait").show();
                    $http.post(window.location.href,{fname:this.user.fname,lname:this.user.lname,email:this.user.email})
                    .success(function(data, status, headers, config) {
                        $scope.user={};
                        //$(".form").show();
                        //$(".wait").hide();   
                        window.location.href=data.game_link;
                    });
                };
                
                
                $scope.fb_coonect_succesided = function() {
                    var game_link='<?=$game_link?>';
                    //alert(game_link);
                    window.location.href=game_link;
                };
                
                $scope.playGameNow = function() {
                    var game_link='<?=$game_link?>';
                    window.location.href=game_link;
                };
            };
        </script>
    </body>
</html>