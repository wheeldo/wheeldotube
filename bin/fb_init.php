<?php

$data = file_get_contents("php://input");
$request=json_decode($data,true);

// include required files form Facebook SDK
$ds = DIRECTORY_SEPARATOR;
$base_dir = str_replace($ds . 'bin', $ds . 'vendor', dirname(__FILE__)) . "{$ds}facebook-php-sdk-v4-without-curl{$ds}src{$ds}";

// added in v4.0.5
//require_once($base_dir.'Facebook'.$ds.'FacebookHttpable.php' );
//require_once($base_dir.'Facebook'.$ds.'FacebookCurl.php' );
//require_once($base_dir.'Facebook'.$ds.'FacebookCurlHttpClient.php' );

// added in v4.0.0
require_once ($base_dir . 'Facebook' . $ds . 'FacebookSession.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookJavaScriptLoginHelper.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookRedirectLoginHelper.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookRequest.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookResponse.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookSDKException.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookRequestException.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookOtherException.php');
require_once ($base_dir . 'Facebook' . $ds . 'FacebookAuthorizationException.php');
require_once ($base_dir . 'Facebook' . $ds . 'GraphObject.php');
require_once ($base_dir . 'Facebook' . $ds . 'GraphSessionInfo.php');

// added in v4.0.5
use Facebook\FacebookHttpable;
use Facebook\FacebookCurl;
use Facebook\FacebookCurlHttpClient;

// added in v4.0.0
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

session_start();

// init app with app id and secret
FacebookSession::setDefaultApplication('1511607775728782', '0f538ec84bcd6e947b332bfbd3ad0a68');

$helper = new FacebookRedirectLoginHelper('http://'.$_SERVER['HTTP_HOST'].'/fb_auth');

if(isset($request['token'])){
	$session = new FacebookSession($request['token']);

	try {
		if (!$session -> validate())
			$session = null;
		else
			$_SESSION['fb_token'] = $request['token'];
	} catch ( Exception $e ) {
		// catch any exceptions
		$session = null;
	}
}
elseif (isset($_SESSION) && isset($_SESSION['fb_token'])) {
	$session = new FacebookSession($_SESSION['fb_token']);

	try {
		if (!$session -> validate())
			$session = null;
	} catch ( Exception $e ) {
		// catch any exceptions
		$session = null;
	}

} else {
	// no session exists

	try {
		$session = $helper -> getSessionFromRedirect();
	} catch( FacebookRequestException $ex ) {
		// When Facebook returns an error
		// handle this better in production code
		// @@@ LOG EXCEPTION
	} catch( Exception $ex ) {
		// When validation fails or other local issues
		// handle this better in production code
		echo '1';
	}
}

/* EOF */
