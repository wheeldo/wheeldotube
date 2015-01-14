<?php
include 'top.php';
header("Content-type: text/html; charset=utf-8");
email::sendMyEmailPlease($_POST['from'],$_POST['email'],$_POST['subject'],$_POST['body']);