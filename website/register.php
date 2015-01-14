<?php

require './classes/dbop.class.php';
require './classes/class.phpmailer.php';

function isLocalMachine() {

	if ( !isset( $_SERVER['SERVER_ADDR'] ) ) {
		return false;
	}

	$server0 = explode( ".", $_SERVER['SERVER_ADDR'] );
	$serverStart = $server0[0];
	if ( $serverStart == "10" || $serverStart == "127" )
		return true;
	else
		return false;
}

function setEmbedImages( $body ) {

	$r = array( );
	$origImageSrc = array( );
	preg_match_all( '/<img[^>]+>/i', $body, $imgTags );

	for ( $i = 0; $i < count( $imgTags[0] ); $i++ ) {
		preg_match( '/src="([^"]+)/i', $imgTags[0][$i], $imgage );
		$ex = explode( "/", $imgage[0] );
		$newName = $ex[count( $ex ) - 1];
		$ex2 = explode( ".", $newName );
		$newName2 = $i . time() . "." . $ex2[1];
		$origImageSrc[$newName2] = str_ireplace( 'src="', '', $imgage[0] );
		$body = str_replace( $imgage[0], 'src="cid:' . $newName2, $body );
	}
	$r['attachments'] = array_unique( $origImageSrc );
	$r['html'] = $body;
	return $r;
}

$user_email = '';
$user_name = '';
$user_org = '';

/* var_dump($_POST);
  var_dump($_POST["form_name"]);
  var_dump($_POST["formNames"]); */
if ( empty( $_POST["formKeys"] ) ) {
	/*
	 * Foo, error
	 */
	exit;
} else {
	$_formKeys = $_POST["formKeys"];
}

if ( empty( $_POST["formValues"] ) ) {
	/*
	 * Foo, error
	 */
	exit;
} else {
	$_formValues = $_POST["formValues"];
}

if ( empty( $_POST["fAction"] ) ) {
	$_justContact = false;
} else {
	$_justContact = true;
}


foreach ( $_formKeys as $k => $v ) {
	if ( $v == 'fName' ) {
		$user_name = (empty( $_formValues[$k] )) ? '' : $_formValues[$k];
	}
	if ( $v == 'email' ) {
		$user_email = (empty( $_formValues[$k] )) ? '' : $_formValues[$k];
	}
	if ( $v == 'oname' ) {
		$user_org = (empty( $_formValues[$k] )) ? '' : $_formValues[$k];
	}
}

if ( empty( $user_name ) || empty( $user_email ) || empty( $user_org ) ) {
	/*
	 * Foooo, error
	 */
}

if ( !defined( 'DB_HOST' ) ) {
	define( 'DB_HOST', 'localhost' );
}

if ( isLocalMachine() ) {

	if ( !defined( 'USER' ) ) {
		define( 'USER', 'root' );
	}
	if ( !defined( 'PASSWORD' ) ) {
		define( 'PASSWORD', '' );
	}
	if ( !defined( 'DATABASE' ) ) {
		define( 'DATABASE', 'wheeldo' );
	}
} else {
	if ( !defined( 'USER' ) ) {
		define( 'USER', 'wheeldo_user' );
	}
	if ( !defined( 'PASSWORD' ) ) {
		define( 'PASSWORD', 'wheeldodb2013' );
	}
	if ( !defined( 'DATABASE' ) ) {
		define( 'DATABASE', 'wheeldo_db' );
	}
}

if ( $_justContact ) {
	/*
	 * Send contact email and go along;
	 */
	$_r = fn_contactUs( $user_email, $user_name, $user_org );
	echo json_encode( array( 'true' ) );
	exit;
}


$_r = regUserToSystem( $user_email, $user_name, $user_org );

echo json_encode( $_r );

exit;
/*
 * 
 */

