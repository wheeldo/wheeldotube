<?php 
require_once "sys/include.php";
?>
<!DOCTYPE html>
<html>
	<head>
                <title>Wheeldo | Share, Play, Learn | New Hire</title>
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
				<div id="block01Wrapper" class="block01_newhire">
					<h1>New Hire Onboarding</h1>
					<div id="startNow"><a href="javascript:void(0)" class="signUpBinder"><div></div></a></div>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_newhire">
					<h2>Engage and motivate your new employee!</h2>
					<div id="newhireDummy"></div>
					<div class="genericTxt01">Motivating and engaging training for your new employees will lead to more efficient and productive personnel. Provide your new employees with innovative, fun and easy-to-use training apps to hit the ground running with our Social Gamified Platform.</div>
				</div>
			</div>
			<div id="block03Rubber">
				<div id="block03Wrapper" class="block03_newhire">
					<div id="Item01"><h2>Reduce time</h2><p>Reduce time to competency.<br>Build relationships with highly personalized emails.</p></div>
					<div id="Item02"><h2>Construct efficiency</h2><p>Construct an efficient,<br>personal onboarding process</p></div>
					<div id="Item03"><h2>Engage and motivate</h2><p>Engage and motivate employees with fun, interactive applications</p></div>
					<div id="Item04"><h2>Reduce costs</h2><p>Reduce administrative costs.</p></div>
					<div class="cL"></div>
				</div>
			</div>
			<div id="block04RubberNH">
				<div id="block04Wrapper" class="block04_newhire">
					<h1>How it works:</h1>
					<div class="block04LCube mT001"><h2>Social Onboarding:</h2><p>It all begins with the Welcome Wall. Your new employees can access the Welcome Wall even prior to their start date to meet and greet their new team and managers.</p></div>
					<div class="block04RCube Item01 mT001"></div>
					<div class="cL"></div>
				</div>
				<div id="block041RubberNH">
					<div id="block041Wrapper" class="block041_newhire">
						<div id="Item02Bkg">
							<div class="block04LCube Item02"><h2></h2><p></p></div>
							<div class="block04RCube h001 fL"><h2>Training Materials:</h2><p>Provide your new recruits with company-specific materials, such as presentations, documents, links and videos. Your material integrates seamlessly with our user and administrator-friendly interface platform.</p></div>
						</div>
						<div class="cL"></div>
					</div>
				</div>
				<div id="block042Wrapper" class="block042_newhire">
					<div class="block04LCube"><h2>Knowledge-Building Games:</h2><p>Social Trivia Quiz:</p>
						<p>Quiz new employees on information, whether about your company or specific duties. Add healthy competition to the workplace by putting your staff members against one another to obtain the highest trivia scores.</p>
						<p>Share Tips - through the Interactive Voter Game, employees can share work tips with one another, pointers that increase team efficiency and allow new team members to benefit from the knowledge of more experienced employees.</p></div>
					<div class="block04RCube Item03 fL"><h2></h2><p></p></div>
					<div class="cB"></div>
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