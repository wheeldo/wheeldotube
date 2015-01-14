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
			<div id="block01RubberNH">
				<div id="block01Wrapper" class="block01_pricing">
					<h1>Pricing</h1>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="block02_pricing">
					<div id="priceLstContainer">
						<table align="center" cellpadding="0" cellspacing="0">
							<tr class="bkgColor trBorderNone">
								<td class="tdBorder2pxRight"></td>
								<td class="titleB tdBorder2pxRight">FREE</td>
								<td class="titleB tdBorder2pxRight">SILVER</td>
								<td class="titleB tdBorder2pxRight">GOLD</td>
								<td class="titleB tdBorder2pxRight">PLATINUM</td>
								<td class="titleB">ENTERPRISE</td>
							</tr>
<!--							<tr class="bkgColor">
								<td class="tdBorder2pxRight"></td>
								<td class="titleN tdBorder2pxRight tdBorder2pxBottom">Try it with one team</td>
								<td class="titleN tdBorder2pxRight tdBorder2pxBottom">Roll it in your department</td>
								<td class="titleN tdBorder2pxRight tdBorder2pxBottom">Roll it in your department</td>
								<td class="titleN tdBorder2pxRight tdBorder2pxBottom">Small-to-medium sized company</td>
								<td class="titleN tdBorder2pxBottom">Go pro with a dedicated customer service rep.</td>
							</tr>-->
							<tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Plays</td>
								<td class="tNrB tdBorder2pxRight">Up to 30/Mon</td>
								<td class="tNrB tdBorder2pxRight">Up to 100/Mon</td>
								<td class="tNrB tdBorder2pxRight">Up to 500/Mon</td>
								<td class="tNrB tdBorder2pxRight">Up to 1000/Mon</td>
								<td class="tNrB">1000+</td>
							</tr>
							<tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Price</td>
								<td class="priceB tdBorder2pxRight">$0<sub>/month</sub></td>
								<td class="priceB tdBorder2pxRight">$19<sub>/month</sub></td>
								<td class="priceB tdBorder2pxRight">$49<sub>/month</sub></td>
								<td class="priceB tdBorder2pxRight">$99<sub>/month</sub></td>
                                                                <td class="priceB"><a href="mailto:info@wheeldo.com?subject=Inquiry about your enterprise program">Email us</a></td>
							</tr>
							<tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Unlimited Emails</td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx"></td>
							</tr>
							<tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Unlimited games</td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx"></td>
							</tr>
							<tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Embed anywhere</td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx"></td>
							</tr>
							<tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Custom design</td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx"></td>
							</tr>
                                                        <tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Full analytics</td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx"></td>
							</tr>
                                                        <tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Ready made content</td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx"></td>
							</tr>
							<tr class="trBorder2pxDotted">
								<td class="rowtitleN bkgColor tdBorder2pxRight">Professional writer</td>
								<td class="priceMinus tdBorder2pxRight"></td>
								<td class="priceMinus tdBorder2pxRight"></td>
								<td class="priceMinus tdBorder2pxRight"></td>
								<td class="priceCbx tdBorder2pxRight"></td>
								<td class="priceCbx"></td>
							</tr>
							<tr>
								<td class="tdBorder2pxRight"></td>
								<td class="startNowPr tdBorder2pxRight"><a href="/CreateGameRR" ><div></div></a></td>
								<td class="startNowPr tdBorder2pxRight"><a href="/CreateGameRR" ><div></div></a></td>
								<td class="startNowPr tdBorder2pxRight"><a href="/CreateGameRR" ><div></div></a></td>
								<td class="startNowPr tdBorder2pxRight"><a href="/CreateGameRR" ><div></div></a></td>
								<td class="startNowPr"><a href="/CreateGameRR" ><div></div></a></td>
							</tr>
						</table>
					</div>
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
			ga('send', 'pageview');</script>
	</body>
</html>
