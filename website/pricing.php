<?php 
require_once "sys/include.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssreset.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssfonts.css">
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/wheeldoMain.css">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="/website/media/js/general.js"></script>                
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
                        
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_pricing">
					<?php
                                        include 'pricing_table.php';
                                        ?>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block03Wrapper" class="block03_pricing">
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
</html>
