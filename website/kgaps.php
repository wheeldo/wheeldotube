<?php 
require_once "sys/include.php";
?>
<!DOCTYPE html>
<html>
	<head>
                <title>Wheeldo | Share, Play, Learn | Knowledge Gaps</title>
                 <link rel="icon" href="<?=$baseDir?>media/img/favicon.ico" type="image/x-icon" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <script type="text/javascript">var BASE_DIR="<?=$baseDir?>";</script>
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/cssreset.css">
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/cssfonts.css">
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="<?=$baseDir?>media/css/wheeldoMain.css">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="<?=$baseDir?>media/js/general.js"></script>
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
				<div id="block01Wrapper" class="block01_kgaps">
					<h1>Knowledge Gaps</h1>
					<div id="startNow"><a href="javascript:void(0)" class="signUpBinder"><div></div></a></div>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_kgaps">
					<h2>Interactive games to overcome knowledge gaps</h2>
					<div id="kgapsDummy"></div>
					<div class="genericTxt01">The workplace environment is constantly changing and evolving with new regulations, technologies and innovations. Your staff must adjust and overcome knowledge gaps quickly in order to stay competitive. Our solution to knowledge gaps will provide your team access to interactive, social-learning apps to obtain the information they need. Team members can then take the solution to put the theory into practice.</div>
				</div>
			</div>
			<div id="block03Rubber">
				<div id="block03Wrapper" class="block03_kgaps">
					<div id="Item01"><!--<h2></h2>--><p>Effective learning to overcome knowledge gaps</p></div>
					<div id="Item02"><!--<h2></h2>--><p>Support for a higher level of proficiency.</p></div>
					<div id="Item03"><!--<h2></h2>--><p>Apply theories while assisting team members</p></div>
					<div class="cL"></div>
				</div>
			</div>
			<div id="block04Rubber">
				<div id="block04Wrapper" class="block04_kgaps">
					<h1>How it works:</h1>
					<div id="Item0xBkg">
						<div id="parrotsWrapper"><h2>Invite your team</h2></div>
					</div>
					<div id="heartsWrapper"><h2>Social and Gamified training</h2></div>
					<div id="bottomWrapper"><h2>Effective Learning & Learning Retention</h2></div>
				</div>
			</div>
			<div id="block05Rubber">
				<div id="block05Wrapper" class="block05_kgaps">
					<div id="startNowKg"><a href="javascript:void(0)" class="signUpBinder"><div></div></a></div>
					<div id="t05">No risk. No credit card or software to install.</div>
				</div>
			</div>
			<div id="footerRubber">
				<div id="footerWrapper">
					<?php
					include 'mainfooter.php';
					?>
				</div>
			</div>
                        <div id="copyRight">
                            <div class="content_data">&copy 2013 Wheeldo</div>
                        </div>
		</div>
		<?php
		include 'signup.php';
		?>
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
			ga('create', 'UA-39562620-1', 'wheeldo.com');
			ga('send', 'pageview');</script>
	</body>
</html>
