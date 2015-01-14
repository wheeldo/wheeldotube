<?php

$subject = 'Hi Bouba!';
$to = '<valentin@wheeldo.com>';
$body = 'This is a fsck email sent using PHP';
if ( mail( $to, $subject, $body ) ) {
	echo 'Mail sent successfully!';
} else {
	echo 'Mail not sent!';
}
	