<?php 
require_once "sys/include.php";
include "../bin/top.php";


$data = file_get_contents("php://input");
$data_array=json_decode($data,true);

if($data_array):
    $res=array();
    $just_class=true;
    require_once "../bin/operators.php";
    $op=new operators();
    
    $reg_data=$data_array['reg_data'];
    $check=$dbop->selectAssocRow("users","WHERE `email`='{$reg_data['email']}'");
    
    if($check) {
        $res['status']="faild";
        $res['error']="Email address already exists!";
        echo json_encode($res);
        die();
    }
    else {
        $res['status']="ok";
        
        $op->gameFormRR(array("data"=>$reg_data));
        
        
        
        
    }

    die();
endif;





?>
<!DOCTYPE html>
<html>
	<head>
		<title>Wheeldo - Sign up</title>
                <link rel="icon" href="<?=$baseDir?>media/img/favicon.ico" type="image/x-icon" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssreset.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssfonts.css">
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/wheeldoMain.css">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="/website/media/js/general.js"></script> 
	</head>
	<body ng-app='wheeldo_website_app'>
		<div id="mainContainer" ng-controller="signupCtrl">
			<div id="headerRubber">
				<div id="headerWrapper">
					<?php
					include 'mainmenu.php';
					?>
				</div>
			</div>
			<div id="block01RubberNH">
				<div id="block01Wrapper">
					<h1>Sign up</h1>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_reg">
                                    <div class="left reg_wrap">
                                        <form ng-submit="register()">
                                            <div class="fr">
                                                <label>Email:</label>
                                                <input tabindex="1" ng-required="validate_form" name="email" ng-model="reg.email" type="email" placeholder="e.g. John@gmail.com " />
                                            </div>
                                            <div class="fr">
                                                <label>First Name:</label>
                                                <input tabindex="2" ng-required="validate_form" name="fname" ng-model="reg.fname" type="text" />
                                            </div>
                                            <div class="fr">
                                                <label>Last Name:</label>
                                                <input tabindex="3" ng-required="validate_form" name="lname" ng-model="reg.lname" type="text" />
                                            </div>
<!--                                            <div class="fr">
                                                <label>Password:</label>
                                                <input tabindex="4" ng-required="validate_form" ng-model="reg.password" type="password" placeholder="More than 6 characters" />
                                            </div>
                                            <div class="fr">
                                                <label>Retype Password:</label>
                                                <input tabindex="5" ng-required="validate_form" ng-model="reg.retype" type="password" />
                                            </div>-->
                                            <div class="terms_check">
                                                <input type="checkbox" ng-model="acc_terms" />
                                                
                                                I accept <a target="_blank" href="/terms">terms of use</a>
                                            </div>
                                            <div class="note">
                                                <span ng-bind="note"></span>
                                            </div>
                                            <div class="start_wrap">
                                                <button tabindex="6" class="start" type="submit">Start</button>
                                            </div>
                                            <div class="wait_wrap">
                                                   Please wait...
                                            </div>

                                        </form>
                                    </div>
                                    
                                    <div class="right alt_signups ">
                                        <div class="right_padd">
                                            <h4>Or sign up with:</h4>

                                            <div class="sign_up_option social_links">
                                               <a class="fb" ng-controller="appCtrl" href="javascript:void(0)" ng-click="loginNoReload(signup_fb_connect)">
                                                    FACEBOOK CONNECT
                                                </a> 
                                            </div>

                                            <div class="signup_text">
                                                <h5>With Wheeldo you can</h5>

                                                Increase engagement <br>
                                                Build your relationship with your clients <br>
                                                Generate and qualify leads <br>
                                                Stand out of all the social media noise <br>
                                                And much much more!
                                            </div>
                                        </div>
                                    </div>
                                    <br class="clr">
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
        <script src='/vendor/angularjs-1.0.7/angular.min.js'></script>
                <script src='/vendor/angularjs-1.0.7/angular-resource.min.js'></script>
                <script src="/vendor/twitter/widgets.js"></script>
                <script src="/media/js/angular-sanitize.min.js"></script>
                <script src="/vendor/ui.bootstrap/ui-bootstrap-tpls-0.6.0.min.js"></script>
                <script src="/vendor/google-plus-signin/google-plus-signin.js"></script>
                <script src="/app/services/fb.js"></script>   
                
                <script>
                    // create the module and name it scotchApp
                    var app = angular.module('wheeldo_website_app', ['ui.bootstrap','ngSanitize']);
                    
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
                    
                    
                    
                    app.controller('signupCtrl', function($scope,$http,$modal) {
                        $scope.validate_form=false;
                        $scope.acc_terms=true;
                        
                        
                        $scope.signup_fb_connect = function() {
                            window.location.href="/Convert";
                        };
                        
                        $scope.register = function() {
                            $scope.note="";
                            $scope.validate_form=true;
                            if(!this.reg)
                                return;
                            
                            
                            
//                            if(this.reg.password !== this.reg.retype) {
//                                $scope.note="Passwrod doesn't match";
//                                return;
//                            }
//                            
//                            if(this.reg.password.length<6) {
//                                $scope.note="Password minimum length is 6 characters";
//                                return;
//                            }
                            
                            if(!$scope.acc_terms) {
                                $scope.note="Please read and accept terms of use";
                                return;
                            }
                            
                            
                            
                            if(this.reg.email && this.reg.fname && this.reg.lname) {
                                $(".start_wrap").hide();
                                $(".wait_wrap").show();
                                
                                $http.post('/register',{reg_data:this.reg})
                                .success(function(data, status, headers, config) {
                                    if(data.status=="faild") {
                                        alert(data.error);
                                        $(".start_wrap").show();
                                        $(".wait_wrap").hide();
                                        return;
                                    }
                                    else {
                                        window.location.href="/Convert";
                                    }
                                });  
                            }
                            
                        };
                            
                    });
                    
                    function setProcessing() {
                        
                    }
                    
                    function stopProcessing() {
                        
                    }
                </script>
