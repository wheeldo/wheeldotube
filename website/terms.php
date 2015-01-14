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
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssreset.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/cssfonts.css">
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="/website/media/css/wheeldoMain.css">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="/website/media/js/general.js"></script>                
	</head>
	<body ng-app='wheeldo_website_app'>
		<div id="mainContainer" ng-controller="appCtrl">
			<div id="headerRubber">
				<div id="headerWrapper">
					<?php
					include 'mainmenu.php';
					?>
				</div>
			</div>
			<div id="block01RubberNH">
				<div id="block01Wrapper" class="block01_pricing">
					<h1>Giver Games Software License Agreement</h1>
				</div>
			</div>
			<div id="block02Rubber">
				<div id="block02Wrapper" class="content">

<p>In consideration for your use of the Giver Games&nbsp; Software and any updates, upgrades, new releases and\or related documentation thereto (The <strong>“Software</strong>”) as provided by Giver games Ltd. (“<strong>Licensor”</strong>), you ("<strong>You</strong>", the <strong>“User</strong>”) hereby agree and covenant to the following terms and conditions. If these terms are unacceptable to You, You may not use the Software, or, have You already used it, You must immediately cease any use thereof.</p>

<p>&nbsp;</p>

<p>The Licensor may modify these terms from time to time and such modification shall be effective upon posting by the Licensor on the Licensor's website. You agree to be bound to any changes to these terms when you use the Software after any such modification is posted.</p>

<p>&nbsp;</p>

<p>1. Grant of License</p>

<p>Licensor hereby grants the User a worldwide (subject to the provisions of section ‎7 below), non-exclusive, non-sublicenseable, time limited (for a period as described in section ‎8 below or such other longer or shorter time period as designated by the Licensor, at its sole discretion) as of the date in which the Software was made available to You by the Licensor), and non-transferable license to use the Software using your credentials (user name and password). It is expressly provided and agreed that Licensor reserves all rights to the Software not expressly granted under this Agreement. The license granted hereunder only grants User the right to use the Software under the terms, conditions, and restrictions specified in this Agreement. User does not by virtue of this Agreement acquire any other right, title or interest in the Software or any copyrights or other intellectual property rights therein. Licensor reserves the right at any time, without liability or prior notice, to change the features or characteristics of the Software, this Agreement, or the Software’s documentation and any related materials, at its sole discretion.</p>

<p>2. Payment Terms</p>

<p>Some parts of the use of the Software by the User, require payments of fees. the User shall pay the Licensor monthly payment in accordance with the package User has purchased (the: <strong>"monthly Payment</strong>"). The Monthly Payment will be paid at the beginning of each month for the following month. The Monthly Payment us exclusive of all taxes, levies, or duties imposed by taxing authorities, and the User will be responsible for payment of all such taxes, levies, or duties. If any payment is not made when due, Licensor reserves the right, among further remedies, either to terminate this Agreement or suspend the performance of this Agreement until payment in full has been made. Licensor may terminate this Agreement at any time, in accordance with the provisions of section ‎8 "Termination". The Licensor reserves the right to change its prices and or to request future payments for parts that were free, without cost to the User before, at any time.</p>

<p>3. License Restrictions</p>

<p>a. User acknowledges that the Software and any related know how, source code constitute valuable trade secrets and know how of Licensor. User agrees not to (i) copy, perform, distribute, modify, adapt, alter, translate, or create derivative works from the Software; (ii) merge the Software with other software; (iii) sublicense, lease, rent, or loan the Software to any third party; (iv) reverse engineer, decompile, disassemble, or otherwise attempt to derive the source code for the Software; or (v) otherwise use the Software except as expressly allowed in this Agreement, nor to permit any third party to do any of the forgoing, except in any case and to the extent the foregoing restrictions are expressly prohibited by any applicable law.<br>
b. Licensor retains exclusive ownership of all worldwide copyrights, trade marks, service marks, trade secrets, patent rights, moral rights, property rights and all other industrial rights in the Software and documentation, including any derivative works, modification, updates, or enhancements. All rights in and to the Software not expressly granted to User in this Agreement are reserved by Licensor. Nothing in this Agreement shall be deemed to grant, by implication, estoppel or otherwise, a license under any of Licensor’s existing or future patents.<br>
c. User shall not use the Software in any way that violates any local, state, federal or law of other nations, including but not limited to the posting of information that may violate third party rights, that may defame a third party, that may be obscene or pornographic, that may harass or assault others, that may violate computer crime regulations, etc.<br>
d. You acknowledge that Licensor may cease to support any or all versions of the Software at any time, with or without prior notice, for any reason or for no reason, and You shall have no claim against Licensor with respect thereto.</p>

