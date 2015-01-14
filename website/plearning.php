<?php 
require_once "sys/include.php";
?>
<!DOCTYPE html>
<html>
	<head>
                <title>Wheeldo | Share, Play, Learn | Peer Learning</title>
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
				<div id="block01Wrapper" class="block01_plearning">
					<h1>Peer Learning</h1>
					<div id="startNow"><a href="javascript:void(0)" class="signUpBinder"><div></div></a></div>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_plearning">
					<h2>Encourage Knowledge Sharing And New Ideas Among Peers</h2>
					<div id="plearningDummy"></div>
					<div class="genericTxt01">Nearly 90% of employees gain knowledge through informal learning. Thanks to our peer-to-peer learning solution, your group leaders can easily enable team members to ask questions, learn from peers and provide feedback. At the same time, team members can create effective discussions and ignite innovation with easy-to-use social and game applications.</div>
				</div>
			</div>
			<div id="block03Rubber">
				<div id="block03Wrapper" class="block03_plearning">
				</div>
			</div>
			<div id="block04Rubber">
				<div id="Item01Bkg">
					<div id="block04Wrapper" class="block04_plearning">
						<div class="block04LCube"><h2>Share Knowledge:</h2><p>With our interactive and social tools, employees get the information they need, when they need it. Provide real-time feedback and encourage social-peer learning.</p></div>
						<div class="block04RCube plItem01"></div>
					</div>
					<div class="cL"></div>
				</div>
				<div id="Item02Bkg">
					<div id="block04Wrapper" class="block04_plearning">
						<div class="block04LCube plItem02"><h2></h2><p></p></div>
						<div class="block04RCube fL"><h2>Engagement:</h2><p>Engage and motivate your teams with social and gaming through our Informal Learning solution. Studies show that well-engaged employees are more productive, customer-focused, accountable and loyal to the organization than their disengaged colleagues. And this engagement translates directly to a company's ROI.</p></div>
					</div>
					<div class="cL"></div>
				</div>
				<div id="Item03Bkg">
					<div id="block04Wrapper" class="block04_plearning">
						<div class="block04LCube"><h2>New Ideas:</h2><p>With our brainstorming tools, managers and team leaders can easily consult with their employees to develop new, innovative concepts and provoke thinking. Team members can voice ideas and opinions, share their knowledge and provide feedback.</p></div>
						<div class="block04RCube plItem03 fL"><h2></h2><p></p></div>
					</div>
					<div class="cL"></div>
				</div>
				<div id="Item04Bkg">
					<div id="block04Wrapper" class="block04_plearning">
						<div class="block04LCube plItem04"><h2></h2><p></p></div>
						<div class="block04RCube fL"><h2>Hassle Free:</h2><p>Our solutions are easy to use and set up. In just two easy steps you can start to enjoy the benefits of our solutions. Simply choose a question and target your audience. Then your managers can start to interact and consult with their teams.</p></div>
					</div>
					<div class="cL"></div>
				</div>
				<div id="Item03Bkg">
					<div id="block04Wrapper" class="block04_plearning">
						<div class="block04LCube"><h2>Cost-effective:</h2><p>Reduce administration costs and save on time management without the time and geographical constraints.</p></div>
						<div class="block04RCube plItem05 fL"><h2></h2><p></p></div>
					</div>
					<div class="cB"></div>
				</div>
			</div>
			<div id="block05Rubber">
				<div id="block05Wrapper" class="block05_plearning">
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