</html>

<style>
    
    #block01Wrapper {
        width:100%;
        max-width:940px;
        margin:0px auto;
        height:60px;
        line-height:60px;
        font-size:40px;
        color:#33ADDF;
    }
    
    .terms_check {
        height:20px;
        line-height:20px;
    }
    
    .terms_check input {
        vertical-align: middle;
        margin-right:5px;
    }
    
    .right_padd {
        max-width:250px;
        margin:0px auto;
    }
    
    
    .signup_text h5 {
        color:#000000;
        font-size:14px;
        font-weight:bold;
    }
    
    .signup_text {
        color:#969696;
        
        margin:0px auto;
        font-size:12px;
        line-height:22px;
    }
    .social_links {
/*        text-align: center;*/
    }
    
    .social_links a {
        display:inline-block;
        margin:0px 5px;
        min-width:120px;
        height:40px;
        line-height:40px;
        color:#ffffff;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        font-size:16px;
        background-position: left center;
        background-repeat: no-repeat;
        padding-left:40px;
        padding-right:20px;
        margin-bottom:10px;
        text-decoration: none;
    }
    
    .social_links a:hover {
        text-decoration: none;
    }
    
    .social_links a.fb {
        background-color:#2D4373;
        background-image: url('/media/img/fb_icon.png');
    }
    
    .social_links a.ggl{
        background-color:#DF4A32;
        background-image: url('/media/img/ggl_icon.png');
    }
    .fb {
        
    }
    
    .sign_up_option {
        margin:20px 0px;
    }
    .alt_signups {
        font-size:16px;

    }
    .wait_wrap {
        display:none;
        margin-top:30px;
        height:50px;
        line-height:50px;
        font-size:16px;
    }
    .note {
        margin:20px 0px;
        color:red;
        font-size:13px;
    }
    .start_wrap {
        margin-top:30px;
    }
    
    .start {
        display:block;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        border:0px;
        width:150px;
        height:50px;
        background-color:#98B724;
        color:#ffffff;
        font-size:20px;
        font-weight:bold;
        cursor:pointer;
    }
    .fr {
        height:30px;
        margin:20px 0px;
    }
    
    .fr label {
        height:30px;
        line-height:30px;
        font-size:14px;
        font-weight:bold;
    }
    
    .fr input[type="text"],
    .fr input[type="email"],
    .fr input[type="password"]{
        float:right;
        height:30px;
        line-height:30px;
        font-size:14px;
        padding:0px 4px;
        width:220px;
    }
    
    #block02Rubber {
       
       background-color: #F5F5F5;
       padding:50px 0px;
    }
    .clr {
        clear:both;
    }
    .block02_reg {
        width:100%;
        max-width:940px;
        margin:0px auto;
        
    }
    
    .block02_reg .left {
        width:50%;
        float:left;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        padding:30px 50px;
    }
    
    .block02_reg .right {
        width:50%;
        float:right;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        padding:30px 50px;
    }
    
    .reg_wrap {
        min-height:300px;
        background-color:#ffffff;
        -webkit-border-radius: 8px;
        border-radius: 8px;
    }
    
    @media screen and (max-width:940px) {
        #block02Rubber {
            padding:0px;
        }
        .block02_reg .left,
        .block02_reg .right{
            float:none;
            width:100%;
        }
        
        .fr {
            height:60px;
            margin:5px 0px;
        }
        .fr label {
            display:block;
        }

        .fr input[type="text"],
        .fr input[type="email"],
        .fr input[type="password"]{
            width:100%;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }
        
        .block02_reg .left {
            padding: 10px 20px;
        }
        
        .start {
            width:100%;
            height:34px;
        }
    }
    
    @media screen and (max-width:400px) {
        .block02_reg .left {
            padding: 10px 10px;
        }
    }
    
    
</style>