<p>e. You accept sole responsibility for all of your activities using the Software, including any and all content you may submit. You will be responsible for ensuring that you do not violate any laws of your jurisdiction or any third party rights, including but not limited to copyright laws. You will not upload or transmit viruses, worms or any other destructive code.</p>

<p>4. Data Collection, Software updates</p>

<p>a. The Licensor may, from time to time, collect or use the information from the computer using the Software. Such information shall include names, e-mail addresses and any data submitted or used by the User in the Software b. Licensor shall, from time to time, automatically update the Software, in accordance with Licensor's sole discretion.</p>

<p>5. Support</p>

<p>Licensor shall make reasonable commercial efforts to diagnose and correct verifiable and reproducible problems with the Software that interfere with the reasonable functionality of the Software (“Errors”) when Errors are reported to Licensor in accordance with this Agreement. User shall make best efforts to assist Giver in reproducing any Error. User can contact Licensor via e-mail: <a href="mailto:info@wheeldo.com">info@wheeldo.com</a> for assistance and Licensor will make commercially reasonable efforts to respond within 48 hours. Licensor cannot provide support for use of the Software not in accordance with this Agreement or where the user experience or interaction with the Software has been modified.</p>

<p>&nbsp;</p>

<p>﻿6. Licensor's Warranty</p>

<ol>
	<li>THE SOFTWARE IS PROVIDED "AS IS" AND THE LICENSOR MAKES NO REPRESENTATION AND GIVES NO WARRANTY AS TO ITS PERFORMANCE OR USE THEREOF. EXCEPT FOR ANY WARRANTY THE EXTENT TO WHICH CANNOT BE EXCLUDED OR LIMITED BY APPLICABLE LAW THE LICENSOR MAKES NO WARRANTY OR REPRESENTATION (EXPRESSED OR IMPLIED) AS TO ANY MATTER INCLUDING WITH RESPECT TO THE SOFTWARRE OR THE USE THEREOF, INCLUDING WITHOUT LIMITATION, NONINFRINGEMENT OF THIRD PARTY RIGHTS, QUALITY, MERCHANTABILITY, OR APPLICABILITY FOR A PARTICULAR USE. USER ASSUMES ALL RISK ASSOCIATED WITH THE USE AND THE PERFORMANCE OF THE SOFTWARE. WITHOUT DEROGAING FROM THE AFORESAID, THE LICENSOR MAKES NO REPRESENTATION AND GIVES NO WARRANTY THAT THE SOFTWARE WILL BE ERROR-FREE OR FREE FROM DEFECTS OR ANY OTHER INTERUPTIONS. USER MAY, AT ANY TIME, AT ITS SOLE DISCRETION, CEASE TO MAKE USE OF THE SOFTWARE, BEEING USER'S SOLE AND EXCLUSIVE REMEDY IN ANY CASE WHERE ANY OF THE AFORESAID HAS OCCURRED.<br>
	b. THE FOREGOING WARRANTY IS USER'S SOLE AND EXCLUSIVE REMEDY TOWARDS LICENSOR, AND IS IN LIEU OF ANY AND ALL OTHER WARRANTIES, GUARANTEES, PROMISES, OR REPRESENTATIONS WHETHER WRITTEN, ORAL OR IMPLIED, INCLUDING WARRANTIES OF MERCHANTABILITY, SATISFACTORINESS OR FITNESS FOR ANY PARTICULAR PURPOSE OR USE. IN NO EVENT SHALL LICENSOR BE LIABLE FOR LOSS OF USE, LOSS OF PROFITS, OR OTHER COLLATERAL, SPECIAL OR CONSEQUENTIAL DAMAGES.<br>
	c. NOTHING IN THIS AGREEMENT EXCLUDES OR LIMITS ANY CLAIM FOR DEATH AND PERSONAL INJURY. FURTHER IN THE EVENT ANY DISCLAIMER, EXCLUSION OR LIMITATION IN THIS AGREEMENT CANNOT BE EXLUDED OR LIMITED ACCORDING TO APPLICABLE LAW THEN ONLY SUCH DISCLAIMER, EXCLUSION OR LIMITATION SHALL NOT APPLY TO YOU AND YOU CONTINUE TO BE BOUND BY ALL THE REMAINING DISCLAIMERS, EXCLUSIONS AND LIMITATIONS.</li>
