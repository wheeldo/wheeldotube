<?php 
require_once "sys/include.php";
session_start();
if(isset($_SESSION['login_user']) || isset($_COOKIE["user_login"])) {
    
    if(isset($_SESSION['login_user'])) {
        $user=$_SESSION['login_user'];
    }
    elseif(isset($_COOKIE["user_login"])) {
        $user=json_decode($_COOKIE["user_login"],true);
    }
    
    
    
    if((int)$user['ghost']==0)
        header("location:/console");
    // check if login
//      /header("location:/console");
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Wheeldo | Play. Engage. Share. </title>
                 <link rel="icon" href="<?=$baseDir?>media/img/favicon.ico" type="image/x-icon" />
                 <script type="text/javascript">var BASE_DIR="<?=$baseDir?>";</script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
                
                <meta property="og:type" content="website" />
                <meta property="og:url" content="http://www.wheeldo.co" />
                <meta property="og:title" content="Wheeldo | Play. Engage. Share." />
                <meta property="og:image" content="http://www.wheeldo.co/media/img/wheeldo_share.jpg" />
                <meta property="og:description" content="Engage And Convert Your Online Traffic With Games! Create a game for your website, mailing list and social channels in minutes!" />

                <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-2.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/cssreset.css">
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/cssfonts.css">
		<!--<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">-->
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/smoothness/jquery-ui-1.10.3.custom.css">
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/agile_carousel.css">
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/wheeldoMain.css?t=<?=time()?>">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="<?=$baseDir?>media/js/agile_carousel.alpha.js"></script>
		<script type="text/javascript" src="<?=$baseDir?>media/js/general.js?t=<?=time()?>"></script>

		<script type="text/javascript" src="<?=$baseDir?>media/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
		<!-- Add fancyBox main JS and CSS files -->
		<script type="text/javascript" src="<?=$baseDir?>media/fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
		<!-- Add Button helper (this is optional) -->
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
		<script type="text/javascript" src="<?=$baseDir?>media/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
		<!-- Add Thumbnail helper (this is optional) -->
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
		<script type="text/javascript" src="<?=$baseDir?>media/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
		<!-- Add Media helper (this is optional) -->
		<script type="text/javascript" src="<?=$baseDir?>media/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>                
		<script type="text/javascript">
			$(document).ready(function() {
                            
                                var devices_img_sets=[
                                    ['website/media/images/hp_devices/mac.jpg','website/media/images/hp_devices/ipad.jpg','website/media/images/hp_devices/iphone.jpg'],
                                    ['website/media/images/hp_devices/mac2.jpg','website/media/images/hp_devices/ipad2.jpg','website/media/images/hp_devices/iphone2.jpg']
//                                    ['website/media/images/hp_devices/mac2.jpg','website/media/images/hp_devices/ipad2.jpg','website/media/images/hp_devices/iphone2.jpg']
                                ];
                            
                                hpDevicesStart(devices_img_sets);
				/*
				 *  Simple image gallery. Uses default settings
				 */

				$('.fancybox').fancybox();

				/*
				 *  Different effects
				 */

				// Change title type, overlay closing speed
				$(".fancybox-effects-a").fancybox({
					helpers: {
						title: {
							type: 'outside'
						},
						overlay: {
							speedOut: 0
						}
					}
				});

				// Disable opening and closing animations, change title type
				$(".fancybox-effects-b").fancybox({
					openEffect: 'none',
					closeEffect: 'none',
					helpers: {
						title: {
							type: 'over'
						}
					}
				});

				// Set custom style, close if clicked, change title type and overlay color
				$(".fancybox-effects-c").fancybox({
					wrapCSS: 'fancybox-custom',
					closeClick: true,
					openEffect: 'none',
					helpers: {
						title: {
							type: 'inside'
						},
						overlay: {
							css: {
								'background': 'rgba(238,238,238,0.85)'
							}
						}
					}
				});

				// Remove padding, set opening and closing animations, close if clicked and disable overlay
				$(".fancybox-effects-d").fancybox({
					padding: 0,
					openEffect: 'elastic',
					openSpeed: 150,
					closeEffect: 'elastic',
					closeSpeed: 150,
					closeClick: true,
					helpers: {
						overlay: null
					}
				});

				/*
				 *  Button helper. Disable animations, hide close button, change title type and content
				 */

				$('.fancybox-buttons').fancybox({
					openEffect: 'none',
					closeEffect: 'none',
					prevEffect: 'none',
					nextEffect: 'none',
					closeBtn: false,
					helpers: {
						title: {
							type: 'inside'
						},
						buttons: {}
					},
					afterLoad: function() {
						this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
					}
				});

				/*
				 *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
				 */

				$('.fancybox-thumbs').fancybox({
					prevEffect: 'none',
					nextEffect: 'none',
					closeBtn: false,
					arrows: false,
					nextClick: true,
					helpers: {
						thumbs: {
							width: 50,
							height: 50
						}
					}
				});

				/*
				 *  Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
				 */
				$('.fancybox-media')
						.attr('rel', 'media-gallery')
						.fancybox({
					openEffect: 'none',
					closeEffect: 'none',
					prevEffect: 'none',
					nextEffect: 'none',
					arrows: false,
					helpers: {
						media: {},
						buttons: {}
					}
				});

				/*
				 *  Open manually
				 */

				$("#fancybox-manual-a").click(function() {
					$.fancybox.open('1_b.jpg');
				});

				$("#fancybox-manual-b").click(function() {
					$.fancybox.open({
						href: 'iframe.html',
						type: 'iframe',
						padding: 5
					});
				});

				$("#fancybox-manual-c").click(function() {
					$.fancybox.open([
						{
							href: '1_b.jpg',
							title: 'My title'
						}, {
							href: '2_b.jpg',
							title: '2nd title'
						}, {
							href: '3_b.jpg'
						}
					], {
						helpers: {
							thumbs: {
								width: 75,
								height: 50
							}
						}
					});
				});


			});
		</script>
		<style type="text/css">
			.fancybox-custom .fancybox-skin {
				box-shadow: 0 0 50px #222;
			}

			body {
				/*max-width: 700px;
				margin: 0 auto;*/
			}
		</style>
	</head>
	<body ng-app='wheeldo_website_app' >
		<div id="mainContainer" ng-controller="appCtrl">
			<div id="headerRubber">
				<div id="headerWrapper">
					<?php
					include 'mainmenu.php';
					?>
				</div>
			</div>
<!--			<div id="block01RubberH">
				<div id="block01Wrapper" class="block01_home">
					<div id="blueWrapperL" class="fL">
                                            <h1>Engage And Convert Your Online Traffic With <span>Games!</span></h1>
                                            <h2>Create a game for your website, mailing list and social channels in minutes!</h2>
                                            
                                            <a href="/register">
                                            <div class="start_now">
                                                Start Now. It's FREE!
                                            </div>
                                            </a>
                                            <h6>No credit card needed or software to install.</h6>
					</div>
					<div id="blueWrapperR" class="fR">
						<div id="rightPlayer"></div>
                                                <div id="rightPlayerHP">
                                                    <div id="hpPlayer">
                                                        
                                                    </div>
                                                    
                                                </div>
					</div>
					<div class="cB"></div>
				</div>
			</div>-->
                    
                        <div id="block01RubberHNew">
				<div id="block01Wrapper" class="block01_home">
					<div id="blueWrapperL" class="fL">
<!--                                            <h1>Engage And Convert Your Online Traffic With <span>Games!</span></h1>
                                            <h2>Create a game for your website, mailing list and social channels in minutes!</h2>-->
                                            <h1>Generate Leads Using Interactive Quizzes</h1>
                                            <h2>Create a quiz for your website, mailing list and social channels in minutes!</h2>
                                            <a href="/register">
                                            <div class="start_now" onclick="track.__event('click','Start Now Home page');">
                                                Start Now. It's FREE!
                                            </div>
                                            </a>
                                            <h6>No credit card needed or software to install.</h6>
					</div>
                                    <div class="devices_amanda">
                                        
                                        <div class="devices">
                                            
                                        </div>
                                        <div class="devices_images">
                                            <div class="mac"></div>
                                            <div class="ipad"></div>
                                            <div class="iphone"></div>
                                        </div>
                                        <div class="amanda">
                                            
                                        </div>
                                    </div>
					<div class="cB"></div>
				</div>
			</div>
                        <div id="blockLogosRubber">
                            <div id="blockLogosWrapper" class="blocklogos_home">
                                <h3>Trusted by:</h3>
                                <div class="logos_row">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/1.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/2.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/3.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/4.png">
                                    <br class="cB">
                                </div>
                                <div class="logos_row">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/5.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/6.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/7.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/8.png">
                                    <br class="cB">
                                </div>
                                <div class="logos_row">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/9.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/10.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/11.png">
                                    <img class="logo" src="<?=$baseDir?>media/images/clients_logos/12.png">
                                    <br class="cB">
                                </div>
                                
                            </div>
                        </div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_home">
					<div id="whatWrapperTitle">
<!--						<h1>Why Choose Wheeldo for Content Marketing?</h1>-->
                                                <h1>Why use Wheeldo for Lead Generation?</h1>
                                                <h3>Enhance Engagement and Deliver an Effective Call to Action with Easy-to-Use Games.</h3>
					</div>
					<div id="whatWrapperL" class="fL hp_solutions">
                                                <div class="zoom new_hire_link">
                                                    
                                                    <h2>Unique Content<br><br></h2>
                                                </div>
						<p class="new_hire_link">Improve your relationship with your customer by providing them fun and shareable marketing games. Stand out of all the Social media marketing noise.</p>
						<div class="hb02SignUp bottomDiv"><a href="/register" ><div></div></a></div>
					</div>
					<div id="whatWrapperM" class="fL hp_solutions">
                                                <div class="zoom peer_learning_link">
                                                    <h2>Organic Engagement<br />&nbsp;</h2>
                                                </div>
						<p class="peer_learning_link">Content is evolving and no longer are white papers the secret to inbound marketing success; stimulating visual content has become the norm. Utilize visual interactive content like our easy-to-use games to achieve high engagement levels and experience viral sharing.</p>
						<div class="hb02SignUp bottomDiv"><a href="/register"><div></div></a></div>
					</div>
					<div id="whatWrapperR" class="fL hp_solutions">
                                                <div class="zoom knowledge_gaps_link">
                                                    <h2>Increase Sales<br />&nbsp;</h2>
                                                </div>
						<p class="knowledge_gaps_link">Increase lead flow and sales by maximizing the effectiveness of your content with a strong call to action, special offers and coupons.</p>
						<div class="hb02SignUp bottomDiv"><a href="/register"><div></div></a></div>
					</div>
					<div class="cL"></div>
				</div>
			</div>
			<div id="block03RubberH">
				<div id="block03Wrapper" class="block03_home">
					<div id="benefitWrapperTitleSmall">
						<h2>Play. Engage. Share.</h2>
					</div>
					<div id="benefitWrapperTitle">
						<h1>The Benefits</h1>
					</div>
					<div id="panel1" class="panel fL"><h2>Maximize</h2>
						<p>Maximize your level of engagement with your consumer by providing fun shareable content.</p>
					</div>
					<div id="panel2" class="panel fR"><h2>Optimize</h2><p>Increase engagement and conversions with content that brings consumers back again.</p></div>
					<div id="panel3" class="panel fL"><h2>Engage</h2><p>Engage and entertain your audience to create brand loyalty and repeat business.</p></div>
					<div id="panel4" class="panel fR"><h2>Innovate</h2><p>Gamification is the future of content marketing. Take the lead now. Run a Wheeldo campaign for free!</p></div>
                                        
                                        
                                        <div id="benefits_screen">
                                            
                                        </div>
				</div>
			</div>
			<div id="block04Rubber">
				<div id="block04sWrapper"></div>
			</div>
			<div id="block05Rubber">
				<div id="block05sWrapper"></div>
			</div>
			<div id="block06Rubber">
				<div id="block06sWrapper"></div>
			</div>
			<div id="footerRubber">
				<div id="footerWrapper">
					<?php
					include 'mainfooter.php';
					?>
				</div>
			</div>
                    
                        <div id="copyRight">
                            <div class="content_data">&copy 2014 Wheeldo</div>
                        </div>
		</div>
                
		<?php
		include 'signup.php';
		?>
                <script src='/vendor/angularjs-1.0.7/angular.min.js'></script>
                <script src='/vendor/angularjs-1.0.7/angular-resource.min.js'></script>
                <script src="/vendor/twitter/widgets.js"></script>
                <script src="/media/js/angular-sanitize.min.js"></script>
                <script src="/vendor/ui.bootstrap/ui-bootstrap-tpls-0.6.0.min.js"></script>
                <script src="/vendor/google-plus-signin/google-plus-signin.js"></script>
                
                
                <script>
                    // create the module and name it scotchApp
                    var app = angular.module('wheeldo_website_app', ['ui.bootstrap','ngSanitize']);

                    // create the controller and inject Angular's $scope
                    
                    
                    
                    app.controller('appCtrl', function($scope,$modal) {

                            // create a message to display in our view
                            $scope.message = 'Everyone come and see how good I look!';
                            
                            $scope.join_popup = function() {

                                var t=new Date().getTime();
                                var modalInstance = $modal.open({
                                  templateUrl: '/app/partials/dice/includes/join_popup.html?t='+t,
                                  keyboard:true,
                                  //backdrop:'static',
                                  windowClass: 'loginPopUp',
                                  controller: joinController,
                                  resolve: {
                                      f_data:function () {
                                        return {
                                            a:1
                                        }
                                      }
                                  }
                                });

                                modalInstance.result.then(function (status) {
                                    if(status==1)
                                        $("form").show();
                                    if(status==2)
                                        $scope.sign_in_popup();

                                }, function () {

                                });

                            };
                            
                            
                            var joinController = function ($scope, $http,  $modalInstance, f_data) {
                                    $scope.notes={};
                                    $scope.registerSubmit = function() {
                                        $scope.notes={};

                                        if(!this.password) {
                                            alert("Password must be at least 6 characters!");
                                            return;
                                        }

                                        if(this.password!==this.retype_password) {
                                            $scope.notes.error_01="Passwords don't match!";
                                            return;
                                        }



                                        regNewUserService(this);
                                    };

                                    $scope.joinClicked = function() {
                                        $(".sign_in_form").removeClass("cancel_error");
                                    };


                                    $scope.checkIFEmailExits = function(email) {
                                      if(email)
                                        checkEmailService(email,$scope.notes);
                                    };


                                    $scope.close = function() {
                                        $modalInstance.close(1);
                                    };

                                    $scope.sign_in = function() {
                                        $modalInstance.close(2);
                                    };

                                    $scope.close_popup = function() {
                                        $modalInstance.close(0);
                                    };

                                };
                            
                            
                    });
                </script>
                
                
                
                <?php
                    include "sys/bottom_scripts.php";
                ?>
                
	</body>
</html>
