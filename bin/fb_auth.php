<?php

/**
 * Callback URL for Facebook authorization
 */

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;

require_once "top.php";
require_once "fb_init.php";

if(isset($_GET['ref_url']))
	$_SESSION['fb_redirect_url'] = $_GET['ref_url'];

$redirect_url = $_SESSION['fb_redirect_url'] ?: 'http://'.$_SERVER['HTTP_HOST'].'/';

if(!isset($_GET['error'])) {
	if($session) {
		// verify the session
		$request = new FacebookRequest( $session, 'GET', '/me?fields=id' );
		$response = $request->execute();
		$graphObject = $response->getGraphObject()->asArray();

		$user_data=$dbop->selectAssocRow("users","WHERE `facebook_uid`='{$graphObject['id']}'");

		if($user_data) {
			// perform login
			setcookie("user_login", "", time()-3600);
			unset($_SESSION['login_user']);

			$user['ID']=$user_data['id'];
			$user['uid']=$user_data['unique_link'];
			$user['name']=$user_data['fname']." ".$user_data['lname'];
			$user['email']=$user_data['email'];
			$user['image']=$user_data['image'];
			$user['ghost']=$user_data['ghost'];

			$_SESSION['login_user']=$user;
			setcookie("user_login", $user['ID'], time()+3600*24*182);
		}
		else {
			$request = new FacebookRequest( $session, 'GET', '/me?fields=id,first_name,last_name,email' );
			$response = $request->execute();
			$graphObject = $response->getGraphObject()->asArray();

			$data['facebook_uid'] = $graphObject['id'];
			$data['fname'] = $graphObject['first_name'];
			$data['lname'] = $graphObject['last_name'];
			$data['email'] = $graphObject['email'];
			$data['password'] = '';
			$data['image'] = "https://graph.facebook.com/{$graphObject['id']}/picture?type=large";
			$data['suppress_output'] = true;

			unset($_POST);
			$_POST['op'] = 'regNewUser';
			$_POST = array_merge($_POST, $data);

			require_once "operators.php";
		}
	}
	else {
		$redirect_url = $helper->getLoginUrl(array('read_stream', 'publish_stream', 'email'));
	}
}

//echo "<pre>[".print_r($_REQUEST, true)."]</pre>";
//echo "<pre>[".print_r($session, true)."]</pre>";
//echo "<pre>[".print_r($redirect_url, true)."]</pre>";
?>
<script>
location = '<?=$redirect_url?>';
</script>