function regUserToSystem( $user_email, $user_name, $user_org ) {

	/* 	$this->doNotRenderHeader = 1;
	  dbop::inst_connect( $host, $user, $password, $db_name ); */
	$dbop = new dbop();

	/*
	 * check if user already exist
	 */
	$p = mysql_query( "SELECT * FROM `users` WHERE `userEmail`='$user_email' AND `userUserKindID`>1 " );
	$n = mysql_num_rows( $p );

	if ( $n > 0 ) {
		return array( 'state' => false, 'msg' => 'This user already exist.', 'action' => 'sayHey' );
	}
	/*
	 * create organizations
	 */
	mysql_query( "INSERT INTO `organizations` SET `organizationID`=NULL, `organizationName`='{$user_org}'" );
	$org_id = mysql_insert_id();
	/*
	 * create user
	 */
	$newPass = substr( sha1( time() ), 0, 8 );
	$hashedPass = hash( 'SHA256', $newPass, false );

	mysql_query( "INSERT INTO `users` SET
				`userID`=NULL,
				`userName`='{$user_name}',
				`userPassword`='{$hashedPass}',
				`userEmail`='{$user_email}',
				`userOrganizationID`='{$org_id}',
				`userOrganizationIdSelect`='{$org_id}',
				`userUserKindID`=2
				" );

	$html = file_get_contents( "Emails/reg_user.html" );
	$parameters = array( );
	$parameters['name'] = ucfirst( $user_name );
	$parameters['user_name'] = $user_email;
	$parameters['password'] = $newPass;
	$parameters['link'] = "http://my.wheeldo.com/?u=" . $user_email;
	foreach ( $parameters as $key => $value ):
		$html = str_replace( "[" . $key . "]", $value, $html );
	endforeach;
	/*
	 * send email to the user
	 */
	fn_sendEmail( array( $user_email => $user_name ), '', "Welcome", $html );
	/*
	 * send email to managers
	 */
	$cc = array( );
	$cc['aviad@wheeldo.com'] = "Aviad";
	$cc['irad@wheeldo.com'] = "Irad";

	$parameters = array( );
	$parameters['user_name'] = $user_name;
	$parameters['user_email'] = $user_email;
	$parameters['user_company'] = $user_org;

	$html = file_get_contents( "Emails/reg_managers.html" );

	foreach ( $parameters as $key => $value ):
		$html = str_replace( "[" . $key . "]", $value, $html );
	endforeach;

	return fn_sendEmail( $cc, '', "New registration", $html );
}

/*
 * 
 */

function fn_sendEmail( $_to, $_from, $_subject, $_message ) {
        
	$result = array( );
	/*
	 * Create a new PHPMailer instance
	 */
	$mail = new PHPMailer();
	/*
	 * Set PHPMailer to use the sendmail transport
	 */
        $mail->SMTPDebug = 0;
        $mail->IsSMTP();
        
        $useGmail=false;
        
        if($useGmail) {
            ////////////////////////////////// gmail ////////////////////////////

            $mail->Host = "smtp.gmail.com";

            $mail->Port = 465; 

            $mail->SMTPAuth = true;  // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail

            $mail->Username = "noreply@wheeldo.com";  
            $mail->Password = "team@wheeldo";           

            /////////////////////////////////////////////////////////////////////
        }
        else {
            ////////////////////////////////// turboSMTP ////////////////////////
            $mail->Mailer = "smtp";
            $mail->Host = "pro.turbo-smtp.com";
            //Enter your SMTP2GO account's SMTP server.

            $mail->Port = "465";
            // 8025, 587 and 25 can also be used. Use Port 465 for SSL.

            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            // Uncomment this line if you want to use SSL.

            $mail->Username = "aviad@wheeldo.com";
            $mail->Password = "rGR9AFMT";


            ////////////////////////////////////////////////////////////////////////
        }
	/*
	 * Set who the message is to be sent from
	 */
	$mail->From = "cto@wheeldo.com";
	$mail->FromName = "Wheeldo system";
	/*
	 * Set an alternative reply-to address
	 */

	/*
	 * Set who the message is to be sent to
	 * 
	 */

	foreach ( $_to as $user_email => $user_name ) {

		$mail->AddAddress( $user_email, $user_name );
	}

	$mail->AddReplyTo( 'cto@wheeldo.com', 'Wheeldo system' );

	$mail->IsHTML( true );
	$mail->Subject = $_subject;

	if ( true ) {
		$embedImages = setEmbedImages( $_message );


		foreach ( $embedImages['attachments'] as $new_name => $src ):
			$mail->AddEmbeddedImage( $src, $new_name, $src );
		endforeach;

		$_message = $embedImages['html'];
	}

	$mail->Body = $_message;
	$mail->WordWrap = 50;
	$mail->CharSet = "UTF-8";


        // to be removed
        //return array( 'state' => true, 'msg' => "<h3>Thanks for signing up!</h3>Please check your email and click <strong>Activate Your Account</strong> in the email we just sent you." );
        
	if ( !$mail->Send() ) {
		/* echo 'Message was not sent.<br>Mailer error: ' . $mail->ErrorInfo; */
		$result = array( 'state' => false, 'msg' => 'Mailer Error: ' . $mail->ErrorInfo );
	} else {
		/* $result = array( 'state' => true, 'msg' => "Thank you for signing for wheeldo, an email with your details was sent to you." ); */
		$result = array( 'state' => true, 'msg' => "<h3>Thanks for signing up!</h3>Please check your email and click <strong>Activate Your Account</strong> in the email we just sent you." );
	}
        
        
        

	return $result;
}

/*
 * 
 */

function fn_contactUs( $user_email, $user_name, $user_org ) {

	$newPass = substr( sha1( time() ), 0, 8 );
	$hashedPass = hash( 'SHA256', $newPass, false );
	/*
	 * send email to managers
	 */
	$cc = array( );
	$cc['aviad@wheeldo.com'] = "Aviad";
	$cc['irad@wheeldo.com'] = "Irad";

	$parameters = array( );
	$parameters['user_name'] = $user_name;
	$parameters['user_email'] = $user_email;
	$parameters['user_company'] = $user_org;

	$html = file_get_contents( "Emails/contact_us.html" );

	foreach ( $parameters as $key => $value ) {
		$html = str_replace( "[" . $key . "]", $value, $html );
	}

	return fn_sendEmail( $cc, '', "Contact Us from Wheeldo site", $html );
}