</ol>

<p>d. YOU SPECIFICALLY ACKNOWLEDGE THAT THE LICENSOR SHALL NOT BE LIABLE FOR USER CONTENT OR THE DEFAMATORY, OFFENSIVE, OR ILLEGAL CONDUCT OF ANY THIRD PARTY AND THAT THE RISK OF HARM OR DAMAGE FROM THE FOREGOING RESTS ENTIRELY WITH YOU</p>

<p>e. THE LICENSOR DOES NOT WARRANT, ENDORSE, GUARANTEE, OR ASSUME RESPONSIBILITY FOR ANY PRODUCT OR SERVICE OR AWARDS ADVERTISED OR OFFERED BY YOU OR A THIRD PARTY THROUGH THE SOFTWARE OR ANY HYPERLINKED WEBSITE OR FEATURED IN ANY BANNER OR OTHER ADVERTISING.</p>

<p>&nbsp;</p>

<p><br>
F. USER SHALL DEFEND, INDEMNIFY AND HOLD HARMLESS LICENSOR, ITS AFFILIATES, OFFICERS, DIRECTORS, CONTRACTORS, AGENTS AND EMPLOYEES, FROM ANY AND ALL CLAIMS ARISING OUT OF USE OF THE SOFTWARE BY THE USER OR ANYONE ACTING ON USER'S BEHALF, AND SHALL PAY ANY AND ALL DAMAGES AND EXPENSES (INCLUDING BUT NOT LIMITED TO LEGAL EXPENSES AND ATTORNEYS FEES) RELATED THEREWITH. LICENSOR SHALL HAVE THE RIGHT, AT IT OWN EXPENSE, TO ASSUME EXCLUSIVE CONTROL AND DEFENSE AND CONTROL OF ANY MATTER SUBJECT TO INDEMNIFICATION BY USER, IN WHICH EVENT USER SHALL COOPERATE WITH THE LICENSOR IN ASSERTING ANY AVAILABLE DEFENSES.</p>

<p>7. Compliance with Laws</p>

<p>User will at all times and at its own expense strictly comply with all applicable laws, rules, regulations and governmental orders, now or hereafter in effect, relating to the use of the Software and its performance of this Agreement, including, without limitation, export laws and regulations and maintain in full force and effect all licenses, permits, authorizations, registrations and qualifications from all applicable governmental departments and agencies to the extent necessary to perform its obligations hereunder or the use of the Software.</p>

<p>8. Termination</p>

<p>This agreement shall stay in full force and effect unless terminated by either party at any time for any reason or for no reason. Should you choose to terminate this Agreement, you must send a request to the Licensor at <a href="mailto:info@wheeldo.com">info@wheeldo.com</a>, in such case the Agreement will be terminated on the 1st day of the following month. For avoidance of doubt it is clarified that the User shall continue to pay the Monthly Payment until the Agreement is terminated. Any User’s right to use the software shall automatically terminate should User breach any of the provisions of this agreement.</p>

<p>9. Miscellaneous</p>

<p>Licensor may at any time assign this Agreement to any successor thereof. This Agreement and any dispute arising hereunder shall be construed in accordance with the laws of the State of Israel without regard to principles of conflict of laws. For the purpose of this Agreement, User consents to the exclusive jurisdiction and venue of the state of Israel. No action, regardless of form, arising out of the transactions under this Agreement, or use of the Software by the User may be brought by User (or anyone acting on its behalf) more than one (1) year after the cause of action has occurred, or was discovered to have occurred, except that an action for infringement of intellectual property rights may be brought within the maximum applicable statutory period. If any provision of this Agreement is prohibited by law or held to be unenforceable, the remaining provisions hereof shall not be affected, and this Agreement shall continue in full force and effect as if such unenforceable provision had never constituted a part hereof, and the unenforceable provision shall be automatically amended so it will best accomplish the objectives of such unenforceable provision within the limits of applicable law.</p>

<p>&nbsp;</p>

<p>&nbsp;&nbsp;</p>

<p>&nbsp;</p>




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
        <style>
            .content {
                padding:40px 0px;
                width:100%;
                max-width:1100px;
                margin:0px auto;
                font-size:18px;
                line-height:22px;
                text-align:justify;
            }
        </style>
</html>


